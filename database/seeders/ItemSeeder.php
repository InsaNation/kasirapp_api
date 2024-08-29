<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'coca cola',
                'items_code' => rand(100000000000, 999999999999),
                'stock' => 100,
                'price' => 10000,
                'is_deleted' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($data as $val) {
            Item::insert([
                'name' => $val['name'],
                'items_code' => $val['items_code'],
                'stock' => $val['stock'],
                'price' => $val['price'],
                'is_deleted' => $val['is_deleted'],
                'created_at' => $val['created_at'],
                'updated_at' => $val['updated_at']
            ]);
        }
    }
}
