<?php

namespace App\Http\Controllers\Backend;

use App\Models\SubCategory;
use Exception;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public $productImageArray = [];
    /**
     * product listing view
     *
     * @return void
     */
    public function listing()
    {
//        return Product::with('subCategory','category','image')->active()->orderBy('id','desc')->get();
        return view('backend.products.index');
    }

    /**
     * Product create
     *
     * @return void
     */
    public function create()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return view('backend.products.create', compact('categories'));
    }

     /**
     * Product Store
     *
     * @param Request $request
     * @return void
     */
    public function store(StoreProductRequest $request)
    {
//       return $request;
        DB::beginTransaction();
        try {
            $cate = Category::find($request->category_id);
            if ($cate && $cate->sub_category->count() > 0) {
                if (!$request->has('subcategory')) {
                    return redirect()->back()->with('fail', 'Selected category has sub-categories, Please select a sub_category!');
                }
            }
            $product = new Product();
            $product->name = $request->name;

            $product->category_id = $request->category_id ?? null;
            $product->sub_category_id = $request->input('subcategory');
            // $product->sub_category_id = $request->subcategory ?? null;
            $product->description = $request->description;
            $product->weight = $request->weight;

            $product->instock = $request->instock;
            $product->save();

            if ($request->hasFile('images')) {
                $this->_createProductImages($product->id, $request->file('images'));
            }

            DB::commit();
            return redirect()->route('product')->with('created', 'Product Created Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Product detail
     *
     */
    public function detail(Product $product){
        $product->with('category','images')->first();
        $data = [
            'product'=>$product,
        ];

        return view('backend.products.detail')->with($data);
    }

    /**
   * Create Review Images
   */
  private function _createProductImages($productId, $files)
  {
      foreach ($files as $image) {
          $this->productImageArray[] = [
              'product_id'      => $productId,
              'path'           => $image->store('products'),
              'created_at'     => now(),
              'updated_at'     => now(),
          ];
      }

      ProductImage::insert($this->productImageArray);
  }

    /**
     * Product edit
     *
     * @param StoreProductRequest $request
     * @param [type] $id
     * @return void
     */
    public function edit(Product $product)
    {
//        return $product;
        $categories = Category::orderBy('id', 'desc')->get();
        $subCategories = SubCategory::where('category_id',$product->category_id)->get();
        return view('backend.products.edit', compact('product', 'categories','subCategories'));
    }

    /**
     * Update Product
     *
     * @param [type] $id
     * @param StoreProductRequest $request
     * @return void
     */
    public function update(Product $product, UpdateProductRequest $request)
    {
//        return $request;
        if (empty($request->old) && empty($request->images)) {
            return redirect()->back()->with('fail', 'Product Image is required');
        }

        DB::beginTransaction();
        try {
            $product->name = $request->name;
            $product->category_id = $request->category_id ?? null;
            $product->sub_category_id = $request->subcategory ?? null;
            $product->weight = $request->weight;
            $product->description = $request->description;
            $product->instock = $request->instock;

            $product->update();

            // old image file delete
            if ($request->has('old')) {
                $files = $product->images()->whereNotIn('id', $request->old)->get();## oldimg where not in request old
                if (count($files) > 0) { ## delete oldimg where not in request old
                    foreach ($files as $file) {
                        $oldPath = $file->getRawOriginal('path') ?? '';
                        Storage::delete($oldPath);
                    }

                    $product->images()->whereNotIn('id', $request->old)->delete();
                }
            }

            if ($request->hasFile('images')) {
                $this->_createProductImages($product->id, $request->file('images'));
            }

            DB::commit();
            return redirect()->route('product')->with('updated', 'Product Updated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

     /**
     * Product destroy
     *
     * @param [type] $id
     * @return void
     */
    public function destroy(Product $product)
    {
        $product->update(['status'=> '0']);
        return 'success';
    }

     /**
     * ServerSide
     *
     * @return void
     */
    public function serverSide()
    {
        $product = Product::with('subCategory','category','image')->active()->orderBy('id','desc');
        return datatables($product)
        ->addColumn('image', function ($each) {
            $image = $each->image;
            return '<img src="'.$image->path.'" class="thumbnail_img"/>';
        })
        ->addColumn('category', function ($each) {
            return $each->category->name ?? '---';
        })
        ->addColumn('subCategory', function ($each) {
            return $each->subCategory->name ?? '---';
        })
        ->editColumn('price',function($each){
            return number_format($each->price,).' MMK';
        })
        ->editColumn('instock',function($each){
            if($each->instock == 1){
                $instock = '<div class="badge badge-soft-success">instock</div>';
            }else{
                $instock = '<div class="badge badge-soft-danger">out of stock</div>';
            }
            return $instock;
        })
        ->addColumn('action', function ($each) {

            $show_icon = '<a href="'.route('product.detail', $each->id).'" class="detail_btn btn btn-sm btn-info"><i class="ri-eye-fill btn_icon_size"></i></a>';
            $edit_icon = '<a href="'.route('product.edit', $each->id).'" class="btn btn-sm btn-success edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';
            $delete_icon = '<a href="#" class="btn btn-sm btn-danger delete_btn" data-id="'.$each->id.'"><i class="mdi mdi-trash-can-outline btn_icon_size"></i></a>';
            return '<div class="action_icon d-flex gap-3">'. $show_icon .$edit_icon. $delete_icon .'</div>';
        })
        ->rawColumns(['category','subCategory', 'instock', 'action', 'image'])
        ->toJson();
    }

    /**
     * Product images
     *
     * @return void
     */
    public function images(Product $product)
    {
        $oldImages = [];
        foreach ($product->images as $img) {
            $oldImages[] = [
            'id'  => $img->id,
            'src' => $img->path,
          ];
        }

        return response()->json($oldImages);
    }
}
