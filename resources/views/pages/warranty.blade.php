@extends('layouts.appe')

@section('title', 'Garantías | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="text-center fw-bold mb-5">Garantías</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Política de Garantía</h2>
                    <p>En <strong>Parada Gamer</strong>, nos comprometemos a brindar productos de la más alta calidad y garantía. Todos nuestros productos cuentan con garantía oficial del fabricante y/o garantía de tienda según corresponda.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">Tipos de Garantía</h2>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border-start border-3 border-success ps-3">
                                <h3 class="h5 text-success">✅ Garantía de Fábrica</h3>
                                <p>Todos los productos nuevos cuentan con garantía oficial del fabricante, la cual varía según el producto:</p>
                                <ul class="mb-0">
                                    <li><strong>PCs y Laptops:</strong> 12 meses</li>
                                    <li><strong>Consolas:</strong> 12 meses</li>
                                    <li><strong>Componentes:</strong> 24 meses</li>
                                    <li><strong>Accesorios:</strong> 6 meses</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-3 border-info ps-3">
                                <h3 class="h5 text-info">🛡️ Garantía de Tienda</h3>
                                <p>Adicionalmente, ofrecemos garantía de tienda para productos que no tengan garantía de fábrica o para casos especiales:</p>
                                <ul class="mb-0">
                                    <li>Soporte técnico gratuito durante el período de garantía</li>
                                    <li>Reemplazo inmediato por defectos de fábrica</li>
                                    <li>Asistencia personalizada para configuración inicial</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h4 mb-0">¿Qué cubre la garantía?</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded h-100">
                                <h3 class="h5 text-success">✅ Cubierto</h3>
                                <ul class="mb-0">
                                    <li>Defectos de fabricación</li>
                                    <li>Fallas de hardware</li>
                                    <li>Problemas de funcionamiento</li>
                                    <li>Componentes defectuosos</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded h-100">
                                <h3 class="h5 text-danger">❌ No cubierto</h3>
                                <ul class="mb-0">
                                    <li>Daños por mal uso</li>
                                    <li>Daños por líquidos</li>
                                    <li>Modificaciones no autorizadas</li>
                                    <li>Desgaste normal</li>
                                    <li>Accesorios consumibles</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h2 class="h4 mb-0">¿Cómo hacer uso de la garantía?</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">1</div>
                                <div class="ms-3">
                                    <h4 class="h6">Contacta a nuestro soporte</h4>
                                    <p class="mb-0">Comunícate con nuestro equipo de soporte técnico a través de WhatsApp o correo electrónico.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">2</div>
                                <div class="ms-3">
                                    <h4 class="h6">Presenta tu comprobante</h4>
                                    <p class="mb-0">Envía una foto de tu boleta o factura de compra y el producto con el problema.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">3</div>
                                <div class="ms-3">
                                    <h4 class="h6">Diagnóstico y solución</h4>
                                    <p class="mb-0">Nuestro equipo técnico evaluará el caso y te informará la solución correspondiente.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">4</div>
                                <div class="ms-3">
                                    <h4 class="h6">Reparación o reemplazo</h4>
                                    <p class="mb-0">Procederemos con la reparación o reemplazo del producto según corresponda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h2 class="h4 mb-0">¿Necesitas asistencia con tu garantía?</h2>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">Nuestro equipo de soporte técnico está listo para ayudarte.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="https://wa.me/51912492910?text=Hola,%20necesito%20asistencia%20con%20la%20garant%C3%ADa%20de%20mi%20producto..." 
                            target="_blank" 
                            class="btn btn-success">
                            <i class="fab fa-whatsapp me-2"></i> Soporte por WhatsApp
                        </a>
                        <a href="mailto:garantias@paradagamer.com" class="btn btn-outline-dark">
                            <i class="fas fa-envelope me-2"></i> garantias@paradagamer.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection