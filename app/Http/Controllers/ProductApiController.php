<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{

    public function index()
    {
        $productCategories = ProductCategory::all();
        foreach ($productCategories as $productCategory) {
            $products = Product::where("product_category_id", $productCategory->id)->get();
            $productCategory["products"] = $products;
        }

        return response()->json([
            "data" => $productCategories
        ]);

        // $products = Product::with("category")->get();
        // return response()->json([
        //     "data" => $products
        // ]);

        // $products = DB::table('products')
        //     ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
        //     ->select('products.*', 'product_categories.category_name')
        //     ->get();

        // return response()->json([
        //     "data" => $products
        // ]);
        // $data = DB::table("products")
        //     ->where("id", 1)
        //     ->orWhere("product_name", "GG FILTER 12")
        //     ->paginate(3);

        // return response()->json($data);
    }
    // public function index()
    // {
    //     $limit = request()->input('limit', 10);
    //     $items = Product::paginate($limit);
    //     $data = $items->items();
    //     $meta = [
    //         'currentPage' => $items->currentPage(),
    //         'perPage' => $items->perPage(),
    //         'total' => $items->total(),
    //     ];
    //     if ($items->hasMorePages()) {
    //         $meta['next_page_url'] = url($items->nextPageUrl());
    //     }
    //     if ($items->currentPage() > 2) {
    //         $meta['prev_page_url'] = url($items->previousPageUrl());
    //     }
    //     if ($items->lastPage() > 1) {
    //         $meta['last_page_url'] = url($items->url($items->lastPage()));
    //     }
    //     return response()->json(['data' => $data, 'meta' => $meta]);
    // }

    public function store(Request $request)
    {
        $input = request()->all();

        // $result = DB::table("products")->insert([
        //     "product_name" => $input["product_name"],
        //     "price" => $input["price"],
        //     "description" => $input["description"],
        // ]);

        $product = new Product();
        $product->product_name = $input["product_name"];
        $product->price = $input["price"];
        $product->description = $input["description"];
        $result = $product->save();

        return response()->json([
            "data" => [
                "result" => $result,
                "message" => "OK"
            ]
        ]);
    }

    public function show($id)
    {
        $item = Product::find($id);
        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $input = request()->all();
        // $result = DB::table("products")
        //     ->where("id", $id)
        //     ->update([
        //         "product_name" => $input["product_name"],
        //         "price" => $input["price"],
        //         "description" => $input["description"],
        //     ]);

        $product = Product::find($id);
        $product->product_name = $input["product_name"];
        $product->price = $input["price"];
        $product->description = $input["description"];
        $result = $product->save();

        return response()->json([
            "data" => [
                "result" => $result
            ]
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $result = $product->delete();

        return response()->json([
            "data" => [
                "result" => $result
            ]
        ]);
    }
}
