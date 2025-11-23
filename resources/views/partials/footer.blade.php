<footer class="bg-dark text-light pt-5 pb-3"> <!-- Solo padding arriba -->
    <div class="container">
        <div class="row g-4">
            <!-- Contáctanos -->
            <div class="col-md-3">
                <h5 class="text-uppercase fw-bold mb-4">Contáctanos</h5>
                <p><strong>Central Telefónica:</strong><br>969 912 190</p>
                <p><strong>Correo electrónico:</strong><br>djpoolk@gmail.com</p>
                <p><strong>Horario de atención:</strong><br>De lunes a sábado de 9:30 am a 8:00 pm</p>
            </div>

            <!-- Sobre Company Computer -->
            <div class="col-md-3">
                <h5 class="text-uppercase fw-bold mb-4">Sobre Company Computer</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('stores') }}" class="text-light text-decoration-none">Nuestras Tiendas</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}" class="text-light text-decoration-none">Contacto</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-light text-decoration-none">Acerca de Nosotros</a></li>
                </ul>
            </div>

            <!-- Políticas y Ayuda -->
            <div class="col-md-3">
                <h5 class="text-uppercase fw-bold mb-4">Políticas y Ayuda</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('warranty') }}" class="text-light text-decoration-none">Garantía</a></li>
                    <li class="mb-2"><a href="{{ route('terms') }}" class="text-light text-decoration-none">Términos y Condiciones</a></li>
                    <li class="mb-2"><a href="{{ route('privacy') }}" class="text-light text-decoration-none">Políticas de Privacidad</a></li>
                    <li class="mb-2"><a href="{{ route('returns') }}" class="text-light text-decoration-none">Política de cambio y devolución</a></li>
                    <li class="mb-2"><a href="{{ route('complaints') }}" class="text-light text-decoration-none">Libro de reclamaciones</a></li>
                </ul>
            </div>

            <!-- Síguenos en -->
            <div class="col-md-3">
                <h5 class="text-uppercase fw-bold mb-4">Síguenos en</h5>
                <div class="d-flex gap-3">
                    <a href="https://facebook.com/tu-pagina-oficial" target="_blank" rel="noopener" class="text-light fs-4">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://instagram.com/tu-perfil-oficial" target="_blank" rel="noopener" class="text-light fs-4">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://tiktok.com/@tu-perfil-oficial" target="_blank" rel="noopener" class="text-light fs-4">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Métodos de pago -->
        <div class="row mt-4 pt-4 border-top border-secondary">
            <div class="col-12 text-center">
                <div class="d-flex justify-content-center flex-wrap gap-3">
                    <img src="{{ asset('images/bancos/bcp.jpg') }}" alt="BCP" class="img-fluid" style="height: 40px;">
                    <img src="{{ asset('images/bancos/interbank.jpg') }}" alt="Interbank" class="img-fluid" style="height: 40px;">
                    <img src="{{ asset('images/bancos/scotiabank.jpg') }}" alt="Scotiabank" class="img-fluid" style="height: 40px;">
                    <img src="{{ asset('images/bancos/bbva.jpg') }}" alt="BBVA" class="img-fluid" style="height: 40px;">
                    <img src="{{ asset('images/bancos/yape.png') }}" alt="YAPE" class="img-fluid" style="height: 40px;">
                    <img src="{{ asset('images/bancos/plin.png') }}" alt="PLIN" class="img-fluid" style="height: 40px;">
                </div>
            </div>
        </div>

        <!-- Copyright DENTRO del footer (sin margen extra) -->
        <div class="text-center mt-4 pt-3 border-top border-secondary">
            <p class="mb-0">&copy; {{ date('Y') }} Company Computer. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>