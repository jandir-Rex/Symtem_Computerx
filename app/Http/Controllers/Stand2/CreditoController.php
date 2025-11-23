<?php

namespace App\Http\Controllers\Stand2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Credito;
use App\Models\Cliente;

class CreditoController extends Controller
{
    // Mostrar todos los créditos
    public function index()
    {
        $creditos = Credito::with('cliente')->latest()->paginate(20);
        return view('stands.stand2.creditos.index', compact('creditos'));
    }

    // Formulario para crear un nuevo crédito
    public function create()
    {
        $clientes = Cliente::activo()->get();
        return view('stands.stand2.creditos.create', compact('clientes'));
    }

    // Guardar un crédito
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'monto' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Credito::create($request->all());

        return redirect()->route('stands.stand2.creditos.index')
                         ->with('success', 'Crédito registrado correctamente.');
    }

    // Ver detalle de un crédito
    public function show(Credito $credito)
    {
        $credito->load('cliente');
        return view('stands.stand2.creditos.show', compact('credito'));
    }

    // Formulario para editar un crédito
    public function edit(Credito $credito)
    {
        $clientes = Cliente::activo()->get();
        return view('stands.stand2.creditos.edit', compact('credito', 'clientes'));
    }

    // Actualizar crédito
    public function update(Request $request, Credito $credito)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'monto' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $credito->update($request->all());

        return redirect()->route('stands.stand2.creditos.index')
                         ->with('success', 'Crédito actualizado correctamente.');
    }

    // Eliminar crédito
    public function destroy(Credito $credito)
    {
        $credito->delete();
        return redirect()->route('stands.stand2.creditos.index')
                         ->with('success', 'Crédito eliminado correctamente.');
    }
}
