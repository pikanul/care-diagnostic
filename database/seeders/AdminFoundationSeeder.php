<?php

namespace Database\Seeders;

use App\Support\AdminFoundationInstaller;
use Illuminate\Database\Seeder;

class AdminFoundationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminFoundationInstaller::syncRolesAndPermissions();
    }
}
