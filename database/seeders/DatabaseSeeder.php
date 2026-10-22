<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                [
                    'name' => 'Super Admin',
                    'role' => 'SuperAdmin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Sales Staff',
                    'role' => 'Sales',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Purchase Staff',
                    'role' => 'Purchase',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Manager',
                    'role' => 'Manager',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (DB::table('inventories')->count() === 0) {
            DB::table('inventories')->insert([
                [
                    'code' => 'BRG-001',
                    'name' => 'Laptop ASUS Vivobook 14',
                    'price' => 8500000,
                    'stock' => 15,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'BRG-002',
                    'name' => 'Mouse Wireless Logitech M330',
                    'price' => 220000,
                    'stock' => 50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'BRG-003',
                    'name' => 'Keyboard Mechanical RGB Red Switch',
                    'price' => 475000,
                    'stock' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'BRG-004',
                    'name' => 'Monitor LED LG 24 Inch Full HD',
                    'price' => 1650000,
                    'stock' => 20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'BRG-005',
                    'name' => 'Headset Gaming 7.1 Surround',
                    'price' => 380000,
                    'stock' => 25,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
