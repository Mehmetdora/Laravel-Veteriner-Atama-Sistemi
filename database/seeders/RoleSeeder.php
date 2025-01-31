<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rolleri oluştur
        $adminRole = Role::create(['name' => 'admin']);
        $veterinerRole = Role::create(['name' => 'veteriner']);

        // İzinleri oluştur
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'edit user']);


        // Admin'e tüm izinleri ver
        $adminRole->givePermissionTo(Permission::all());

        // Veteriner sadece blog oluşturabilir
        $veterinerRole->givePermissionTo(['edit user']);
    }
}
