<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductApiController extends Controller
{
    public function index()
    {
        $limit = request()->input('limit', 10);
        $items = Product::paginate($limit);
        $data = $items->items();
        $meta = [
            'currentPage' => $items->currentPage(),
            'perPage' => $items->perPage(),
            'total' => $items->total(),
        ];
        if ($items->hasMorePages()) {
            $meta['next_page_url'] = url($items->nextPageUrl());
        }
        if ($items->currentPage() > 2) {
            $meta['prev_page_url'] = url($items->previousPageUrl());
        }
        if ($items->lastPage() > 1) {
            $meta['last_page_url'] = url($items->url($items->lastPage()));
        }
        return response()->json(['data' => $data, 'meta' => $meta], 200);
    }

    public function store(Request $request)
    {
        $item = Product::create($request->all());
        return response()->json(['data' => $item], 200);
    }

    public function show($id)
    {
        $item = Product::findOrFail($id);
        return response()->json(['data' => $item], 200);
    }

    public function update(Request $request, $id)
    {
        $item = Product::findOrFail($id);
        $item->update($request->all());
        return response()->json(['data' => $item], 200);
    }

    public function destroy($id)
    {
        $item = Product::findOrFail($id);
        $item->delete();
        return response()->json([
            'data' => $item
        ], 200);
    }
}
