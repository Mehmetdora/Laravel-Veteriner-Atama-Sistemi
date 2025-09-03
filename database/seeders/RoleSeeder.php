<?php

namespace Database\Seeders;

use App\Models\EvrakTur;
use App\Models\GirisAntrepo;
use App\Models\Izin;
use App\Models\Urun;
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
        $veterinerRole = Role::create(['name' => 'veteriner']);

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
            'name' => 'veteriner2',
            'username' => 'veteriner2',
            'email' => 'veteriner2@gmail.com',
            'phone_number' => '1213423249',
            'password' => bcrypt('123123')
        ])->assignRole('veteriner');

        User::create([
            'name' => 'veteriner3 ',
            'username' => 'veteriner3',
            'email' => 'veteriner1@gmail.com',
            'phone_number' => '5674565656',
            'password' => bcrypt('123123')
        ])->assignRole('veteriner');

        User::create([
            'name' => 'veteriner4 ',
            'username' => 'veteriner4',
            'email' => 'veteriner1@gmail.com',
            'phone_number' => '6785674545',
            'password' => bcrypt('123123')
        ])->assignRole('veteriner');

        User::create([
            'name' => 'veteriner5 ',
            'username' => 'veteriner5',
            'email' => 'veteriner1@gmail.com',
            'phone_number' => '4568906767',
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
            'name' => 'Harun Müdür',
            'username' => 'admin1',
            'email' => 'admin1@gmail.com',
            'phone_number' => '1231232323',
            'password' => bcrypt('123123')
        ])->assignRole('admin');

        User::create([
            'name' => 'Mustafa Uğur Beken',
            'username' => 'admin2',
            'email' => 'admin2@gmail.com',
            'phone_number' => '1231232323',
            'password' => bcrypt('123123')
        ])->assignRole('admin');

        User::create([
            'name' => 'Erdem Teknisyen ',
            'username' => 'admin3',
            'email' => 'admin3@gmail.com',
            'phone_number' => '1231232323',
            'password' => bcrypt('123123')
        ])->assignRole('admin');

        Urun::create(['name' => 'Tavuk']);
        Urun::create(['name' => 'Balık']);
        Urun::create(['name' => 'Süt']);
        Urun::create(['name' => 'Tohum']);
        Urun::create(['name' => 'Küçük Baş']);
        Urun::create(['name' => 'Büyük Baş']);


        GirisAntrepo::create(['name' => 'Antrepo 1']);
        GirisAntrepo::create(['name' => 'Antrepo 2']);
        GirisAntrepo::create(['name' => 'Antrepo 3']);
        GirisAntrepo::create(['name' => 'Antrepo 4']);

        $permissions = ['Yıllık İzin', 'Hastalık İzni', 'Özel İzin', 'Eğitim İzni'];
        foreach ($permissions as $permission) {
            Izin::create(['name' => $permission]);
        }
    }
}
