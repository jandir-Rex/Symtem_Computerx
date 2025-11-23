<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * 🔥 Crear sesión de checkout con metadata optimizado
     */
    public function createCheckoutSession(array $orderData)
    {
        try {
            $lineItems = [];
            
            foreach ($orderData['items'] as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'pen',
                        'unit_amount' => intval($item['price'] * 100),
                        'product_data' => [
                            'name' => $item['name'],
                            'description' => $item['description'] ?? 'Producto Company Computer',
                        ],
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            $successUrl = route('checkout.stripe.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('checkout.stripe.cancel', [], true);

            Log::info('Creando sesión de Stripe', [
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'line_items_count' => count($lineItems),
            ]);

            // 🔥 PREPARAR METADATA OPTIMIZADO (máx 500 chars por campo)
            $metadata = [
                // Datos básicos del cliente (campos individuales)
                'customer_name' => substr($orderData['customer']['name'] ?? '', 0, 100),
                'customer_email' => substr($orderData['customer']['email'] ?? '', 0, 100),
                'customer_dni' => substr($orderData['customer']['dni'] ?? '', 0, 20),
                'customer_phone' => substr($orderData['customer']['phone'] ?? '', 0, 20),
                'customer_address' => substr($orderData['customer']['address'] ?? '', 0, 150),
                'customer_district' => substr($orderData['customer']['district'] ?? '', 0, 50),
                'customer_city' => substr($orderData['customer']['city'] ?? '', 0, 50),
            ];

            // 🔥 DIVIDIR DATOS GRANDES EN CHUNKS (máx 500 chars)
            $orderDataJson = json_encode($orderData, JSON_UNESCAPED_UNICODE);
            $cartDataJson = json_encode($orderData['items'], JSON_UNESCAPED_UNICODE);

            // Dividir orderData en chunks si es necesario
            if (strlen($orderDataJson) <= 450) {
                $metadata['order_data'] = $orderDataJson;
            } else {
                // Dividir en múltiples campos
                $chunks = str_split($orderDataJson, 450);
                foreach ($chunks as $index => $chunk) {
                    $metadata["order_data_{$index}"] = $chunk;
                }
                $metadata['order_data_chunks'] = count($chunks);
            }

            // Dividir cartData en chunks si es necesario
            if (strlen($cartDataJson) <= 450) {
                $metadata['cart_data'] = $cartDataJson;
            } else {
                $chunks = str_split($cartDataJson, 450);
                foreach ($chunks as $index => $chunk) {
                    $metadata["cart_data_{$index}"] = $chunk;
                }
                $metadata['cart_data_chunks'] = count($chunks);
            }

            // Timestamp para debugging
            $metadata['created_at'] = now()->toIso8601String();

            Log::info('Metadata preparado', [
                'fields_count' => count($metadata),
                'order_data_length' => strlen($orderDataJson),
                'cart_data_length' => strlen($cartDataJson),
            ]);

            // ✅ CREAR SESIÓN
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'customer_email' => $orderData['customer']['email'] ?? null,
                'metadata' => $metadata,
                'billing_address_collection' => 'auto',
                'shipping_address_collection' => [
                    'allowed_countries' => ['PE'],
                ],
            ]);

            Log::info('✅ Sesión de Stripe creada exitosamente', [
                'session_id' => $session->id,
                'url' => $session->url,
                'metadata_stored' => count($metadata) . ' campos',
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al crear sesión de Stripe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * 🔥 Verificar estado del pago con reconstrucción de metadata
     */
    public function verifyPayment(string $sessionId)
    {
        try {
            Log::info('Verificando pago de Stripe', ['session_id' => $sessionId]);

            $session = Session::retrieve($sessionId);

            Log::info('Sesión de Stripe recuperada', [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status,
                'status' => $session->status,
            ]);

            $status = 'pending';
            if ($session->payment_status === 'paid') {
                $status = 'paid';
            } elseif ($session->payment_status === 'unpaid') {
                $status = 'unpaid';
            }

            // 🔥 RECONSTRUIR METADATA SI ESTÁ EN CHUNKS
            $metadata = $session->metadata->toArray();
            
            // Reconstruir order_data si está dividido
            if (isset($metadata['order_data_chunks'])) {
                $orderDataComplete = '';
                for ($i = 0; $i < $metadata['order_data_chunks']; $i++) {
                    $orderDataComplete .= $metadata["order_data_{$i}"] ?? '';
                }
                $metadata['order_data'] = $orderDataComplete;
                
                // Limpiar chunks individuales
                for ($i = 0; $i < $metadata['order_data_chunks']; $i++) {
                    unset($metadata["order_data_{$i}"]);
                }
                unset($metadata['order_data_chunks']);
            }

            // Reconstruir cart_data si está dividido
            if (isset($metadata['cart_data_chunks'])) {
                $cartDataComplete = '';
                for ($i = 0; $i < $metadata['cart_data_chunks']; $i++) {
                    $cartDataComplete .= $metadata["cart_data_{$i}"] ?? '';
                }
                $metadata['cart_data'] = $cartDataComplete;
                
                // Limpiar chunks individuales
                for ($i = 0; $i < $metadata['cart_data_chunks']; $i++) {
                    unset($metadata["cart_data_{$i}"]);
                }
                unset($metadata['cart_data_chunks']);
            }

            Log::info('✅ Metadata reconstruido', [
                'has_order_data' => isset($metadata['order_data']),
                'has_cart_data' => isset($metadata['cart_data']),
                'order_data_length' => isset($metadata['order_data']) ? strlen($metadata['order_data']) : 0,
                'cart_data_length' => isset($metadata['cart_data']) ? strlen($metadata['cart_data']) : 0,
            ]);

            // Obtener charge_id si existe
            $chargeId = null;
            if ($session->payment_intent) {
                try {
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);
                    $chargeId = $paymentIntent->charges->data[0]->id ?? null;
                } catch (\Exception $e) {
                    Log::warning('No se pudo obtener charge_id', ['error' => $e->getMessage()]);
                }
            }

            return [
                'success' => true,
                'status' => $status,
                'payment_status' => $session->payment_status,
                'payment_intent_id' => $session->payment_intent ?? null,
                'charge_id' => $chargeId,
                'amount' => $session->amount_total / 100,
                'currency' => $session->currency,
                'customer_email' => $session->customer_email,
                'metadata' => $metadata,
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al verificar pago de Stripe', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener detalles de una sesión (útil para debugging)
     */
    public function getSessionDetails(string $sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);

            // Reconstruir metadata
            $metadata = $session->metadata->toArray();
            
            if (isset($metadata['order_data_chunks'])) {
                $orderDataComplete = '';
                for ($i = 0; $i < $metadata['order_data_chunks']; $i++) {
                    $orderDataComplete .= $metadata["order_data_{$i}"] ?? '';
                }
                $metadata['order_data'] = $orderDataComplete;
            }

            if (isset($metadata['cart_data_chunks'])) {
                $cartDataComplete = '';
                for ($i = 0; $i < $metadata['cart_data_chunks']; $i++) {
                    $cartDataComplete .= $metadata["cart_data_{$i}"] ?? '';
                }
                $metadata['cart_data'] = $cartDataComplete;
            }

            return [
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'payment_status' => $session->payment_status,
                    'status' => $session->status,
                    'amount_total' => $session->amount_total / 100,
                    'currency' => $session->currency,
                    'customer_email' => $session->customer_email,
                    'payment_intent' => $session->payment_intent,
                    'metadata' => $metadata,
                    'created' => date('Y-m-d H:i:s', $session->created),
                ],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}