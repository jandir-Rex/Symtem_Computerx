<?php

namespace App\Imports;

use App\Models\Producto;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductosImport
{
    private $importados = 0;
    private $actualizados = 0;
    private $errores = [];

    public function import($filePath)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            
            // Buscar la hoja "Datos"
            $sheet = null;
            foreach ($spreadsheet->getAllSheets() as $worksheet) {
                if ($worksheet->getTitle() === 'Datos') {
                    $sheet = $worksheet;
                    break;
                }
            }
            
            if (!$sheet) {
                $sheet = $spreadsheet->getActiveSheet();
            }
            
            $rows = $sheet->toArray();

            // Obtener y limpiar encabezados
            $headers = array_shift($rows);
            $headers = array_map(function($header) {
                return strtolower(trim(str_replace(['*', ' '], ['', '_'], $header)));
            }, $headers);

            $filaActual = 2;

            foreach ($rows as $row) {
                try {
                    // Saltar filas vacías
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $data = array_combine($headers, $row);

                    // Validar campos obligatorios
                    if (empty($data['nombre']) || !isset($data['precio_compra']) || !isset($data['precio_venta'])) {
                        $this->errores[] = "Fila {$filaActual}: Faltan campos obligatorios";
                        $filaActual++;
                        continue;
                    }

                    // Buscar producto existente
                    $producto = Producto::where('codigo_barras', $data['codigo_barras'] ?? null)
                        ->orWhere('nombre', $data['nombre'])
                        ->first();

                    // Preparar datos
                    $datos = [
                        'nombre' => $data['nombre'],
                        'codigo_barras' => $data['codigo_barras'] ?? null,
                        'precio_compra' => (float) ($data['precio_compra'] ?? 0),
                        'precio_venta' => round((float) ($data['precio_venta'] ?? 0) * 1.18, 2),
                        'stock' => (int) ($data['stock'] ?? 0),
                        'stock_minimo' => (int) ($data['stock_minimo'] ?? 0),
                        'categoria' => $data['categoria'] ?? null,
                        'marca' => $data['marca'] ?? null,
                        'garantia_meses' => (int) ($data['garantia_meses'] ?? 0),
                        'descripcion' => $data['descripcion'] ?? null,
                        'activo' => isset($data['activo']) ? (bool) $data['activo'] : true,
                        'destacado' => isset($data['destacado']) ? (bool) $data['destacado'] : false,
                        'visible_ecommerce' => isset($data['visible_ecommerce']) ? (bool) $data['visible_ecommerce'] : false,
                        'user_id' => 1,
                    ];

                    if ($producto) {
                        $producto->update($datos);
                        $this->actualizados++;
                    } else {
                        Producto::create($datos);
                        $this->importados++;
                    }

                } catch (\Exception $e) {
                    $this->errores[] = "Fila {$filaActual}: " . $e->getMessage();
                    Log::error('Error importando producto', [
                        'fila' => $filaActual,
                        'error' => $e->getMessage()
                    ]);
                }

                $filaActual++;
            }

        } catch (\Exception $e) {
            $this->errores[] = "Error al leer archivo: " . $e->getMessage();
            Log::error('Error procesando Excel', ['error' => $e->getMessage()]);
        }
    }

    public function getImportados()
    {
        return $this->importados;
    }

    public function getActualizados()
    {
        return $this->actualizados;
    }

    public function getErrores()
    {
        return $this->errores;
    }
}