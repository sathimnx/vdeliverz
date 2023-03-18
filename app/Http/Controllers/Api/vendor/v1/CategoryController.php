<?php

namespace App\Http\Controllers\Api\vendor\v1;

use App\Http\Controllers\Controller;
use App\SubCategory;
use Illuminate\Http\Request;
use Validator, DB;
use App\Product;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required',
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        // $ids = auth('api')->user()->outlets()->pluck('id');
        $products = Product::where('shop_id', $request->shop_id)->groupBy('sub_category_id')->paginate(10);
        foreach ($products as $key => $value) {
            $productCategories[] = [
                'product_category_id' => $value->sub_category_id,
                'name' => $value->subCategory->name,
                'products_count' => $value->subCategory->products()->where('shop_id', $request->shop_id)->count()
            ];
        }
        $pagination = apiPagination($products);
        return response(['status' => true, 'message' => 'Product Categories', 'categories' => $productCategories ?? [], 'pagination' => $pagination]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:sub_categories,name'
        ]);
        if($validator->fails()){
            $errors = implode(" & ", $validator->errors()->all());
            return response(['status' => false, 'message' => $errors]);
        }
        $productCategory = SubCategory::create(['name' => $request->name]);
        return response(['status' => true, 'message' => 'Product Category Created.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function show(SubCategory $subCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCategory $subCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $subCategory)
    {
        //
    }
}
