<?php

// app/Http/Controllers/TransaksiController.php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        // Validate input data
        $validated = Validator::make($request->all(),[
            'invoice_code' => 'required|string|unique:transaksi,invoice_code',
            'total_items' => 'required|integer',
            'total_price' => 'required|numeric',
            'change' => 'required|numeric', // Add validation for change
            'bayar' => 'required|numeric',  // Add validation for bayar
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

        DB::beginTransaction(); // Start database transaction

        try {
            // Create main transaction entry
            $transaksi = Transaksi::create([
                'invoice_code' => $invoiceCode,
                'total_items' => $validated['total_items'],
                'total_price' => $validated['total_price'],
                'change' => $validated['change'], // Save change
                'bayar' => $validated['bayar'],   // Save bayar
            ]);

            // Create detail transaction entries and reduce stock
            foreach ($validated['details'] as $detail) {
                // Fetch the product
                $product = Item::where('name', $detail['product_name'])->first();

                // If product doesn't exist
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Product not found: ' . $detail['product_name'],
                    ], 404);
                }

                // Check if the product has sufficient stock
                if ($product->stock < $detail['quantity']) {
                    DB::rollBack(); // Rollback the transaction
                    return response()->json([
                        'message' => 'Insufficient stock for product : ' . $detail['product_name'],
                    ], 400);
                }

                // Reduce the product's stock
                $product->stock -= $detail['quantity'];
                $product->save();

                // Create the detail transaction entry
                DetailTransaksi::create([
                    'invoice_number' => $invoiceCode,
                    'product_name' => $detail['product_name'],
                    'product_price' => $detail['product_price'],
                    'quantity' => $detail['quantity'],
                ]);
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'message' => 'Transaction successfully saved!',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction if any error occurs

            // Distinguish stock issue from other exceptions
            if (str_contains($e->getMessage(), 'Insufficient stock')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }

            // Handle any other exceptions
            return response()->json([
                'message' => 'Failed to save transaction. Error : ' . $e->getMessage(),
            ], 500);
        }
    }



}