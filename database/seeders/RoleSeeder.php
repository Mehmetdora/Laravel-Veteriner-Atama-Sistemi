<?php

namespace Database\Seeders;

use App\Models\User;
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


        User::create([
            'name' => 'Veteriner ',
            'username' => 'veteriner123',
            'email' => 'veteriner@gmail.com',
            'phone_number' => '1231231212',
            'password' => bcrypt('123123')
        ])->assignRole('veteriner');
        
        User::create([
            'name' => 'admin ',
            'username' => 'admin123',
            'email' => 'admin@gmail.com',
            'phone_number' => '1231232323',
            'password' => bcrypt('123123')
        ])->assignRole('admin');
    }
}
