<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="asset-url" content="{{ asset('') }}">

    <!-- SEO -->
    <title>@yield('title', 'Company Computer - Tienda Gamer Perú | Ofertas 2025')</title>
    <meta name="description" content="@yield('description', 'Tienda gamer en Perú con los mejores precios en PC, laptops, componentes y periféricos. Envío gratis a Lima y Trujillo.')">
    @stack('meta')

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Rajdhani:wght@600&display=swap" rel="stylesheet">

    <!-- CSS Global -->
    <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ time() }}">
    
    <!-- CHATBOT + BACK BUTTON STYLES -->
    <style>
        /* BOTÓN FLOTANTE REGRESAR */
        #floatingBackBtn {
            position: fixed;
            top: 80px;
            left: 20px;
            z-index: 1040;
            border-radius: 50px;
            background: linear-gradient(135deg, #dc3545, #ff6b00);
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
            transition: all 0.3s ease;
            padding: 12px 25px;
            font-weight: 600;
            cursor: pointer;
        }
        
        #floatingBackBtn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.6);
        }
        
        #floatingBackBtn:active {
            transform: translateY(-1px) scale(1.02);
        }

        /* CHATBOT FLOATING BUTTON */
        #chatbotToggle {
            animation: pulse 2s infinite;
            transition: transform 0.3s ease;
        }
        #chatbotToggle:hover {
            transform: scale(1.1);
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            50% { box-shadow: 0 0 0 15px rgba(220, 53, 69, 0); }
        }

        /* CHATBOT MODAL CUSTOM */
        #chatbotMessages {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            overflow-y: auto;
            scroll-behavior: smooth;
        }
        #chatbotMessages::-webkit-scrollbar {
            width: 8px;
        }
        #chatbotMessages::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        #chatbotMessages::-webkit-scrollbar-thumb {
            background: #dc3545;
            border-radius: 4px;
        }
        
        /* MENSAJE DEL BOT */
        .bot-message {
            animation: slideInLeft 0.3s ease;
        }
        
        /* MENSAJE DEL USUARIO */
        .user-message {
            animation: slideInRight 0.3s ease;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* TYPING INDICATOR */
        .typing-indicator {
            display: inline-flex;
            gap: 4px;
            padding: 10px;
        }
        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
            30% { transform: translateY(-10px); opacity: 1; }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            #floatingBackBtn {
                width: 50px;
                height: 50px;
                bottom: 90px;
                left: 15px;
            }
            #chatbotToggle {
                width: 60px !important;
                height: 60px !important;
            }
        }
    </style>
    
    @stack('styles')
</head>

