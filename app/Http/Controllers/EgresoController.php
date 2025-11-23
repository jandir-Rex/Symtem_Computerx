<?php

namespace App\Http\Controllers;

use App\Models\Egreso;
use App\Models\Proveedor; // Asumimos que existe este modelo
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EgresoController extends Controller
{
    /**
     * Muestra la lista de Egresos (Compras y Gastos).
     */
    public function index(Request $request)
    {
        $egresos = Egreso::with('proveedor')
            ->orderBy('fecha_emision', 'desc')
            ->paginate(15);

        return view('egresos.index', compact('egresos'));
    }

    /**
     * Muestra el formulario para crear un nuevo Egreso.
     */
    public function create()
    {
        // Necesario para el dropdown en el formulario
        $proveedores = Proveedor::orderBy('nombre')->pluck('nombre', 'id');
        $documentos = $this->getDocumentoTipos();
        
        return view('egresos.create', compact('proveedores', 'documentos'));
    }

    /**
     * Almacena un Egreso recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        // Aseguramos que el documento tipo sea mayúsculas (para consistencia con el ContadorController)
        $data['documento_tipo'] = strtoupper($data['documento_tipo']);
        
        Egreso::create($data);

        return redirect()->route('dashboard.egresos.index')->with('success', '✅ Egreso registrado exitosamente. Crédito Fiscal actualizado.');
    }

    /**
     * Muestra el formulario para editar un Egreso.
     */
    public function edit(Egreso $egreso)
    {
        $proveedores = Proveedor::orderBy('nombre')->pluck('nombre', 'id');
        $documentos = $this->getDocumentoTipos();

        return view('egresos.edit', compact('egreso', 'proveedores', 'documentos'));
    }

    /**
     * Actualiza el Egreso especificado en la base de datos.
     */
    public function update(Request $request, Egreso $egreso)
    {
        $data = $request->validate($this->rules());
        
        $data['documento_tipo'] = strtoupper($data['documento_tipo']);

        $egreso->update($data);

        return redirect()->route('admin.egresos.index')->with('success', '🔄 Egreso N° ' . $egreso->id . ' actualizado correctamente.');
    }

    /**
     * Elimina el Egreso de la base de datos.
     */
    public function destroy(Egreso $egreso)
    {
        $egreso->delete();

        return redirect()->route('admin.egresos.index')->with('success', '🗑️ Egreso eliminado. El Crédito Fiscal se ha revertido.');
    }

    /**
     * Reglas de validación para Egreso.
     */
    protected function rules()
    {
        return [
            'documento_tipo' => ['required', 'string', Rule::in($this->getDocumentoTipos())],
            'descripcion' => 'required|string|max:255',
            'total' => 'required|numeric|min:0.01',
            'fecha_emision' => 'required|date',
            'proveedor_id' => 'required|exists:proveedors,id', // 'proveedors' debe ser el nombre de tu tabla
        ];
    }
    
    /**
     * Tipos de documentos de egreso válidos para SUNAT
     */
    protected function getDocumentoTipos()
    {
        return [
            'FACTURA', 
            'BOLETA', 
            'RECIBO POR HONORARIOS', 
            'NOTA DE CRÉDITO (COMPRA)', 
            'RECIBO DE LUZ/AGUA/TLF',
            'OTROS',
        ];
    }
}