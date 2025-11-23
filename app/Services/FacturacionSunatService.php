<?php

namespace App\Services;

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\SaleDetail;
use App\Models\Venta;
use Illuminate\Support\Facades\Storage;

class FacturacionSunatService
{
    protected See $see;

    public function __construct()
    {
        $this->see = new See();
        $this->see->setService(SunatEndpoints::FE_BETA);

        // Certificado .pem en la raíz del proyecto
        $this->see->setCertificate(file_get_contents(base_path('certificado.pem')));

        // Credenciales SOL de prueba
        $this->see->setCredentials('20000000001MODDATOS', 'moddatos');
    }

    protected function getCompany(): Company
    {
        return (new Company())
            ->setRuc('20000000001')
            ->setRazonSocial('EMPRESA DEMO S.A.C.')
            ->setNombreComercial('EMPRESA DEMO')
            ->setAddress(
                (new Address())
                    ->setUbigueo('150101')
                    ->setDepartamento('LIMA')
                    ->setProvincia('LIMA')
                    ->setDistrito('LIMA')
                    ->setUrbanizacion('-')
                    ->setDireccion('Av. Los Ejemplos 123')
            );
    }

    public function generarComprobante(Venta $venta)
    {
        $documento = $venta->cliente->documento ?? '';
        $cliente = new Client();

        if (strlen($documento) === 11) $cliente->setTipoDoc('6'); // RUC
        elseif (strlen($documento) === 8) $cliente->setTipoDoc('1'); // DNI
        else $cliente->setTipoDoc('-');

        $cliente->setNumDoc($documento)
                ->setRznSocial($venta->cliente->nombre);

        // Crear comprobante (FACTURA y BOLETA usan la misma clase Invoice)
        $comprobante = new Invoice();
        
        if ($venta->tipo_comprobante === 'FACTURA') {
            $comprobante->setTipoDoc('01')->setSerie('F001');
        } else {
            // BOLETA también usa Invoice, solo cambia el tipo de documento
            $comprobante->setTipoDoc('03')->setSerie('B001');
        }

        $comprobante->setCorrelativo(str_pad($venta->id, 8, '0', STR_PAD_LEFT))
                    ->setFechaEmision(new \DateTime())
                    ->setTipoMoneda('PEN')
                    ->setClient($cliente)
                    ->setCompany($this->getCompany());

        // Detalles
        $detalles = [];
        foreach ($venta->detalles as $item) {
            $detalles[] = (new SaleDetail())
                ->setCodProducto((string)$item->producto_id)
                ->setUnidad('NIU')
                ->setDescripcion($item->producto->nombre)
                ->setCantidad($item->cantidad)
                ->setMtoValorUnitario($item->precio_unitario)
                ->setMtoValorVenta($item->subtotal)
                ->setMtoBaseIgv($item->subtotal)
                ->setPorcentajeIgv(18)
                ->setIgv(round($item->subtotal * 0.18, 2))
                ->setTipAfeIgv('10')
                ->setMtoPrecioUnitario(round($item->precio_unitario * 1.18, 2));
        }

        $comprobante->setDetails($detalles);

        // Totales
        $total = $venta->total;
        $comprobante->setMtoOperGravadas($total)
                    ->setMtoIGV(round($total * 0.18, 2))
                    ->setTotalImpuestos(round($total * 0.18, 2))
                    ->setValorVenta($total)
                    ->setSubTotal(round($total * 1.18, 2))
                    ->setMtoImpVenta(round($total * 1.18, 2));

        // Enviar a SUNAT
        $result = $this->see->send($comprobante);

        if (!$result->isSuccess()) {
            $venta->update([
                'estado_sunat' => 'ERROR',
                'mensaje_sunat' => $result->getError()->getMessage(),
            ]);

            return ['success' => false, 'error' => $result->getError()->getMessage()];
        }

        // Hash simple: serie-correlativo
        $hash = $comprobante->getSerie() . '-' . $comprobante->getCorrelativo();

        // Guardar en BD
        $venta->update([
            'hash_sunat' => $hash,
            'estado_sunat' => $result->getCdrResponse()->isAccepted() ? 'ACEPTADO' : 'RECHAZADO',
            'mensaje_sunat' => $result->getCdrResponse()->getDescription(),
        ]);

        // Guardar archivos XML y CDR
        $nombreArchivo = $venta->tipo_comprobante . '-' . $venta->id;
        
        // Guardar XML firmado
        Storage::put("sunat/xml/{$nombreArchivo}.xml", $this->see->getFactory()->getLastXml());
        
        // Guardar CDR (Constancia de Recepción)
        Storage::put("sunat/cdr/R-{$nombreArchivo}.zip", $result->getCdrZip());

        return [
            'success' => true,
            'hash' => $hash,
            'estado' => $venta->estado_sunat,
            'mensaje' => $venta->mensaje_sunat,
            'xml_path' => "sunat/xml/{$nombreArchivo}.xml",
            'cdr_path' => "sunat/cdr/R-{$nombreArchivo}.zip"
        ];
    }
}