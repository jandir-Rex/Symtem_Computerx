@extends('layouts.appe')

@section('title', 'Política de Cambios y Devoluciones | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="text-center fw-bold mb-5">Política de Cambios y Devoluciones</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">1. Introducción</h2>
                    <p>En <strong>PARADA GAMER</strong>, nos comprometemos a garantizar la satisfacción de nuestros clientes. Esta política establece las condiciones y procedimientos para realizar cambios y devoluciones de productos adquiridos a través de nuestro sitio web <strong>www.paradagamer.com</strong>.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">2. Productos elegibles para cambio o devolución</h2>
                </div>
                <div class="card-body">
                    <p>Los siguientes productos pueden ser cambiados o devueltos dentro de los plazos establecidos:</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-start border-3 border-success ps-3">
                                <h3 class="h6 text-success">✅ Productos con defectos de fábrica</h3>
                                <p class="mb-0">Productos que presenten fallas o defectos de fabricación desde su entrega.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-3 border-success ps-3">
                                <h3 class="h6 text-success">✅ Productos equivocados</h3>
                                <p class="mb-0">Productos que no correspondan con lo solicitado en la orden de compra.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-3 border-success ps-3">
                                <h3 class="h6 text-success">✅ Productos dañados en el transporte</h3>
                                <p class="mb-0">Productos que lleguen dañados debido al proceso de envío.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-3 border-danger ps-3">
                                <h3 class="h6 text-danger">❌ Productos no elegibles</h3>
                                <ul class="mb-0">
                                    <li>Software, juegos digitales o códigos de activación</li>
                                    <li>Accesorios consumibles (baterías, filtros, etc.)</li>
                                    <li>Productos con daños por mal uso o manipulación</li>
                                    <li>Productos que hayan sido modificados o reparados por terceros</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h2 class="h4 mb-0">3. Plazos para cambios y devoluciones</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">Defectos de fábrica</h3>
                                <p class="mb-0"><strong>15 días calendario</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">Producto equivocado</h3>
                                <p class="mb-0"><strong>7 días calendario</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">Daño en transporte</h3>
                                <p class="mb-0"><strong>24 horas</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">Cambio por otro producto</h3>
                                <p class="mb-0"><strong>7 días calendario</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">4. Requisitos para cambios y devoluciones</h2>
                    <ul>
                        <li>Presentar la <strong>boleta o factura original</strong> de compra</li>
                        <li>El producto debe estar en <strong>estado original</strong> (sin uso, con todos sus accesorios y empaque original)</li>
                        <li>Los sellos de garantía y etiquetas de seguridad deben estar <strong>intactos</strong></li>
                        <li>Notificar la solicitud de cambio o devolución a través de los canales oficiales</li>
                        <li>Para devoluciones por defectos, se debe proporcionar <strong>evidencia fotográfica</strong> del problema</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h2 class="h4 mb-0">5. Proceso de cambio o devolución</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">1</div>
                                <div class="ms-3">
                                    <h4 class="h6">Contactar a soporte</h4>
                                    <p class="mb-0">Comuníquese con nuestro equipo de soporte dentro del plazo establecido.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">2</div>
                                <div class="ms-3">
                                    <h4 class="h6">Enviar documentación</h4>
                                    <p class="mb-0">Proporcione fotos del producto, boleta/factura y descripción del problema.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">3</div>
                                <div class="ms-3">
                                    <h4 class="h6">Evaluación técnica</h4>
                                    <p class="mb-0">Nuestro equipo técnico evaluará la solicitud y determinará la solución.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">4</div>
                                <div class="ms-3">
                                    <h4 class="h6">Coordinar recojo o entrega</h4>
                                    <p class="mb-0">Organizaremos el recojo del producto o la entrega del nuevo producto.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">5</div>
                                <div class="ms-3">
                                    <h4 class="h6">Resolución final</h4>
                                    <p class="mb-0">Procederemos con el cambio, devolución o reparación según corresponda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h4 mb-0">6. Opciones de resolución</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">🔄 Cambio por el mismo producto</h3>
                                <p class="mb-0">Se entregará un producto nuevo idéntico al devuelto.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">🔄 Cambio por otro producto</h3>
                                <p class="mb-0">Se puede cambiar por otro producto de igual o mayor valor (diferencia a pagar).</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">💰 Reembolso</h3>
                                <p class="mb-0">Se devolverá el monto total pagado mediante el mismo método de pago original.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="bg-light p-3 rounded text-center h-100">
                                <h3 class="h6">🔧 Reparación</h3>
                                <p class="mb-0">Para productos con garantía, se realizará la reparación correspondiente.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h2 class="h4 mb-0">7. Costos de envío</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded h-100">
                                <h3 class="h6">Defectos de fábrica o errores nuestros</h3>
                                <p class="mb-0"><strong>GRATIS</strong> - PARADA GAMER asume todos los costos de envío.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded h-100">
                                <h3 class="h6">Cambio por otro producto (sin defectos)</h3>
                                <p class="mb-0"><strong>A cargo del cliente</strong> - El cliente asume los costos de envío de ida y vuelta.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h2 class="h4 mb-0">8. Contacto</h2>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">Para iniciar un proceso de cambio o devolución, contacte a nuestro equipo de soporte:</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="https://wa.me/51912492910?text=Hola,%20quiero%20iniciar%20un%20proceso%20de%20cambio%20o%20devolución..." 
                            target="_blank" 
                            class="btn btn-success">
                            <i class="fab fa-whatsapp me-2"></i> Soporte por WhatsApp
                        </a>
                        <a href="mailto:devoluciones@paradagamer.com" class="btn btn-outline-dark">
                            <i class="fas fa-envelope me-2"></i> devoluciones@paradagamer.com
                        </a>
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