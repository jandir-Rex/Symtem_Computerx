<?php

namespace App\Services;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfComprobanteService
{
    public function generarYGuardar(Venta $venta): string
    {
        // ✅ Refrescar modelo para obtener datos actualizados
        $venta->refresh();
        $venta->load(['cliente', 'detalles.producto']);

        // 🔥 EXTRAER DATOS DEL METADATA (para ventas ecommerce/Stripe)
        $metadata = $venta->metadata ?? [];
        
        Log::info('📄 Generando PDF', [
            'venta_id' => $venta->id,
            'tiene_metadata' => !empty($metadata),
            'metadata_keys' => is_array($metadata) ? array_keys($metadata) : [],
        ]);

        // ✅ Si no tiene número de comprobante, generar uno temporal
        if (empty($venta->numero_comprobante)) {
            $prefijo = $venta->tipo_comprobante === 'FACTURA' ? 'F' : 'B';
            $numeroTemporal = $prefijo . '001-' . str_pad($venta->id, 8, '0', STR_PAD_LEFT);
            
            // Actualizar la venta con el número temporal
            $venta->update(['numero_comprobante' => $numeroTemporal]);
            
            Log::info("Número de comprobante temporal generado: {$numeroTemporal}");
        }

        $tipoComprobante = strtolower($venta->tipo_comprobante);
        $numeroLimpio = str_replace(['F', 'B', '-'], '', $venta->numero_comprobante);
        $filename = "{$tipoComprobante}_{$numeroLimpio}.pdf";
        $filePath = "comprobantes/{$filename}";

        // 🔥 COMENTADO: No verificar si existe, siempre regenerar para tener datos actualizados
        // if (Storage::disk('public')->exists($filePath)) {
        //     Log::info("PDF ya existe: {$filePath}");
        //     return $filePath;
        // }

        try {
            // 🔥 PREPARAR DATOS DEL CLIENTE (priorizar metadata)
            $clienteData = $this->obtenerDatosCliente($venta, $metadata);
            
            // 🔥 PREPARAR DATOS DE PRODUCTOS (priorizar metadata)
            $detallesData = $this->obtenerDatosProductos($venta, $metadata);
            
            Log::info('📦 Datos para PDF', [
                'cliente_nombre' => $clienteData['nombre'],
                'productos_count' => count($detallesData),
                'productos' => array_map(fn($d) => $d->producto->nombre, $detallesData),
            ]);

            $empresa = $this->crearEmpresaMock();
            $hash_qr = $this->generarHashQR($venta, $empresa);

            $pdf = Pdf::loadView('comprobantes.pdf', [
                'venta' => $venta,
                'cliente' => (object) $clienteData,
                'detalles' => $detallesData,
                'empresa' => $empresa,
                'hash_qr' => $hash_qr,
            ]);

            $pdf->setPaper('a4', 'portrait');
            
            $pdfContent = $pdf->output();
            Storage::disk('public')->put($filePath, $pdfContent);

            Log::info("✅ PDF generado correctamente: {$filePath}");
            
            return $filePath;

        } catch (\Throwable $e) {
            Log::error('❌ Error al generar PDF', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'venta_id' => $venta->id,
            ]);
            throw new \Exception('Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 Obtener datos del cliente (priorizar metadata)
     */
    protected function obtenerDatosCliente(Venta $venta, array $metadata): array
    {
        // Intentar desde metadata primero (ventas ecommerce/Stripe)
        if (isset($metadata['customer_data'])) {
            $customerData = $metadata['customer_data'];
            
            return [
                'nombre' => $customerData['name'] ?? 'Cliente',
                'documento' => $customerData['dni'] ?? '',
                'email' => $customerData['email'] ?? '',
                'telefono' => $customerData['phone'] ?? '',
                'direccion' => implode(', ', array_filter([
                    $customerData['address'] ?? '',
                    $customerData['district'] ?? '',
                    $customerData['city'] ?? '',
                ])),
            ];
        }

        // Fallback: usar datos del modelo Cliente
        $cliente = $venta->cliente;
        
        return [
            'nombre' => $cliente->nombre ?? 'Cliente General',
            'documento' => $cliente->documento ?? '',
            'email' => $cliente->email ?? '',
            'telefono' => $cliente->telefono ?? '',
            'direccion' => $cliente->direccion ?? '',
        ];
    }

    /**
     * 🔥 Obtener datos de productos (priorizar metadata)
     */
    protected function obtenerDatosProductos(Venta $venta, array $metadata): array
    {
        $productos = [];

        // Intentar desde metadata primero (cart_data contiene los productos)
        if (isset($metadata['cart_data']) && is_array($metadata['cart_data'])) {
            foreach ($metadata['cart_data'] as $item) {
                $cantidad = $item['quantity'] ?? ($item['qty'] ?? 1);
                $precioUnitario = floatval($item['price'] ?? 0);
                
                $productos[] = (object) [
                    'cantidad' => $cantidad,
                    'producto' => (object) [
                        'nombre' => $item['name'] ?? 'Producto',
                    ],
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $precioUnitario * $cantidad,
                ];
            }
            
            Log::info('✅ Productos obtenidos desde metadata', [
                'count' => count($productos)
            ]);
            
            return $productos;
        }

        // Fallback: usar detalles de la venta
        foreach ($venta->detalles as $detalle) {
            $productos[] = (object) [
                'cantidad' => $detalle->cantidad,
                'producto' => (object) [
                    'nombre' => $detalle->producto->nombre ?? 'Producto',
                ],
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal' => $detalle->subtotal,
            ];
        }

        Log::info('📦 Productos obtenidos desde detalles_venta', [
            'count' => count($productos)
        ]);

        return $productos;
    }

    protected function crearEmpresaMock()
    {
        return new class {
            public function getRuc() {
                return '20123456789';
            }
            
            public function getRazonSocial() {
                return 'COMPANY COMPUTER S.A.C.';
            }
            
            public function getAddress() {
                return new class {
                    public function getDireccion() {
                        return 'Av. Principal 123, Lima - Perú';
                    }
                    
                    public function getDistrito() {
                        return 'Lima';
                    }
                    
                    public function getDepartamento() {
                        return 'Lima';
                    }
                };
            }
        };
    }

    protected function generarHashQR(Venta $venta, $empresa): string
    {
        $ruc = $empresa->getRuc();
        $tipoDoc = $venta->tipo_comprobante === 'FACTURA' ? '01' : '03';
        
        $partes = explode('-', $venta->numero_comprobante);
        $serie = $partes[0] ?? 'B001';
        $numero = $partes[1] ?? str_pad($venta->id, 8, '0', STR_PAD_LEFT);
        
        // 🔥 CALCULAR TOTALES CORRECTOS
        $total = floatval($venta->total);
        $subtotal = round($total / 1.18, 2);
        $igv = round($total - $subtotal, 2);
        
        $fechaEmision = $venta->created_at->format('Y-m-d');
        
        // Obtener documento del cliente (priorizar metadata)
        $metadata = $venta->metadata ?? [];
        $docCliente = '';
        
        if (isset($metadata['customer_data']['dni'])) {
            $docCliente = $metadata['customer_data']['dni'];
        } elseif ($venta->cliente) {
            $docCliente = $venta->cliente->documento ?? '';
        }
        
        $tipoDocReceptor = strlen($docCliente) === 11 ? '6' : (strlen($docCliente) === 8 ? '1' : '-');
        
        $hashCPE = $venta->hash_sunat ?? md5($venta->numero_comprobante);
        
        return implode('|', [
            $ruc,
            $tipoDoc,
            $serie,
            $numero,
            number_format($igv, 2, '.', ''),
            number_format($total, 2, '.', ''),
            $fechaEmision,
            $tipoDocReceptor,
            $docCliente,
            $hashCPE
        ]);
    }

    public function eliminar(Venta $venta): bool
    {
        if (empty($venta->numero_comprobante)) {
            return false;
        }

        $tipoComprobante = strtolower($venta->tipo_comprobante);
        $numeroLimpio = str_replace(['F', 'B', '-'], '', $venta->numero_comprobante);
        $filename = "{$tipoComprobante}_{$numeroLimpio}.pdf";
        $filePath = "comprobantes/{$filename}";

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }
}