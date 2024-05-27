<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\storeSubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCategoryRequest;

class SubCategoryController extends Controller
{
    /**
     * product listing view
     *
     * @return void
     */
    public function index()
    {
        return view('backend.subCategories.index');
    }
    public function sub_category_by_category($id){
        $subCateogry = SubCategory::where('category_id',$id)->get();
        return $subCateogry;
    }

    /**
     * Create Form
     *
     * @return void
     */
    public function create()
    {
        return view('backend.subCategories.create');
    }

    /**
     * Store Category
     *
     * @param StoreCategoryRequest $request
     * @return void
     */
    public function store(storeSubCategoryRequest $request)
    {
        $category = new SubCategory();
        $category->name = $request->name;
        $category->category_id = $request->category_id;
        if ($request->hasFile('image')) {
            $category->image = $request->file('image')->store('categories');
        }
        $category->save();

        return redirect()->route('sub.category')->with('created', 'Category created Successfully');
    }

    /**
     * Product Categeory Edit
     *
     * @param [type] $id
     * @return void
     */
    public function edit(SubCategory $category)
    {
        return view('backend.subCategories.edit', compact('category'));
    }

    /**
     * Product Category Update
     *
     * @param Reqeuest $reqeuest
     * @param [type] $id
     * @return void
     */
    public function update(StoreCategoryRequest $request, SubCategory $category)
    {
        $category->name = $request->name;
        $category->category_id = $request->category_id;
        if ($request->hasFile('image')) {
            $oldImage = $category->getRawOriginal('image') ?? '';
            Storage::delete($oldImage);
            $category->image = $request->file('image')->store('categories');
        }
        $category->update();

        return redirect()->route('sub.category')->with('updated', 'Category Updated Successfully');
    }


    /**
     * delete Category
     *
     * @return void
     */
    public function destroy(SubCategory $category)
    {
        $oldImage = $category->getRawOriginal('image') ?? '';
        Storage::delete($oldImage);

        $category->delete();

        return 'success';
    }

    /**
     * ServerSide
     *
     * @return void
     */
    public function serverSide()
    {
        $category = SubCategory::with('category')->withCount('product')->orderBy('id','desc');
        return datatables($category)
            ->addColumn('image', function ($each) {
                return '<img src="'.$each->image.'" class="thumbnail_img"/>';
            })
            ->addColumn('count', function ($each) {
                return $each->product_count;
            })
            ->addColumn('action', function ($each) {
                $edit_icon = '<a href="'.route('sub.category.edit', $each->id).'" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';
                $delete_icon = '<a href="#" class="btn btn-sm btn-danger delete_btn" data-id="'.$each->id.'"><i class="mdi mdi-trash-can-outline btn_icon_size"></i></a>';

                return '<div class="action_icon">'.$edit_icon. $delete_icon .'</div>';
            })
            ->rawColumns(['image', 'action','count'])
            ->toJson();
    }
}
