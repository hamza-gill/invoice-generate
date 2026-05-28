<?php

namespace Database\Seeders;

use App\Models\PlatformAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformAdminSeeder extends Seeder
{
    public function run(): void
    {
        PlatformAdmin::updateOrCreate(
            ['email' => 'admin@reconx.com'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'super_admin',
            ]
        );
    }
}
