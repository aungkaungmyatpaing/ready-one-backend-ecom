<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ProductDetailResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Http\Resources\ProductImageResource;
use App\Http\Resources\ProductResource;

class ProductController extends BaseController
{
    //listing
    public function listing(Request $request)
    {
        $request->validate([
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        $query = Product::active()->with('image','subCategory', 'category');

        if (isset($request->category_id)) {
            $query = $query->where('category_id', $request->category_id);
        }
        if (isset($request->sub_category_id)) {
            $query = $query->where('sub_category_id', $request->sub_category_id);
        }

        if (isset($request->search_key)) {
            $query = $query->where(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->search_key . '%');
            });
        }

        $result = $query->orderBy('id', 'desc')->paginate($request->limit);

        $totalPages = ceil($result->total() / $request->limit);

        if ($result->total() == 0) {
            return $this->sendError(204, 'No Product Found');
        }

        return response()->json([
            'success' => true,
            'total' => $result->total(),
            'can_load_more' => $result->total() == 0 || $request->page >= $totalPages ? false : true,
            'data' => ProductResource::collection($result)
        ], 200);
    }

    //product detail
    public function detail($id)
    {
        $product = Product::where('id', $id)->with( 'category', 'image')->first();
        if (!$product) {
            return $this->sendError(204, 'No Product Found');
        }
        return $this->sendResponse('success', new ProductDetailResource($product));
    }

    //product images
    public function productImages($id)
    {
        $images = ProductImage::where('product_id', $id)->get();
        if (!$images->count()) {
            return $this->sendError(204, 'No Product Images Found');
        }
        return $this->sendResponse('success', ProductImageResource::collection($images));
    }
}
