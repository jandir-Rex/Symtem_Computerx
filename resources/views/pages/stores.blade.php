@extends('layouts.appe')

@section('title', 'Nuestras Tiendas | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="text-center fw-bold mb-4">Company Computer / Tienda Stand6 - 2Piso - Trujillo</h1>

            <!-- Imagen destacada -->
            <div class="mb-4">
                <img src="{{ asset('images/store-hero.jpg') }}" 
                    class="img-fluid rounded shadow" 
                    alt="Tienda CyberPlaza"
                    style="height: 400px; object-fit: cover;">
            </div>

            <!-- Información de la tienda -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Parada Gamer: Expertos en Productos Gamer</h2>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <p><i class="fas fa-mobile-alt me-2 text-primary"></i> <strong>Móvil:</strong> 969 912 190</p>
                            <p><i class="fas fa-envelope me-2 text-primary"></i> <strong>Email:</strong> djpoolk@gmail.com</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-clock me-2 text-primary"></i> <strong>Horario:</strong> Lunes a Sábados 9:30 am - 8:00 pm</p>
                            <p><i class="fas fa-map-marker-alt me-2 text-primary"></i> <strong>Dirección:</strong> AV. ESPAÑA N° 2155 TDA.205 STAND.06 PISO.02 - TRUJILLO</p>
                        </div>
                    </div>

                    <!-- Mapa de Google Maps -->
                    <div class="mt-4">
                        <h3 class="h5 mb-3">Ubicación</h3>
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://maps.app.goo.gl/fdS3E7oauqX3WQuc9" 
                                allowfullscreen 
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection