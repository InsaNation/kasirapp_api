<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search=$request->get('search');
        if ($search) {
            return Item::where('name', 'like', "%$search%")->get();
        }

        return Item::all();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'items_code' => 'required|string|max:255',
                'stock' => 'required|integer',
                'price' => 'required|numeric',
                'is_deleted' => 'required|boolean',
            ]);

            $item = Item::create($request->all());

            return response()->json($item, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'items_code' => 'required|string|max:255',
                'stock' => 'required|integer',
                'price' => 'required|numeric',
                'is_deleted' => 'required|boolean',
            ]);

            $item->update($request->all());

            return response()->json($item, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            // Validate the request
            $request->validate([
                'is_deleted' => 'required|boolean',
            ]);

            // Update the item with the new value
            $item->update(['is_deleted' => $request->is_deleted]);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
