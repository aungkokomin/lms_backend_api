<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = Role::create(['name' => 'admin']);
        $agent = Role::create(['name' => 'affiliate']);
        $student = Role::create(['name' => 'student']);

        $datas = [
            ["name"=> "view users"],
            ["name"=> "create users"],
            ["name"=> "edit users"],
            ["name"=> "delete users"],
        ];
        foreach ($datas as $data) {
            $permissions = new Permission;
            $permissions->create($data);
            $admin->givePermissionTo($data['name']);
        }
    }
}
