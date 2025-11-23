<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = $this->calculateTotal($cart);
        
        return view('cart.index', compact('cart', 'total'));
    }

    public function getCartData()
    {
        $cart = Session::get('cart', []);
        $count = array_sum(array_column($cart, 'quantity'));
        $total = $this->calculateTotal($cart);

        return response()->json([
            'count' => $count,
            'total' => $total,
            'items' => $cart
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'image' => 'nullable|string',
            'category' => 'nullable|string',
            'stock' => 'nullable|integer|min:0'
        ]);

        $cart = Session::get('cart', []);

        $productId = $request->input('id');
        $quantity = $request->input('quantity', 1);
        $stock = $request->input('stock', 999);

        // Verificar si ya existe en el carrito
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            if (isset($item['id']) && $item['id'] == $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $newQuantity = $cart[$existingIndex]['quantity'] + $quantity;
            
            if ($newQuantity > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock disponible'
                ], 400);
            }
            
            $cart[$existingIndex]['quantity'] = $newQuantity;
        } else {
            if ($quantity > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock disponible'
                ], 400);
            }

            $cart[] = [
                'id' => $productId,
                'name' => $request->input('name'),
                'price' => floatval($request->input('price')),
                'quantity' => $quantity,
                'image' => $request->input('image'),
                'category' => $request->input('category'),
                'stock' => $stock
            ];
        }

        Session::put('cart', $cart);

        $count = array_sum(array_column($cart, 'quantity'));
        $total = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Producto añadido al carrito',
            'cart_count' => $count,
            'cart_total' => $total
        ]);
    }

    public function update($index, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Session::get('cart', []);

        if (!isset($cart[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $newQuantity = $request->input('quantity');
        $stock = $cart[$index]['stock'] ?? 999;

        if ($newQuantity > $stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente'
            ], 400);
        }

        $cart[$index]['quantity'] = $newQuantity;
        Session::put('cart', $cart);

        $total = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada',
            'cart_total' => $total
        ]);
    }

    public function remove($index)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            Session::put('cart', $cart);
        }

        $count = array_sum(array_column($cart, 'quantity'));
        $total = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado',
            'cart_count' => $count,
            'cart_total' => $total
        ]);
    }

    public function clear()
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado',
            'cart_count' => 0,
            'cart_total' => 0
        ]);
    }

    private function calculateTotal($cart)
    {
        return array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
    }
}