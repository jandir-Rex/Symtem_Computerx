<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Venta; 
use App\Models\Reparacion;
use App\Models\Egreso;
use Illuminate\Support\Facades\Log; 

class ContadorController extends Controller
{
    /**
     * Tasa de IGV en Perú
     */
    private const IGV_FACTOR = 1.18;
    private const IGV_RATE = 0.18; // 18%

    /**
     * Muestra las métricas financieras CONSOLIDADAS para la vista del Contador.
     */
    public function dashboard(Request $request)
    {
        // --- 1. Definición de Periodo ---
        // Si no hay mes/año en la request, usa el mes/año actual
        $mes = $request->get('mes', Carbon::now()->month);
        $anio = $request->get('anio', Carbon::now()->year);
        
        $fechaReporte = Carbon::create($anio, $mes, 1);
        $fechaInicioMes = $fechaReporte->copy()->startOfMonth()->startOfDay();
        $fechaFinMes = $fechaReporte->copy()->endOfMonth()->endOfDay();
        
        $fechaInicioAnio = Carbon::create($anio, 1, 1)->startOfDay();
        // El fin del año fiscal es hasta el mes del reporte o el fin de diciembre
        $fechaFinAnio = Carbon::create($anio, 12, 31)->endOfMonth()->endOfDay();
        
        // ** FIX DIRECTO: Define la variable mesReporte **
        $mesReporte = $fechaReporte->locale('es')->monthName . ' ' . $anio;

        try {
            // =================================================================
            // === CÁLCULO MENSUAL: IGV DÉBITO FISCAL (VENTAS E INGRESOS) ===
            // =================================================================
            
            // A. INGRESOS POR VENTAS DE PRODUCTOS
            $ventasQueryMes = Venta::query()
                ->whereBetween('fecha_pago', [$fechaInicioMes, $fechaFinMes]) 
                ->whereIn('estado_sunat', ['ACEPTADO', 'APROBADO']);
                
            $subtotalVentas = $ventasQueryMes->clone()->sum('subtotal');
            $igvDebitoVentas = $ventasQueryMes->clone()->sum('igv');

            // B. INGRESOS POR SERVICIOS DE REPARACIÓN 
            $reparacionesQueryMes = Reparacion::query()
                ->where('estado', 'entregado')
                ->whereBetween('fecha_entrega_real', [$fechaInicioMes, $fechaFinMes]);
            
            $totalReparaciones = $reparacionesQueryMes->clone()->sum('costo_total');
            
            // CÁLCULO: Base Imponible y IGV de Reparaciones
            $subtotalReparaciones = $totalReparaciones > 0 ? $totalReparaciones / self::IGV_FACTOR : 0;
            $igvDebitoReparaciones = $totalReparaciones - $subtotalReparaciones;

            // CONSOLIDADO IGV DÉBITO
            $subtotalIngresosMes = $subtotalVentas + $subtotalReparaciones;
            $igvDebitoFiscal = $igvDebitoVentas + $igvDebitoReparaciones;
            
            // =================================================================
            // === CÁLCULO MENSUAL: IGV CRÉDITO FISCAL (EGRESOS) ===
            // =================================================================
            
            // C. EGRESOS (Solo Facturas dan Crédito Fiscal)
            $egresosConCreditoQueryMes = Egreso::query()
                ->whereBetween('fecha_emision', [$fechaInicioMes, $fechaFinMes]) 
                ->where('documento_tipo', 'like', '%FACTURA%'); 
            
            $totalCompradoMes = $egresosConCreditoQueryMes->clone()->sum('total');
            
            // CÁLCULO: Base Imponible y Crédito Fiscal
            $subtotalEgresosMes = $totalCompradoMes > 0 ? $totalCompradoMes / self::IGV_FACTOR : 0; 
            $igvCreditoFiscal = $totalCompradoMes - $subtotalEgresosMes; 

            // =================================================================
            // === CÁLCULO ANUAL: RENTA (Base Imponible Acumulada) ===
            // =================================================================
            
            // D. INGRESOS ANUALES NETOS (Base Imponible)
            $ingresosAnualesVentas = Venta::query()
                ->whereBetween('fecha_pago', [$fechaInicioAnio, $fechaFinAnio])
                ->whereIn('estado_sunat', ['ACEPTADO', 'APROBADO'])
                ->sum('subtotal');

            $totalReparacionesAnual = Reparacion::query()
                ->where('estado', 'entregado')
                ->whereBetween('fecha_entrega_real', [$fechaInicioAnio, $fechaFinAnio])
                ->sum('costo_total');
            
            $ingresosAnualesReparacionesNetos = $totalReparacionesAnual > 0 ? $totalReparacionesAnual / self::IGV_FACTOR : 0;

            $ingresosAnualesNetos = $ingresosAnualesVentas + $ingresosAnualesReparacionesNetos;

            // E. EGRESOS ANUALES NETOS (Base Imponible de TODO gasto deducible)
            $egresosAnualesTotales = Egreso::query()
                ->whereBetween('fecha_emision', [$fechaInicioAnio, $fechaFinAnio])
                ->sum('total');
            
            $egresosAnualesNetos = $egresosAnualesTotales > 0 ? $egresosAnualesTotales / self::IGV_FACTOR : 0;
            
            // =================================================================
            // === CÁLCULO DE MÉTRICAS CONSOLIDADAS PARA LA VISTA ===
            // =================================================================

            // Totales Brutos para Tarjetas
            $ingresosTotal = $subtotalIngresosMes + $igvDebitoFiscal; // Total Venta/Servicio (Bruto)
            $egresosTotal = $totalCompradoMes; // Total Compras (Bruto)
            $utilidadBruta = $subtotalIngresosMes - $subtotalEgresosMes; // Base Imponible Neta Mensual
            
            // IGV Fiscales para Tarjetas
            $igvVentas = $igvDebitoFiscal;
            $igvEgresos = $igvCreditoFiscal;
            $igvACargo = $igvDebitoFiscal - $igvCreditoFiscal; // Neto a Pagar (Positivo) o Saldo a Favor (Negativo)

            // Proyección Renta Anual (30% sobre la base imponible acumulada anual)
            $rentaAnualPagar = $ingresosAnualesNetos * 0.30; 

            // =================================================================
            // === 5. Data para Gráfico Anual (Tendencia) ===
            // =================================================================
            $labelsAnuales = [];
            $dataIngresos = [];
            $dataEgresos = [];
            $dataUtilidad = [];

            $start = Carbon::create($anio, 1, 1);
            $end = $fechaReporte->copy(); // Itera hasta el mes reportado (inclusive)

            for ($date = clone $start; $date->lte($end); $date->addMonth()) {
                $labelsAnuales[] = $date->locale('es')->shortMonthName;

                $mesStart = $date->copy()->startOfMonth()->startOfDay();
                $mesEnd = $date->copy()->endOfMonth()->endOfDay();

                // Ingresos Netos Mensuales (Base Imponible)
                $vNetas = Venta::whereBetween('fecha_pago', [$mesStart, $mesEnd])
                    ->whereIn('estado_sunat', ['ACEPTADO', 'APROBADO'])
                    ->sum('subtotal');
                    
                $rTotales = Reparacion::where('estado', 'entregado')
                    ->whereBetween('fecha_entrega_real', [$mesStart, $mesEnd])
                    ->sum('costo_total');
                    
                $iNetos = $vNetas + ($rTotales > 0 ? $rTotales / self::IGV_FACTOR : 0);

                // Egresos Netos Mensuales (Base Imponible)
                $eTotales = Egreso::whereBetween('fecha_emision', [$mesStart, $mesEnd])
                    // Usamos 'documento_tipo' like '%FACTURA%' si solo queremos los deducibles de Renta
                    ->sum('total'); 
                    
                $eNetos = $eTotales > 0 ? $eTotales / self::IGV_FACTOR : 0;

                // Llenar arrays (redondeado para la presentación)
                $dataIngresos[] = round($iNetos, 2);
                $dataEgresos[] = round($eNetos, 2);
                $dataUtilidad[] = round($iNetos - $eNetos, 2);
            }
            // ------------------------------------------------------------------
            
            $data = [
                'mesReporte' => $mesReporte, // FIX: Mes y Año formateado
                
                // Métricas Mensuales
                'utilidadBruta' => round($utilidadBruta, 2),
                'ingresosTotal' => round($ingresosTotal, 2),
                'egresosTotal' => round($egresosTotal, 2),
                
                // IGV
                'igvVentas' => round($igvVentas, 2),
                'igvEgresos' => round($igvEgresos, 2),
                'igvACargo' => round($igvACargo, 2),

                // Renta Anual
                'rentaAnualPagar' => round($rentaAnualPagar, 2),
                
                // Gráfico
                'labelsAnuales' => $labelsAnuales,
                'dataIngresos' => $dataIngresos,
                'dataEgresos' => $dataEgresos,
                'dataUtilidad' => $dataUtilidad,
            ];

            // NOTA: Asumo que el nombre correcto de la vista es 'contador.index' 
            // ya que el error se mostraba en 'contador/index.blade.php'
            return view('contador.index', $data);

        } catch (\Throwable $e) {
            Log::error('Error FATAL en ContadorController Dashboard:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            // Si ocurre un error, aseguramos que todas las variables usadas en la vista existan con valor 0 o []
            $error_data = [
                'mesReporte' => $mesReporte, // FIX: Pasa la variable incluso en el error
                'utilidadBruta' => 0, 'ingresosTotal' => 0, 'egresosTotal' => 0,
                'igvVentas' => 0, 'igvEgresos' => 0, 'igvACargo' => 0,
                'rentaAnualPagar' => 0,
                'labelsAnuales' => [], 'dataIngresos' => [], 'dataEgresos' => [], 'dataUtilidad' => [],
                'error_mensaje' => "Error al procesar la data. Verifique la Base de Datos y las tablas (Ventas, Reparaciones, Egresos). Mensaje: " . $e->getMessage(),
            ];
            // Aseguramos que la vista se cargue con datos seguros
            return view('contador.index', $error_data);
        }
    }
}