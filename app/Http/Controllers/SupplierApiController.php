<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierApiController extends Controller
{
    public function index()
    {
        $limit = request()->input('limit', 10);
        $items = Supplier::paginate($limit);
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
        return response()->json(['data' => $data, 'meta' => $meta]);
    }

    public function store(Request $request)
    {
        $item = Supplier::create($request->all());
        return response()->json(['data' => $item], 201);
    }

    public function show($id)
    {
        $item = Supplier::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = Supplier::findOrFail($id);
        $item->update($request->all());
        return response()->json(['data' => $item]);
    }

    public function destroy($id)
    {
        $item = Supplier::findOrFail($id);
        $item->delete();
        return response(null, 204);
    }
}
