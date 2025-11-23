<?php
// app/Http/Controllers/UsuarioController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    /**
     * Constructor - Solo admin puede acceder
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::with('roles')->latest()->paginate(15);
        $roles = Role::all();
        
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => ['required', 'exists:roles,name'],
            'stand_id' => ['nullable', 'exists:stands,id']
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'rol.required' => 'Debe seleccionar un rol',
            'rol.exists' => 'El rol seleccionado no existe'
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'stand_id' => $request->stand_id,
            'activo' => $request->has('activo') ? true : false
        ]);

        $usuario->assignRole($request->rol);

        return redirect()->route('usuarios.index')
            ->with('success', '✓ Usuario creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        $usuario->load(['roles', 'ventas', 'stand']);
        
        // Estadísticas del usuario
        $stats = [
            'total_ventas' => $usuario->ventas()->count(),
            'ventas_mes' => $usuario->ventas()->whereMonth('created_at', now()->month)->count(),
            'total_ingresos' => $usuario->ventas()->sum('total'),
            'ultima_actividad' => $usuario->ventas()->latest()->first()?->created_at
        ];
        
        return view('usuarios.show', compact('usuario', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'rol' => ['required', 'exists:roles,name'],
            'stand_id' => ['nullable', 'exists:stands,id']
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'stand_id' => $request->stand_id,
            'activo' => $request->has('activo') ? true : false
        ];

        // Solo actualizar contraseña si se proporciona
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);
        
        // Actualizar rol
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('usuarios.index')
            ->with('success', '✓ Usuario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        // Obtener el ID del usuario autenticado
        $usuarioActualId = Auth::id();
        
        // No permitir eliminar el propio usuario
        if ($usuario->id == $usuarioActualId) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propio usuario']);
        }

        // Verificar si tiene ventas asociadas
        if ($usuario->ventas()->exists()) {
            return back()->withErrors([
                'error' => 'No se puede eliminar el usuario porque tiene ventas registradas. Puede desactivarlo en su lugar.'
            ]);
        }

        $nombreUsuario = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', "✓ Usuario '{$nombreUsuario}' eliminado correctamente");
    }
    
    /**
     * Toggle estado activo/inactivo (AJAX)
     */
    public function toggleEstado(Request $request, User $usuario)
    {
        // Obtener el ID del usuario autenticado
        $usuarioActualId = Auth::id();
        
        // No permitir desactivar el propio usuario
        if ($usuario->id == $usuarioActualId) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propio usuario'
            ], 400);
        }

        // Cambiar estado
        $nuevoEstado = !$usuario->activo;
        $usuario->update(['activo' => $nuevoEstado]);

        return response()->json([
            'success' => true,
            'activo' => $usuario->activo,
            'message' => $usuario->activo ? '✓ Usuario activado correctamente' : '✓ Usuario desactivado correctamente'
        ]);
    }
    
    /**
     * Resetear contraseña
     */
    public function resetPassword(Request $request, User $usuario)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ], [
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);

        $usuario->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => '✓ Contraseña actualizada correctamente'
        ]);
    }
    
    /**
     * Cambiar rol del usuario
     */
    public function cambiarRol(Request $request, User $usuario)
    {
        $request->validate([
            'rol' => 'required|exists:roles,name'
        ], [
            'rol.required' => 'Debe seleccionar un rol',
            'rol.exists' => 'El rol seleccionado no existe'
        ]);

        // No permitir cambiar rol al propio usuario
        if ($usuario->id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes cambiar tu propio rol'
            ], 400);
        }

        $usuario->syncRoles([$request->rol]);

        return response()->json([
            'success' => true,
            'message' => '✓ Rol actualizado correctamente'
        ]);
    }
    
    /**
     * Obtener usuarios por rol (AJAX)
     */
    public function porRol($rol)
    {
        $usuarios = User::role($rol)
            ->where('activo', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'stand_id']);

        return response()->json([
            'success' => true,
            'usuarios' => $usuarios
        ]);
    }
    
    /**
     * Buscar usuarios (AJAX)
     */
    public function buscar(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Debe ingresar un término de búsqueda'
            ], 400);
        }
        
        $usuarios = User::where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->where('activo', true)
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email', 'stand_id']);

        return response()->json([
            'success' => true,
            'usuarios' => $usuarios
        ]);
    }
    
    /**
     * Obtener estadísticas del usuario (AJAX)
     */
    public function estadisticas(User $usuario)
    {
        $stats = [
            'ventas_totales' => $usuario->ventas()->count(),
            'ventas_hoy' => $usuario->ventas()->whereDate('created_at', today())->count(),
            'ventas_mes' => $usuario->ventas()->whereMonth('created_at', now()->month)->count(),
            'ventas_anio' => $usuario->ventas()->whereYear('created_at', now()->year)->count(),
            'ingresos_totales' => $usuario->ventas()->sum('total'),
            'ingresos_mes' => $usuario->ventas()->whereMonth('created_at', now()->month)->sum('total'),
            'promedio_venta' => $usuario->ventas()->avg('total'),
            'ultima_venta' => $usuario->ventas()->latest()->first()?->created_at?->format('d/m/Y H:i')
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
    
    /**
     * Exportar usuarios a CSV
     */
    public function exportar()
    {
        $usuarios = User::with('roles')->get();
        
        $filename = 'usuarios_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Nombre',
            'Email',
            'Rol',
            'Stand',
            'Estado',
            'Fecha Registro'
        ]);
        
        // Datos
        foreach ($usuarios as $usuario) {
            fputcsv($output, [
                $usuario->id,
                $usuario->name,
                $usuario->email,
                $usuario->roles->pluck('name')->implode(', '),
                $usuario->stand_id ? "Stand {$usuario->stand_id}" : 'Sin asignar',
                $usuario->activo ? 'Activo' : 'Inactivo',
                $usuario->created_at->format('d/m/Y H:i')
            ]);
        }
        
        fclose($output);
        exit;
    }
}