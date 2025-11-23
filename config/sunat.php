<?php

return [
    'ruc' => env('SUNAT_RUC'),
    'razon_social' => env('SUNAT_RAZON_SOCIAL'),
    'nombre_comercial' => env('SUNAT_NOMBRE_COMERCIAL'),

    // Certificado digital
    'certificado' => storage_path('sunat/certificado.pem'),
    'usuario_sol' => env('SUNAT_USUARIO_SOL'),
    'clave_sol' => env('SUNAT_CLAVE_SOL'),

    // Entornos SUNAT
    'endpoint_beta' => 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService',
    'endpoint_produccion' => 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService',
];
