<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔄 Limpiar caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // 📋 CREACIÓN DE PERMISOS
        // ========================================
        $permisos = [
            // Módulo de Ventas
            'ver ventas',
            'crear ventas',
            'editar ventas',
            'eliminar ventas',
            'generar comprobantes',
            
            // Módulo de Almacén
            'ver almacen',
            'crear productos',
            'editar productos',
            'eliminar productos',
            'ajustar stock',
            'importar productos',
            
            // Módulo de Reparaciones
            'ver reparaciones',
            'crear reparaciones',
            'editar reparaciones',
            'eliminar reparaciones',
            
            // Módulo Contable
            'ver dashboard contable',
            'gestionar egresos',
            'ver reportes',
            'exportar reportes',
            
            // Módulo de Clientes
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',
            
            // Gestión de Cuotas
            'ver cuotas',
            'registrar pagos',
            
            // Administración
            'gestionar usuarios',
            'gestionar roles',
            'ver configuracion',
            'ver todos los stands'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // ========================================
        // 👑 ROL: ADMINISTRADOR (Acceso Total)
        // ========================================
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all());

        // ========================================
        // 🏪 ROL: STAND 1 (Ventas Stand 1)
        // ========================================
        $stand1 = Role::firstOrCreate(['name' => 'Stand1']);
        $stand1->syncPermissions([
            'ver ventas',
            'crear ventas',
            'editar ventas',
            'generar comprobantes',
            'ver almacen', // Solo lectura
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'ver cuotas',
            'registrar pagos'
        ]);

        // ========================================
        // 🛠️ ROL: STAND 2 (Ventas + Reparaciones)
        // ========================================
        $stand2 = Role::firstOrCreate(['name' => 'Stand2']);
        $stand2->syncPermissions([
            'ver ventas',
            'crear ventas',
            'editar ventas',
            'generar comprobantes',
            'ver reparaciones',
            'crear reparaciones',
            'editar reparaciones',
            'ver almacen', // Solo lectura
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'ver cuotas',
            'registrar pagos'
        ]);

        // ========================================
        // 💰 ROL: CONTADOR (Finanzas y Reportes)
        // ========================================
        $contador = Role::firstOrCreate(['name' => 'Contador']);
        $contador->syncPermissions([
            'ver dashboard contable',
            'gestionar egresos',
            'ver reportes',
            'exportar reportes',
            'ver ventas',
            'ver cuotas',
            'ver todos los stands'
        ]);

        // ========================================
        // 📦 ROL: ALMACÉN (Gestión de Productos)
        // ========================================
        $almacen = Role::firstOrCreate(['name' => 'Almacen']);
        $almacen->syncPermissions([
            'ver almacen',
            'crear productos',
            'editar productos',
            'eliminar productos',
            'ajustar stock',
            'importar productos'
        ]);

        // ========================================
        // 👤 USUARIOS DE PRUEBA
        // ========================================
        
        // Usuario Admin
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('T9j!P4su@L2z&Q8rB6'),
                'stand_id' => null
            ]
        );
        $userAdmin->assignRole('Administrador');

        // Usuario Stand 1
        $userStand1 = User::firstOrCreate(
            ['email' => 'stand1@sistema.com'],
            [
                'name' => 'Vendedor Stand 1',
                'password' => Hash::make('F2q#Vm7!aZ9\$tR4nX1'),
                'stand_id' => 1
            ]
        );
        $userStand1->assignRole('Stand1');

        // Usuario Stand 2
        $userStand2 = User::firstOrCreate(
            ['email' => 'stand2@sistema.com'],
            [
                'name' => 'Vendedor Stand 2',
                'password' => Hash::make('M8u&Ks3@wT5#pQ1zH7'),
                'stand_id' => 2
            ]
        );
        $userStand2->assignRole('Stand2');

        // Usuario Contador
        $userContador = User::firstOrCreate(
            ['email' => 'contador@sistema.com'],
            [
                'name' => 'Contador',
                'password' => Hash::make('C4r@D9x!eQ7&Lm2tV5'),
                'stand_id' => null
            ]
        );
        $userContador->assignRole('Contador');

        // Usuario Almacén
        $userAlmacen = User::firstOrCreate(
            ['email' => 'almacen@sistema.com'],
            [
                'name' => 'Almacenero',
                'password' => Hash::make('A6m!R3z#B8q\$T1hP9y'),
                'stand_id' => null
            ]
        );
        $userAlmacen->assignRole('Almacen');

        echo "✅ Roles, permisos y usuarios creados exitosamente\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📧 Usuarios de prueba:\n";
        echo "   Admin:    admin@sistema.com / T9j!P4su@L2z&Q8rB6\n";
        echo "   Stand1:   stand1@sistema.com / F2q#Vm7!aZ9\$tR4nX1\n";
        echo "   Stand2:   stand2@sistema.com / M8u&Ks3@wT5#pQ1zH7\n";
        echo "   Contador: contador@sistema.com / C4r@D9x!eQ7&Lm2tV5\n";
        echo "   Almacén:  almacen@sistema.com / A6m!R3z#B8q\$T1hP9y\n";
    }
}