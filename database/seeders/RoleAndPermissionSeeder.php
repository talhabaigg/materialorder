<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        Permission::create(['name' => 'create requisition']);
        Permission::create(['name' => 'delete requisition']);
        Permission::create(['name' => 'process requisition']);


        // Create roles and assign existing permissions
        $role = Role::create(['name' => 'foreman']);
        $role->givePermissionTo(['create requisition']);
        
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
        $adminUser = User::find(1); // Adjust this based on your user retrieval method
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}
