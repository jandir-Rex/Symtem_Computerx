<?php

namespace App\Http\Controllers\Stand2;

use App\Http\Controllers\Controller;
use App\Models\Reparacion;
use App\Models\Stand;
use Illuminate\Http\Request;
use App\Services\PdfReparacionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReparacionController extends Controller
{
    // LISTAR REPARACIONES
    public function index()
    {
        $reparaciones = Reparacion::latest()->paginate(15);
        return view('stands.stand2.reparaciones.index', compact('reparaciones'));
    }

    // FORM CREAR
    public function create()
    {
        return view('stands.stand2.reparaciones.create');
    }

    // GUARDAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:100',
            'cliente_telefono' => 'nullable|string|max:20',
            'tipo_equipo' => 'required|string|max:50',
            'marca' => 'nullable|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'numero_serie' => 'nullable|string|max:100',
            'problema_reportado' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'solucion_aplicada' => 'nullable|string',
            'estado' => 'required|in:recibido,diagnosticando,en_reparacion,esperando_repuestos,listo,entregado,cancelado',
            'prioridad' => 'nullable|in:baja,normal,alta,urgente',
            'costo_mano_obra' => 'nullable|numeric',
            'costo_repuestos' => 'nullable|numeric',
            'fecha_ingreso' => 'nullable|date',
            'fecha_estimada_entrega' => 'nullable|date',
            'fecha_entrega_real' => 'nullable|date',
            'notas_internas' => 'nullable|string',
            'calificacion' => 'nullable|integer|min:1|max:5',
        ]);

        $validated['costo_total'] = ($validated['costo_mano_obra'] ?? 0) + ($validated['costo_repuestos'] ?? 0);
        $validated['stand_id'] = 2;

        Reparacion::create($validated);

        return redirect()->route('stands.stand2.reparaciones.index')
            ->with('success', 'Reparación registrada correctamente.');
    }

    // EDITAR
    public function edit(Reparacion $reparacion)
    {
        return view('stands.stand2.reparaciones.edit', compact('reparacion'));
    }

    // ACTUALIZAR
    public function update(Request $request, Reparacion $reparacion)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:100',
            'cliente_telefono' => 'nullable|string|max:20',
            'tipo_equipo' => 'required|string|max:50',
            'marca' => 'nullable|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'numero_serie' => 'nullable|string|max:100',
            'problema_reportado' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'solucion_aplicada' => 'nullable|string',
            'estado' => 'required|in:recibido,diagnosticando,en_reparacion,esperando_repuestos,listo,entregado,cancelado',
            'prioridad' => 'nullable|in:baja,normal,alta,urgente',
            'costo_mano_obra' => 'nullable|numeric',
            'costo_repuestos' => 'nullable|numeric',
            'fecha_ingreso' => 'nullable|date',
            'fecha_estimada_entrega' => 'nullable|date',
            'fecha_entrega_real' => 'nullable|date',
            'notas_internas' => 'nullable|string',
            'calificacion' => 'nullable|integer|min:1|max:5',
        ]);

        $validated['costo_total'] = ($validated['costo_mano_obra'] ?? 0) + ($validated['costo_repuestos'] ?? 0);

        if ($validated['estado'] === 'entregado' && !$reparacion->fecha_entrega_real) {
            $validated['fecha_entrega_real'] = now();
        }

        $reparacion->update($validated);

        if ($validated['estado'] === 'entregado') {
            session(['ingresos_reparaciones_' . now()->toDateString() =>
                (session('ingresos_reparaciones_' . now()->toDateString(), 0) + $reparacion->costo_total)
            ]);
        }

        return redirect()->route('stands.stand2.reparaciones.index')
            ->with('success', 'Reparación actualizada correctamente.');
    }

    // ELIMINAR (SOFT DELETE)
    public function destroy(Reparacion $reparacion)
    {
        $reparacion->delete();
        return redirect()->route('stands.stand2.reparaciones.index')
            ->with('success', 'Reparación eliminada correctamente.');
    }

    // MOSTRAR DETALLE
    public function show(Reparacion $reparacion)
    {
        return view('stands.stand2.reparaciones.show', compact('reparacion'));
    }

    // =======================================================================
    // === NUEVOS MÉTODOS: PDF y WHATSAPP ===
    // =======================================================================

    /**
     * 📥 Descarga directa del comprobante PDF de reparación
     */
    public function descargarComprobante($id, PdfReparacionService $pdfService)
    {
        $reparacion = Reparacion::findOrFail($id);

        $filePath = $pdfService->generarYGuardar($reparacion);

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, "Archivo PDF no encontrado.");
        }

        return Storage::disk('public')->download($filePath, basename($filePath));
    }

    /**
     * 🔗 Genera el PDF y devuelve la URL para WhatsApp
     */
    public function generarComprobanteYEnviar($id, PdfReparacionService $pdfService)
    {
        try {
            $reparacion = Reparacion::findOrFail($id);

            if (empty($reparacion->cliente_telefono)) {
                return response()->json([
                    'success' => false,
                    'error' => 'La reparación no tiene un número de teléfono registrado.'
                ], 422);
            }

            $filePath = $pdfService->generarYGuardar($reparacion);

            if (!Storage::disk('public')->exists($filePath)) {
                throw new \Exception("Error al guardar el PDF en el servidor.");
            }

            $pdfUrl = Storage::disk('public')->url($filePath);

            return response()->json([
                'success' => true,
                'url' => url($pdfUrl),
                'celular' => $reparacion->cliente_telefono,
                'filename' => basename($filePath),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de reparación: ' . $e->getMessage(), ['reparacion_id' => $id]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🆕 DETALLE DE REPARACIÓN PARA MODAL (AJAX)
     * (CAMBIO SOLICITADO)
     */
    public function detalleModal($id)
    {
        $reparacion = Reparacion::where('stand_id', 2)
                                ->findOrFail($id);

        return view('stands.stand2.reparaciones.detalle-modal', compact('reparacion'));
    }
}
