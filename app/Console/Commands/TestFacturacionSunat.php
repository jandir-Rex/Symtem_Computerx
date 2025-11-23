<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FacturacionSunatService;
use App\Models\Venta;
use Illuminate\Support\Facades\Storage;

class TestFacturacionSunat extends Command
{
    protected $signature = 'sunat:test {venta_id?}';
    protected $description = 'Prueba completa del sistema de facturación electrónica';

    public function handle()
    {
        $this->info("╔══════════════════════════════════════════════════════════╗");
        $this->info("║  TEST DE FACTURACIÓN ELECTRÓNICA SUNAT                   ║");
        $this->info("╚══════════════════════════════════════════════════════════╝\n");

        // 1. Verificar certificado
        $this->info("📋 [1/7] Verificando certificado...");
        $certPath = base_path('certificado.pem');
        if (!file_exists($certPath)) {
            $this->error("✗ Certificado NO encontrado en: {$certPath}");
            return 1;
        }
        $this->line("  ✓ Certificado encontrado");

        // 2. Verificar carpetas
        $this->info("\n📁 [2/7] Verificando carpetas de almacenamiento...");
        $dirs = ['sunat', 'sunat/xml', 'sunat/cdr'];
        foreach ($dirs as $dir) {
            $path = storage_path("app/{$dir}");
            if (!is_dir($path)) {
                $this->error("✗ Carpeta no existe: {$path}");
                return 1;
            }
            $this->line("  ✓ {$dir}/");
        }

        // 3. Verificar venta
        $this->info("\n🛒 [3/7] Buscando venta...");
        $ventaId = $this->argument('venta_id');
        
        if ($ventaId) {
            $venta = Venta::find($ventaId);
        } else {
            $venta = Venta::with(['cliente', 'detalles.producto'])->latest()->first();
        }

        if (!$venta) {
            $this->error("✗ No se encontró ninguna venta");
            return 1;
        }

        $this->line("  ✓ Venta ID: {$venta->id}");
        $this->line("  ✓ Tipo: {$venta->tipo_comprobante}");
        $this->line("  ✓ Total: S/ " . number_format($venta->total, 2));

        // 4. Verificar relaciones
        $this->info("\n🔗 [4/7] Verificando relaciones...");
        
        if (!$venta->cliente) {
            $this->error("✗ La venta no tiene cliente asociado");
            return 1;
        }
        $this->line("  ✓ Cliente: {$venta->cliente->nombre}");
        $this->line("  ✓ Documento: {$venta->cliente->documento}");

        if ($venta->detalles->count() === 0) {
            $this->error("✗ La venta no tiene detalles");
            return 1;
        }
        $this->line("  ✓ Detalles: {$venta->detalles->count()} productos");

        foreach ($venta->detalles as $detalle) {
            if (!$detalle->producto) {
                $this->error("✗ Detalle sin producto asociado (ID: {$detalle->id})");
                return 1;
            }
        }

        // 5. Generar comprobante
        $this->info("\n📤 [5/7] Enviando a SUNAT...");
        $this->line("  → Conectando con servidor de pruebas...");
        
        try {
            $service = app(FacturacionSunatService::class);
            $resultado = $service->generarComprobante($venta);

            if (!$resultado['success']) {
                $this->error("\n✗ ERROR EN SUNAT:");
                $this->error("  {$resultado['error']}");
                return 1;
            }

            $this->line("  ✓ Respuesta recibida");

        } catch (\Exception $e) {
            $this->error("\n✗ EXCEPCIÓN:");
            $this->error("  {$e->getMessage()}");
            $this->error("\n  Archivo: {$e->getFile()}");
            $this->error("  Línea: {$e->getLine()}");
            return 1;
        }

        // 6. Verificar archivos generados
        $this->info("\n📄 [6/7] Verificando archivos generados...");
        
        if (isset($resultado['xml_path']) && Storage::exists($resultado['xml_path'])) {
            $size = Storage::size($resultado['xml_path']);
            $this->line("  ✓ XML: {$resultado['xml_path']} ({$size} bytes)");
        } else {
            $this->warn("  ⚠ XML no generado");
        }

        if (isset($resultado['cdr_path']) && Storage::exists($resultado['cdr_path'])) {
            $size = Storage::size($resultado['cdr_path']);
            $this->line("  ✓ CDR: {$resultado['cdr_path']} ({$size} bytes)");
        } else {
            $this->warn("  ⚠ CDR no generado");
        }

        // 7. Verificar base de datos
        $this->info("\n💾 [7/7] Verificando actualización en BD...");
        $venta->refresh();
        
        $this->line("  ✓ Hash: {$venta->hash_sunat}");
        $this->line("  ✓ Estado: {$venta->estado_sunat}");
        $this->line("  ✓ Mensaje: {$venta->mensaje_sunat}");

        // Resultado final
        $this->info("\n╔══════════════════════════════════════════════════════════╗");
        if ($venta->estado_sunat === 'ACEPTADO') {
            $this->info("║  ✓ FACTURACIÓN EXITOSA - TODO FUNCIONA AL 100%          ║");
        } elseif ($venta->estado_sunat === 'RECHAZADO') {
            $this->warn("║  ⚠ COMPROBANTE RECHAZADO POR SUNAT                      ║");
        } else {
            $this->error("║  ✗ ERROR EN FACTURACIÓN                                 ║");
        }
        $this->info("╚══════════════════════════════════════════════════════════╝");

        // Mostrar resumen JSON
        $this->newLine();
        $this->line("📊 RESUMEN COMPLETO:");
        $this->line(json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return 0;
    }
}