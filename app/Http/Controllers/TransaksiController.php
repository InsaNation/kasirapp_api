<?php

// app/Http/Controllers/TransaksiController.php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data input
        $validated = Validator::make($request->all(),[
            'invoice_code' => 'required|string|unique:transaksi,invoice_code',
            'total_items' => 'required|integer',
            'total_price' => 'required|numeric',
            'change' => 'required|numeric',
            'bayar' => 'required|numeric',
            'cashier' => 'required|string',  // Tambahkan validasi untuk kasir
            'details' => 'required|array',
            'details.*.product_name' => 'required|string',
            'details.*.product_price' => 'required|numeric',
            'details.*.quantity' => 'required|integer',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validated->errors()
            ], 422);
        } 

        $validated = $validated->validated();
        $invoiceCode = $validated['invoice_code'];

        DB::beginTransaction();

        try {
            // Buat entri transaksi utama
            $transaksi = Transaksi::create([
                'invoice_code' => $invoiceCode,
                'total_items' => $validated['total_items'],
                'total_price' => $validated['total_price'],
                'change' => $validated['change'],
                'bayar' => $validated['bayar'],
                'cashier' => $validated['cashier'],  // Simpan nama kasir
            ]);

            // Buat entri transaksi detail dan kurangi stok
            foreach ($validated['details'] as $detail) {
                $product = Item::where('name', $detail['product_name'])->first();

                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Product not found: ' . $detail['product_name'],
                    ], 404);
                }

                if ($product->stock < $detail['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Insufficient stock for product: ' . $detail['product_name'],
                    ], 400);
                }

                $product->stock -= $detail['quantity'];
                $product->save();

                DetailTransaksi::create([
                    'invoice_number' => $invoiceCode,
                    'product_name' => $detail['product_name'],
                    'product_price' => $detail['product_price'],
                    'quantity' => $detail['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaction successfully saved!',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            if (str_contains($e->getMessage(), 'Insufficient stock')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }

            return response()->json([
                'message' => 'Failed to save transaction. Error : ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getTransactions(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            if ($user->role === 'admin') {
                // Admin: ambil semua transaksi dengan kolom tertentu
                $transaksi = Transaksi::select('invoice_code', 'total_items', 'total_price', 'change', 'bayar', 'cashier')->get();
            } elseif ($user->role === 'kasir') {
                // Kasir: ambil transaksi sesuai dengan nama kasir
                $transaksi = Transaksi::select('invoice_code', 'total_items', 'total_price', 'change', 'bayar', 'cashier')
                    ->get();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access for this role.',
                ], 403);
            }

            return response()->json($transaksi);
        } else {
            return response()->json([
                'error' => 'User not authenticated.',
            ], 401);
        }
    }








    public function daily_income()
    {
        // Ambil tanggal hari ini dalam format Y-m-d
        $today = Carbon::today();

        // Lakukan query untuk mengambil total pemasukan hari ini
        $dailyIncome = Transaksi::whereDate('created_at', $today)->sum('total_price');

        // Debugging: Periksa apakah query menghasilkan data yang benar
        // dd($dailyIncome);

        // Kembalikan data dalam format JSON
        return response()->json([
            'daily_income' => $dailyIncome,
        ], 200);
    }




    public function monthly_income()
    {
        // Ambil bulan saat ini menggunakan Carbon
        $month = Carbon::now()->month;

        // Ambil tahun saat ini untuk memastikan query pada tahun yang benar
        $year = Carbon::now()->year;

        // Lakukan query untuk menjumlahkan total pemasukan bulan ini
        $monthlyIncome = Transaksi::whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->sum('total_price');

        return response()->json([
            'monthly_income' => $monthlyIncome,
        ], 200);
    }


    public function monthly_transaction()
    {
        // Ambil bulan saat ini menggunakan Carbon
        $month = Carbon::now()->month;

        // Ambil tahun saat ini untuk memastikan query pada tahun yang benar
        $year = Carbon::now()->year;

        $monthlyTransaction = Transaksi::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->count();
        return response()->json([
            'monthly_transaction' => $monthlyTransaction,
        ], 200);
    }

    public function daily_transaction()
    {   // Ambil tanggal hari ini dalam format Y-m-d
        $today = Carbon::today();
        $dailyTransaction = Transaksi::whereDate('created_at', $today)->count();
        return response()->json([
            'daily_transaction' => $dailyTransaction,
        ], 200);
    }

    public function getDataChart(){
        $data = DB::select("WITH Months AS (
            SELECT 'Jan' AS month, 1 AS month_num UNION ALL
            SELECT 'Feb', 2 UNION ALL
            SELECT 'Mar', 3 UNION ALL
            SELECT 'Apr', 4 UNION ALL
            SELECT 'May', 5 UNION ALL
            SELECT 'Jun', 6 UNION ALL
            SELECT 'Jul', 7 UNION ALL
            SELECT 'Aug', 8 UNION ALL
            SELECT 'Sep', 9 UNION ALL
            SELECT 'Oct', 10 UNION ALL
            SELECT 'Nov', 11 UNION ALL
            SELECT 'Dec', 12
            )
            SELECT 
                M.month AS 'M', 
                COUNT(t.id) AS count 
            FROM 
                Months M 
            LEFT JOIN 
                transaksi t 
                ON M.month_num = MONTH(t.created_at) AND YEAR(t.created_at) = 2024
            GROUP BY 
                M.month, YEAR(t.created_at)
            ORDER BY 
                M.month_num;
            ");
            
        return response()->json(['data' => $data], 200);
    }



}