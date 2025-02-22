<?php

namespace Database\Seeders;

use App\Models\EvrakTur;
use App\Models\Izin;
use App\Models\User;
use App\Models\Veteriner;
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
        $memurRole = Role::create(['name' => 'memur']);
        $veterinerRole = Role::create(['name'=>'veteriner']);

        // İzinleri oluştur
        Permission::create(['name' => 'manage all']);
        Permission::create(['name' => 'edit veteriner']);
        Permission::create(['name' => 'edit evrak']);


        // Admin'e tüm izinleri ver
        $adminRole->givePermissionTo(Permission::all());

        // memur sadece evrak düzenleyip ekleyebilir
        $memurRole->givePermissionTo(['edit evrak']);


        User::create([
            'name' => 'veteriner1 ',
            'username' => 'veteriner1',
            'email' => 'veteriner1@gmail.com',
            'phone_number' => '1213423242',
            'password' => bcrypt('123123')
        ])->assignRole('veteriner');

        User::create([
            'name' => 'memur1 ',
            'username' => 'memur1',
            'email' => 'memur1@gmail.com',
            'phone_number' => '1213143242',
            'password' => bcrypt('123123')
        ])->assignRole('memur');

        User::create([
            'name' => 'memur2 ',
            'username' => 'memur2',
            'email' => 'memur2@gmail.com',
            'phone_number' => '4425345232',
            'password' => bcrypt('123123')
        ])->assignRole('memur');

        User::create([
            'name' => 'admin ',
            'username' => 'admin123',
            'email' => 'admin@gmail.com',
            'phone_number' => '1231232323',
            'password' => bcrypt('123123')
        ])->assignRole('admin');

        EvrakTur::create(['name' => 'İthalat']);
        EvrakTur::create(['name' => 'Transit']);
        EvrakTur::create(['name' => 'Antrepo']);

        $permissions = ['Yıllık İzin', 'Hastalık İzni', 'Özel İzin', 'Eğitim İzni'];

        foreach ($permissions as $permission) {
            Izin::create(['name' => $permission]);
        }

        User::find(1)->izins()->attach(1,['startDate' => '2025-03-01','endDate'=>'2025-03-05']);
    }
}
