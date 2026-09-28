<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'phone' => '082121212121',
                'password' => Hash::make('admin'),
                'role' => Role::ADMIN->status(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'sekretaris@admin.com'],
            [
                'name' => 'Sekretaris Dinas',
                'phone' => '082121212122',
                'password' => Hash::make('sekretaris'),
                'role' => Role::SEKRETARIS->status(),
            ]
        );

        $staffs = [
            ['name' => 'Budi Santoso', 'email' => 'staff@admin.com', 'phone' => '082121212123'],
            ['name' => 'Siti Aminah', 'email' => 'siti@admin.com', 'phone' => '082121212124'],
            ['name' => 'Andi Pratama', 'email' => 'andi@admin.com', 'phone' => '082121212125'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@admin.com', 'phone' => '082121212126'],
        ];

        foreach ($staffs as $staff) {
            User::firstOrCreate(
                ['email' => $staff['email']],
                [
                    'name' => $staff['name'],
                    'phone' => $staff['phone'],
                    'password' => Hash::make('staff'),
                    'role' => Role::STAFF->status(),
                ]
            );
        }
    }
}
