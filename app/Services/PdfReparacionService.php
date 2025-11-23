<?php

namespace App\Services;

use App\Models\Reparacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfReparacionService
{
    /**
     * Generar y guardar el PDF del comprobante de reparación
     */
    public function generarYGuardar(Reparacion $reparacion): string
    {
        // Nombre del archivo
        $filename = 'comprobante_reparacion_' . $reparacion->id . '_' . now()->format('YmdHis') . '.pdf';
        $filePath = 'comprobantes/reparaciones/' . $filename;

        // Generar el PDF (CORREGIDO: usa la ruta correcta)
        $pdf = Pdf::loadView('comprobantes.comprobante-reparacion', compact('reparacion'))
            ->setPaper('a4', 'portrait');

        // Guardar en storage/app/public/comprobantes/reparaciones/
        Storage::disk('public')->put($filePath, $pdf->output());

        return $filePath;
    }
}