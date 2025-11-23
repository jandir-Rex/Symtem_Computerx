<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Muestra la lista de Proveedores.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $proveedores = Proveedor::when($search, function($query, $search) {
                return $query->where('nombre', 'like', "%{$search}%")
                           ->orWhere('ruc', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Muestra el formulario para crear un nuevo Proveedor.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Almacena un Proveedor recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        
        Proveedor::create($data);

        return redirect()->route('dashboard.proveedores.index')->with('success', '✅ Proveedor registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un Proveedor.
     */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualiza el Proveedor especificado en la base de datos.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $data = $request->validate($this->rules($proveedor->id));

        $proveedor->update($data);

        return redirect()->route('admin.proveedores.index')->with('success', '🔄 Proveedor actualizado correctamente.');
    }

    /**
     * Elimina el Proveedor de la base de datos.
     */
    public function destroy(Proveedor $proveedor)
    {
        // Verificar si tiene egresos asociados
        if ($proveedor->egresos()->count() > 0) {
            return redirect()->route('admin.proveedores.index')
                ->with('error', '⚠️ No se puede eliminar el proveedor porque tiene egresos registrados.');
        }

        $proveedor->delete();

        return redirect()->route('admin.proveedores.index')->with('success', '🗑️ Proveedor eliminado correctamente.');
    }

    /**
     * Reglas de validación para Proveedor.
     */
    protected function rules($id = null)
    {
        return [
            'ruc' => 'required|string|size:11|unique:proveedors,ruc,' . $id,
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }
}