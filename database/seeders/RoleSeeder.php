<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = Role::create([
            "name" => "admin",
            "guard_name" => "api"
        ]);

        $user_role = Role::create([
            "name" => "user",
            "guard_name" => "api"
        ]);


        $create_permission = Permission::create(['name' => 'create promo code', 'guard_name' => 'api']);
        $admin_role->syncPermissions($create_permission);

        $use_permission = Permission::create(['name' => 'use promo code', 'guard_name' => 'api']);
        $user_role->syncPermissions($use_permission);



    }
}
