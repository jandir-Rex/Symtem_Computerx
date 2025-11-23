@extends('layouts.appe')

@section('title', 'Contáctanos | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="text-center fw-bold mb-5">Asesores Comerciales</h1>

            <div class="row g-4">
                <!-- Asesor 1 -->
                <div class="col-md-4">
                    <div class="card shadow-sm text-center h-100">
                        <img src="{{ asset('images/asesores/asesor1.png') }}" 
                            class="card-img-top" 
                            alt="Asesor Ventas 1"
                            style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title">VENTAS 1</h3>
                            <p class="text-muted">ASESOR DE VENTA</p>
                            <p class="text-muted">Email: djpoolk@gmail.com</p>
                            <a href="https://wa.me/51969912190?text=Hola,%20quiero%20información%20sobre..." 
                                target="_blank" 
                                class="btn btn-success mt-auto">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Asesor 2 -->
                <div class="col-md-4">
                    <div class="card shadow-sm text-center h-100">
                        <img src="{{ asset('images/asesores/asesor2.png') }}" 
                            class="card-img-top" 
                            alt="Asesor Ventas 2"
                            style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title">VENTAS 2</h3>
                            <p class="text-muted">ASESORA DE VENTA</p>
                            <p class="text-muted">Email: djpoolk@gmail.com</p>
                            <a href="https://wa.me/51969912190?text=Hola,%20quiero%20información%20sobre..." 
                                target="_blank" 
                                class="btn btn-success mt-auto">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Asesor Técnico -->
                <div class="col-md-4">
                    <div class="card shadow-sm text-center h-100">
                        <img src="{{ asset('images/asesores/tecnico.png') }}" 
                            class="card-img-top" 
                            alt="Asesor Técnico"
                            style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title">TÉCNICO</h3>
                            <p class="text-muted">SOPORTE TÉCNICO</p>
                            <p class="text-muted">Email: djpoolk@gmail.com</p>
                            <a href="https://wa.me/51969912190?text=Hola,%20tengo%20un%20problema%20técnico%20con%20mi%20producto..." 
                                target="_blank" 
                                class="btn btn-success mt-auto">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection