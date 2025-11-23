@extends('layouts.appe')

@section('title', 'Términos y Condiciones | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="text-center fw-bold mb-5">Términos y Condiciones</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">1. Introducción</h2>
                    <p>Los presentes Términos y Condiciones regulan el uso del sitio web <strong>www.paradagamer.com</strong> (en adelante, el "Sitio") y la compra de productos ofrecidos por <strong>PARADA GAMER</strong>, razón social <strong>HC ASOCIADOS S.A.C.</strong>, RUC <strong>20518968239</strong>, con domicilio en AV. GARCILASO DE LA VEGA NRO. 1348 TDA. 2A-125 – CYBER PLAZA – LIMA – LIMA.</p>
                    <p>Al utilizar nuestro Sitio y/o realizar una compra, usted acepta los presentes Términos y Condiciones en su totalidad.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">2. Capacidad para contratar</h2>
                    <p>Para utilizar los servicios de PARADA GAMER, los usuarios deben ser mayores de edad (18 años) y tener capacidad legal para contratar. Los menores de edad solo podrán utilizar el Sitio con la autorización de sus padres, tutores o representantes legales.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">3. Registro de usuario</h2>
                    <p>Para realizar compras en nuestro Sitio, es necesario registrarse creando una cuenta de usuario. El usuario se compromete a:</p>
                    <ul>
                        <li>Proporcionar información veraz, exacta y completa</li>
                        <li>Mantener actualizada su información de registro</li>
                        <li>Proteger la confidencialidad de su contraseña</li>
                        <li>Notificar inmediatamente cualquier uso no autorizado de su cuenta</li>
                    </ul>
                    <p class="mb-0">PARADA GAMER no será responsable por daños derivados del uso no autorizado de la cuenta del usuario.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">4. Productos y precios</h2>
                    <p>Los productos ofrecidos en nuestro Sitio están sujetos a disponibilidad de stock. Nos reservamos el derecho de modificar precios, características y especificaciones de los productos en cualquier momento sin previo aviso.</p>
                    <p class="mb-0">Los precios mostrados incluyen IGV (18%) y están expresados en Soles Peruanos (S/).</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">5. Proceso de compra</h2>
                    <p>El proceso de compra consta de los siguientes pasos:</p>
                    <ol>
                        <li>Selección de productos y agregación al carrito</li>
                        <li>Revisión del carrito y confirmación de datos</li>
                        <li>Selección del método de pago</li>
                        <li>Confirmación de la orden de compra</li>
                    </ol>
                    <p class="mb-0">La orden de compra se considera aceptada una vez que el cliente recibe la confirmación por correo electrónico.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">6. Métodos de pago</h2>
                    <p>Aceptamos los siguientes métodos de pago:</p>
                    <ul>
                        <li>Tarjetas de crédito y débito (Visa, Mastercard, American Express)</li>
                        <li>Transferencias bancarias</li>
                        <li>Pago contra entrega (solo para Lima Metropolitana)</li>
                        <li>Yape y Plin</li>
                    </ul>
                    <p class="mb-0">La transacción se considera completada una vez que se confirma el pago.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">7. Entrega y despacho</h2>
                    <p>Realizamos despachos a nivel nacional con los siguientes plazos estimados:</p>
                    <ul>
                        <li><strong>Lima Metropolitana:</strong> 1-3 días hábiles</li>
                        <li><strong>Provincias:</strong> 3-7 días hábiles</li>
                    </ul>
                    <p class="mb-0">Los costos de envío serán calculados y mostrados antes de la confirmación de la compra. PARADA GAMER no se responsabiliza por retrasos causados por fuerza mayor o por el servicio de mensajería.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">8. Garantía y devoluciones</h2>
                    <p>Todos nuestros productos cuentan con garantía oficial del fabricante. Para mayor información, consulte nuestra <a href="{{ route('warranty') }}" class="text-decoration-none">Política de Garantía</a>.</p>
                    <p class="mb-0">No aceptamos devoluciones por cambio de opinión, salvo que el producto presente defectos de fabricación o no corresponda con lo solicitado.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">9. Propiedad intelectual</h2>
                    <p>Todos los contenidos del Sitio (textos, imágenes, logotipos, diseños, software, etc.) son propiedad exclusiva de PARADA GAMER o de sus licenciantes y están protegidos por las leyes de propiedad intelectual.</p>
                    <p class="mb-0">Queda prohibida la reproducción, distribución, modificación o uso no autorizado de cualquier contenido del Sitio.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">10. Privacidad</h2>
                    <p>La información personal de los usuarios será tratada conforme a nuestra <a href="{{ route('privacy') }}" class="text-decoration-none">Política de Privacidad</a>, la cual forma parte integral de los presentes Términos y Condiciones.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">11. Modificaciones</h2>
                    <p>PARADA GAMER se reserva el derecho de modificar los presentes Términos y Condiciones en cualquier momento. Las modificaciones entrarán en vigencia inmediatamente después de su publicación en el Sitio.</p>
                    <p class="mb-0">El uso continuado del Sitio después de dichas modificaciones constituirá la aceptación de los nuevos términos.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">12. Legislación aplicable y jurisdicción</h2>
                    <p class="mb-0">Los presentes Términos y Condiciones se regirán e interpretarán de acuerdo con las leyes de la República del Perú. Cualquier controversia derivada de su interpretación o ejecución será sometida a los tribunales competentes de la ciudad de Lima.</p>
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