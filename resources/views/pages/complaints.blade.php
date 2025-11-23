@extends('layouts.appe')

@section('title', 'Libro de Reclamaciones | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="text-center fw-bold mb-5">Libro de Reclamaciones</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">¿Qué es el Libro de Reclamaciones?</h2>
                    <p>El Libro de Reclamaciones es un documento oficial establecido por la <strong>Ley de Protección al Consumidor del Perú</strong> que permite a los consumidores presentar quejas, reclamos o denuncias sobre los productos o servicios adquiridos en <strong>PARADA GAMER</strong>.</p>
                    <p>Este mecanismo garantiza que su reclamo sea atendido de manera oportuna y conforme a la normativa vigente.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">Datos de la empresa</h2>
                </div>
                <div class="card-body">
                    <p><strong>Razón Social:</strong> HC ASOCIADOS S.A.C.</p>
                    <p><strong>RUC:</strong> 20518968239</p>
                    <p><strong>Dirección Fiscal:</strong> AV. GARCILASO DE LA VEGA NRO. 1348 TDA. 2A-125 – CYBER PLAZA – LIMA – LIMA</p>
                    <p><strong>Teléfono:</strong> +51 912 492 910</p>
                    <p><strong>Correo electrónico:</strong> reclamos@paradagamer.com</p>
                    <p><strong>Horario de atención:</strong> Lunes a viernes de 9:00 AM a 7:00 PM</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">¿Cuándo puede presentar una reclamación?</h2>
                    <p>Puede presentar una reclamación si:</p>
                    <ul class="mb-0">
                        <li>El producto adquirido presenta defectos o no cumple con las características anunciadas</li>
                        <li>El servicio prestado no cumple con los estándares de calidad esperados</li>
                        <li>No se respetaron los plazos de entrega comprometidos</li>
                        <li>Hubo publicidad engañosa o información incorrecta</li>
                        <li>No se respetaron los términos de garantía establecidos</li>
                        <li>Cualquier otra situación que afecte sus derechos como consumidor</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h2 class="h4 mb-0">Plazos para presentar reclamaciones</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border-start border-3 border-primary ps-3">
                                <h3 class="h6 text-primary">Productos con garantía</h3>
                                <p class="mb-0"><strong>30 días calendario</strong> desde la detección del problema.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start border-3 border-success ps-3">
                                <h3 class="h6 text-success">Servicios</h3>
                                <p class="mb-0"><strong>15 días calendario</strong> desde la prestación del servicio.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start border-3 border-warning ps-3">
                                <h3 class="h6 text-warning">Publicidad engañosa</h3>
                                <p class="mb-0"><strong>60 días calendario</strong> desde la contratación del producto/servicio.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h2 class="h4 mb-0">Cómo presentar su reclamación</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded h-100">
                                <h3 class="h6 mb-2">📝 Formulario en línea</h3>
                                <p class="mb-2">Complete nuestro formulario digital y adjunte la documentación requerida.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Presentar reclamación</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded h-100">
                                <h3 class="h6 mb-2">📱 WhatsApp</h3>
                                <p class="mb-2">Envíe su reclamación a través de nuestro canal de WhatsApp oficial.</p>
                                <a href="https://wa.me/51912492910?text=Hola,%20quiero%20presentar%20una%20reclamación..." 
                                   target="_blank" 
                                   class="btn btn-sm btn-success">
                                    <i class="fab fa-whatsapp me-1"></i> Enviar por WhatsApp
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded h-100">
                                <h3 class="h6 mb-2">📧 Correo electrónico</h3>
                                <p class="mb-2">Envíe su reclamación detallada a nuestro correo oficial.</p>
                                <a href="mailto:reclamos@paradagamer.com" class="btn btn-sm btn-outline-dark">
                                    <i class="fas fa-envelope me-1"></i> reclamos@paradagamer.com
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Información que debe incluir en su reclamación</h2>
                    <p>Para que su reclamación sea procesada adecuadamente, debe incluir:</p>
                    <ul class="mb-0">
                        <li><strong>Datos personales:</strong> Nombre completo, DNI, dirección y teléfono de contacto</li>
                        <li><strong>Descripción detallada:</strong> Fecha, producto/servicio, problema específico</li>
                        <li><strong>Documentación de respaldo:</strong> Boleta/factura, fotos del producto, capturas de pantalla</li>
                        <li><strong>Solicitud específica:</strong> Qué solución espera (reembolso, cambio, reparación, etc.)</li>
                        <li><strong>Firma del reclamante:</strong> En caso de reclamación física</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h4 mb-0">Tiempos de respuesta</h2>
                </div>
                <div class="card-body">
                    <p>Nos comprometemos a responder su reclamación dentro de los siguientes plazos:</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded text-center h-100 d-flex flex-column justify-content-center">
                                <h3 class="h6 text-primary">Reclamaciones simples</h3>
                                <p class="mb-0"><strong>3 días hábiles</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded text-center h-100 d-flex flex-column justify-content-center">
                                <h3 class="h6 text-success">Reclamaciones complejas</h3>
                                <p class="mb-0"><strong>7 días hábiles</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded text-center h-100 d-flex flex-column justify-content-center">
                                <h3 class="h6 text-danger">Reclamaciones ante INDECOPI</h3>
                                <p class="mb-0"><strong>15 días hábiles</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Derechos del consumidor</h2>
                    <p>De acuerdo con la Ley de Protección al Consumidor, usted tiene derecho a:</p>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="p-2 bg-light border rounded text-center">Recibir información clara y veraz</div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light border rounded text-center">Recibir productos y servicios de calidad</div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light border rounded text-center">Ser atendido de manera oportuna y respetuosa</div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-light border rounded text-center">Recibir una respuesta justa a su reclamación</div>
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 bg-light border rounded text-center">Presentar denuncias ante INDECOPI si no obtiene respuesta satisfactoria</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h2 class="h4 mb-0">¿No obtuvo respuesta satisfactoria?</h2>
                </div>
                <div class="card-body">
                    <p>Si considera que su reclamación no fue atendida de manera adecuada, puede presentar una denuncia formal ante:</p>
                    <div class="bg-light p-3 rounded">
                        <h3 class="h5">INDECOPI - Instituto Nacional de Defensa de la Competencia y de la Protección de la Propiedad Intelectual</h3>
                        <p><strong>Sitio web:</strong> <a href="https://www.indecopi.gob.pe" target="_blank" class="text-decoration-none">www.indecopi.gob.pe</a></p>
                        <p><strong>Teléfono:</strong> 224-1414</p>
                        <p><strong>Oficinas:</strong> Av. Larco 1047, Miraflores - Lima</p>
                    </div>
                </div>
            </div>

            <div class="text-center text-muted mt-4 pt-3 border-top">
                <p class="mb-0"><strong>Última actualización:</strong> {{ date('d/m/Y') }}</p>
                <p class="mb-0">© {{ date('Y') }} PARADA GAMER - HC ASOCIADOS S.A.C. - Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</div>
@endsection