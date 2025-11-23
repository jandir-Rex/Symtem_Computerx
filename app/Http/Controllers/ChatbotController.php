<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Producto;

class ChatbotController extends Controller
{
    public function respond(Request $request)
    {
        // Validar entrada
        $message = $request->input('message');
        if (!$message || strlen($message) > 500) {
            return response()->json(['response' => 'Por favor, escribe un mensaje válido (máx. 500 caracteres).']);
        }

        try {
            // Obtener contexto de productos (con caché de 10 minutos)
            $productsContext = Cache::remember('chatbot_products_context', 600, function () {
                return $this->getProductsContext();
            });

            // Construir el prompt
            $systemMessage = "Eres Beymax, asistente virtual de Company Computer (tienda gamer peruana). 
            
Tu misión es recomendar productos reales del catálogo basándote en las necesidades del cliente.

PRODUCTOS DISPONIBLES:
{$productsContext}

REGLAS:
- Solo recomienda productos de la lista
- Menciona precio y categoría
- Sé amigable y breve (máx. 150 palabras)
- Si preguntan por algo fuera de stock, sugiere alternativas
- Para presupuestos, ordena de económico a premium
- Usa emojis para hacer la conversación más amigable

IMPORTANTE: Todos los precios están en Soles Peruanos (S/)";

            // Verificar API Key
            $apiKey = config('services.openai.api_key');
            if (!$apiKey) {
                Log::error('OpenAI API Key no configurada');
                return response()->json(['response' => 'Lo siento, el servicio no está disponible temporalmente.']);
            }

            // Llamada a OpenAI
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemMessage],
                        ['role' => 'user', 'content' => $message]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 300
                ]);

            // Manejar respuesta
            if ($response->successful()) {
                $data = $response->json();
                $botResponse = $data['choices'][0]['message']['content'] ?? 'Lo siento, no pude generar una respuesta.';
                return response()->json(['response' => trim($botResponse)]);
            }

            // Error de API
            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return response()->json(['response' => 'Lo siento, hubo un problema con el servicio.']);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json(['response' => 'Lo siento, no puedo responder en este momento.']);
        }
    }

    /**
     * Obtiene contexto optimizado: 5 económicos + 5 premium por categoría
     */
    private function getProductsContext()
    {
        $categorias = Producto::getCategorias();
        $context = "📦 CATÁLOGO COMPANY COMPUTER\n\n";

        foreach ($categorias as $slug => $nombre) {
            // Productos económicos (5 más baratos)
            $economicos = Producto::visibleEcommerce()
                ->where('categoria', $slug)
                ->orderBy('precio_venta', 'asc')
                ->take(5)
                ->get(['nombre', 'precio_venta', 'marca']);

            // Productos premium (5 más caros)
            $premium = Producto::visibleEcommerce()
                ->where('categoria', $slug)
                ->orderBy('precio_venta', 'desc')
                ->take(5)
                ->get(['nombre', 'precio_venta', 'marca']);

            // Combinar sin duplicados
            $productos = $economicos->merge($premium)->unique('nombre');

            if ($productos->isNotEmpty()) {
                $context .= "🔹 {$nombre}\n";
                
                foreach ($productos as $producto) {
                    $marca = $producto->marca ? " [{$producto->marca}]" : "";
                    $context .= "  • {$producto->nombre}{$marca} - S/ {$producto->precio_venta}\n";
                }
                
                $context .= "\n";
            }
        }

        return $context;
    }

    /**
     * Limpiar caché del chatbot (para admins)
     */
    public function clearCache()
    {
        Cache::forget('chatbot_products_context');
        return response()->json(['message' => 'Caché del chatbot limpiada']);
    }
}