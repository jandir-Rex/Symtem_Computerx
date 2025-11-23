<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

$see = new See();

// Certificado PFX con clave
$see->setCertificate(
    file_get_contents(__DIR__.'/certificate.pfx'),
    'password_del_certificado'
);

// SUNAT PRUEBAS
$see->setService(SunatEndpoints::FE_BETA);

// Credenciales SOL PRUEBA
$see->setClaveSOL('20000000001', 'MODDATOS', 'moddatos');

return $see;
