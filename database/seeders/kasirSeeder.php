<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class kasirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'kasir',
                'role' => 'kasir',
                'email' => 'kasir@gmail.com',
                'password' => Hash::make('kasir'),
                'is_deleted' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($data as $val) {
            User::insert([
                'name' => $val['name'],
                'role' => $val['role'],
                'email' => $val['email'],
                'password' => $val['password'],
                'is_deleted' => $val['is_deleted'],
                'created_at' => $val['created_at'],
                'updated_at' => $val['updated_at']
            ]);
        }
    }
}
