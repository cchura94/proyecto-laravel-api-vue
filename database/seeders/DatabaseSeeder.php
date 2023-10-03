<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $p0 = new Permiso();
        $p0->nombre = "manage_all";
        $p0->action = "manage";
        $p0->subject = "all";
        $p0->detalle = "Administrar Todo";
        $p0->save();

        $p1 = new Permiso();
        $p1->nombre = "listar_categoria";
        $p1->action = "listar";
        $p1->subject = "categoria";
        $p1->detalle = "Lista Categoria";
        $p1->save();

        $p2 = new Permiso();
        $p2->nombre = "guardar_categoria";
        $p2->action = "guardar";
        $p2->subject = "categoria";
        $p2->detalle = "Guardar Categoria";
        $p2->save();

        $r0 = new Role();
        $r0->nombre = "Super Administrador";
        $r0->save();

        $r1 = new Role();
        $r1->nombre = "DIRECTOR";
        $r1->save();

        $r2 = new Role();
        $r2->nombre = "VENTAS";
        $r2->save();

        // asignar permiso al role
        $r0->permisos()->attach([$p0->id]);
        $r1->permisos()->attach([$p1->id, $p2->id]);
        $r2->permisos()->attach([$p1->id]);

        // asignar role al usuario
        $u0 = new User();
        $u0->name = "Super Admin";
        $u0->email = "superadmin@mail.com";
        $u0->password = bcrypt("admin54321");
        $u0->save();

        $u1 = new User();
        $u1->name = "Juan DIRECTOR";
        $u1->email = "director@mail.com";
        $u1->password = bcrypt("director54321");
        $u1->save();

        $u2 = new User();
        $u2->name = "ANA VENTAS";
        $u2->email = "ana@mail.com";
        $u2->password = bcrypt("ana54321");
        $u2->save();
        

        // asignar role al usuario

        $u0->asignarRole($r0);
        $u1->asignarRole($r1);
        $u2->asignarRole($r2);


    }
}
