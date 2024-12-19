<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run()
    {
        // Path ke file Excel
        $filePath = storage_path('app/items.xlsx');

        // Load file Excel
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Looping baris demi baris untuk mengambil data dari Excel
        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            // Lewati baris pertama jika itu adalah header
            if ($rowIndex == 1) continue;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();  // Menyimpan setiap kolom dalam array
            }

            // Insert ke database
            Item::create([
                'name' => $data[0],
                'items_code' => $data[1],
                'stock' => $data[2],
                'price' => $data[3],
                'is_deleted' => $data[4],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