<body class="@yield('body-class', 'bg-white text-dark')">
    @include('partials.nav')

    <main class="min-vh-100">
        @yield('content')
    </main>

    @include('partials.footer')

    <!-- ========================================
         BOTÓN FLOTANTE REGRESAR
    ======================================== -->
    @if(!request()->routeIs('home') && !request()->routeIs('cart.index'))
        <button id="floatingBackBtn" 
                onclick="window.history.back()" 
                class="btn text-white"
                title="Regresar">
            <i class="fas fa-arrow-left fs-4"></i>
        </button>
    @endif

    <!-- ========================================
         CHATBOT FLOTANTE
    ======================================== -->
    <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1050;">
        <button class="btn btn-danger rounded-circle shadow-lg border-3 border-white" 
                id="chatbotToggle" 
                style="width: 70px; height: 70px;"
                title="Habla con Beymax">
            <i class="fas fa-robot fa-beat fs-2"></i>
        </button>
    </div>

    <!-- MODAL CHATBOT -->
    <div class="modal fade" id="chatbotModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                
                <!-- HEADER -->
                <div class="modal-header bg-gradient text-white border-0" style="background: linear-gradient(135deg, #ff0000, #ff6b00);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative">
                            <img src="{{ asset('images/beymax.png') }}" 
                                 alt="Beymax" 
                                 width="55" 
                                 class="rounded-circle border border-3 border-white"
                                 onerror="this.src='https://ui-avatars.com/api/?name=Beymax&background=dc3545&color=fff&size=55'">
                            <span class="position-absolute bottom-0 end-0 translate-middle p-2 bg-success border border-2 border-white rounded-circle">
                                <span class="visually-hidden">Online</span>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Beymax</h5>
                            <small class="opacity-75">
                                <i class="fas fa-circle text-success" style="font-size: 8px;"></i> 
                                Asistente Gamer IA
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <!-- MENSAJES -->
                <div class="modal-body" id="chatbotMessages" style="max-height: 500px; min-height: 400px;">
                    <!-- Mensaje de bienvenida -->
                    <div class="text-center text-white-50 small mb-3">
                        <i class="fas fa-shield-alt text-danger"></i> 
                        Conversación cifrada de extremo a extremo
                    </div>
                    
                    <div class="d-flex mb-3 bot-message">
                        <img src="{{ asset('images/beymax.png') }}" 
                             width="40" 
                             height="40"
                             class="rounded-circle me-2 border border-2 border-danger"
                             onerror="this.src='https://ui-avatars.com/api/?name=B&background=dc3545&color=fff&size=40'">
                        <div class="bg-secondary text-white p-3 rounded-3 rounded-start-0 shadow-sm" style="max-width: 80%;">
                            <strong>¡Hola! Soy Beymax 🤖</strong><br>
                            Tu asistente gamer personal de Company Computer.<br><br>
                            ¿Buscas una PC para jugar a 240 FPS? ¿O un monitor 360Hz?<br>
                            ¡Dime qué necesitas y te armo la configuración perfecta! 🎮✨
                        </div>
                    </div>
                </div>

                <!-- INPUT -->
                <div class="modal-footer bg-black border-top border-danger p-3">
                    <div class="w-100">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control form-control-lg bg-dark text-white border-danger" 
                                   id="userMessage" 
                                   placeholder="Escribe tu mensaje..."
                                   maxlength="500"
                                   autocomplete="off"
                                   style="border-radius: 25px 0 0 25px;">
                            <button class="btn btn-danger btn-lg px-4" 
                                    id="sendButton"
                                    style="border-radius: 0 25px 25px 0;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div class="text-white-50 small mt-2 text-end">
                            <span id="charCount">0</span>/500 caracteres
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}?v={{ time() }}"></script>
    @stack('scripts')

    <!-- ========================================
         SCRIPT DEL CARRITO GLOBAL
    ======================================== -->
    <script>
    // FUNCIÓN PARA ACTUALIZAR CONTADOR DEL CARRITO
    function updateCartCount(count = null) {
        const badge = document.getElementById('cart-count');
        if (!badge) return;

        if (count !== null) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        } else {
            fetch('/cart/data', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                const itemCount = data.cart_count || 0;
                badge.textContent = itemCount;
                badge.style.display = itemCount > 0 ? 'inline-block' : 'none';
            })
            .catch(err => console.error('Error al actualizar contador:', err));
        }
    }

    // FUNCIÓN PARA MOSTRAR NOTIFICACIONES
    function showNotification(message, type = 'success') {
        let toast = document.getElementById('globalToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'globalToast';
            toast.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;min-width:300px;';
            document.body.appendChild(toast);
        }

        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

        toast.innerHTML = `
            <div class="toast align-items-center text-white ${bgClass} border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body fw-bold">
                        <i class="fas ${icon} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        setTimeout(() => {
            const toastEl = toast.querySelector('.toast');
            if (toastEl) toastEl.classList.remove('show');
        }, 3000);
    }

    // AÑADIR AL CARRITO
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.add-to-cart');
        if (!btn) return;

        e.preventDefault();

        const productData = {
            id: parseInt(btn.dataset.id), 
            name: btn.dataset.name,
            price: parseFloat(btn.dataset.price),
            quantity: 1,
            image: btn.dataset.image,
            category: btn.dataset.category,
            stock: parseInt(btn.dataset.stock)
        };

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Añadiendo...';

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(productData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                showNotification('✓ Producto añadido al carrito', 'success');
            } else {
                showNotification(data.message || 'Error al añadir', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showNotification('Error al añadir producto', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // ACTUALIZAR CONTADOR AL CARGAR
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
    });
    </script>

    <!-- ========================================
         CHATBOT JAVASCRIPT COMPLETO
    ======================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal('#chatbotModal');
            const toggle = document.getElementById('chatbotToggle');
            const input = document.getElementById('userMessage');
            const send = document.getElementById('sendButton');
            const messages = document.getElementById('chatbotMessages');
            const charCount = document.getElementById('charCount');

            // Abrir modal
            toggle?.addEventListener('click', () => {
                modal.show();
                setTimeout(() => input.focus(), 300);
            });

            // Contador de caracteres
            input?.addEventListener('input', () => {
                charCount.textContent = input.value.length;
            });

            // Función para enviar mensaje
            const sendMessage = () => {
                const text = input.value.trim();
                if (!text) return;

                // Agregar mensaje del usuario
                messages.insertAdjacentHTML('beforeend', `
                    <div class="d-flex justify-content-end mb-3 user-message">
                        <div class="bg-danger text-white p-3 rounded-3 rounded-end-0 shadow-sm" style="max-width: 80%;">
                            ${escapeHtml(text)}
                        </div>
                    </div>
                `);
                
                input.value = '';
                charCount.textContent = '0';
                messages.scrollTop = messages.scrollHeight;

                // Mostrar indicador de escritura
                const typingId = 'typing-' + Date.now();
                messages.insertAdjacentHTML('beforeend', `
                    <div class="d-flex mb-3 bot-message" id="${typingId}">
                        <img src="{{ asset('images/beymax.png') }}" 
                             width="40" 
                             class="rounded-circle me-2"
                             onerror="this.src='https://ui-avatars.com/api/?name=B&background=dc3545&color=fff&size=40'">
                        <div class="bg-secondary text-white p-3 rounded-3 rounded-start-0">
                            <div class="typing-indicator">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                `);
                messages.scrollTop = messages.scrollHeight;

                // Enviar a la API
                fetch('/chatbot', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: text })
                })
                .then(r => r.json())
                .then(data => {
                    // Remover indicador de escritura
                    document.getElementById(typingId)?.remove();

                    // Agregar respuesta del bot
                    messages.insertAdjacentHTML('beforeend', `
                        <div class="d-flex mb-3 bot-message">
                            <img src="{{ asset('images/beymax.png') }}" 
                                 width="40" 
                                 class="rounded-circle me-2 border border-2 border-danger"
                                 onerror="this.src='https://ui-avatars.com/api/?name=B&background=dc3545&color=fff&size=40'">
                            <div class="bg-secondary text-white p-3 rounded-3 rounded-start-0 shadow-sm" style="max-width: 80%;">
                                ${formatBotResponse(data.response || 'No entendí, ¿puedes repetirlo? 🤔')}
                            </div>
                        </div>
                    `);
                    messages.scrollTop = messages.scrollHeight;
                })
                .catch(() => {
                    document.getElementById(typingId)?.remove();
                    
                    messages.insertAdjacentHTML('beforeend', `
                        <div class="d-flex mb-3 bot-message">
                            <img src="{{ asset('images/beymax.png') }}" 
                                 width="40" 
                                 class="rounded-circle me-2"
                                 onerror="this.src='https://ui-avatars.com/api/?name=B&background=dc3545&color=fff&size=40'">
                            <div class="bg-danger text-white p-3 rounded-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error de conexión. Intenta de nuevo.
                            </div>
                        </div>
                    `);
                    messages.scrollTop = messages.scrollHeight;
                });
            };

            // Event listeners
            send?.addEventListener('click', sendMessage);
            input?.addEventListener('keypress', e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Función para escapar HTML
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Función para formatear respuesta del bot
            function formatBotResponse(text) {
                // Convertir saltos de línea a <br>
                text = escapeHtml(text).replace(/\n/g, '<br>');
                
                // Convertir **texto** a negrita
                text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                
                return text;
            }
        });
    </script>

    <!-- GOOGLE ANALYTICS (opcional) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-XXXXXXX');
    </script>

</body>
</html>