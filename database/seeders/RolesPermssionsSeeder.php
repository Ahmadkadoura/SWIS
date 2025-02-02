<?php

namespace Database\Seeders;

use App\Enums\userType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermssionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $keeperRole = Role::create(['name' => 'keeper']);
        $donorRole = Role::create(['name' => 'donor']);

        // Define permissions
        $permissions = [
            'Admin',
            'Keeper',
            'Donor',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');}

        // Assign permissions to roles
        $adminRole->syncPermissions(['Admin']);
        $keeperRole->givePermissionTo('Keeper');
        $donorRole->givePermissionTo('Donor');

        // Create users and assign roles
        $adminUser = User::factory()->create([
            'name' => 'Admin name',
            'email' => 'AdminName@Admin.com',
            'password' => bcrypt('password'),
            'contact_email'=>'khaledabdalslam99@gmail.com',
            'type'=>userType::admin->value,
        ]);
        $adminUser->assignRole($adminRole);
        $adminUser->givePermissionTo('Admin');


        $keeperUser = User::factory()->create([
            'name' => 'Keeper name',
            'email' => 'KeeperName@Keeper.com',
            'password' => bcrypt('password'),
            'type'=>userType::keeper->value,

        ]);
        $keeperUser->assignRole($keeperRole);
        $keeperUser->givePermissionTo('Keeper');


        $donorUser = User::factory()->create([
            'name' => 'donor name',
            'email' => 'DonorName@Donor.com',
            'password' => bcrypt('password'),
            'type'=>userType::donor->value,

        ]);
        $donorUser->assignRole($donorRole);
        $donorUser->givePermissionTo('Donor');

    }
}
