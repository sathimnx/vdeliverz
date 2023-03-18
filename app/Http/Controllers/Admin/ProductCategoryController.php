<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\SubCategory;
use App\ShopSubCategory;
use Illuminate\Http\Request;
use Validator, DB, Auth;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->hasAnyRole('vendor')){
            return redirect(route('product-categories.filter', auth()->user()->shop->id));
        }
        $data['subCategories'] = ShopSubCategory::with('shop', 'subCategory')->orderBy('order')->paginate(10);
        $data['shops'] = \App\Shop::all();
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                    $search = request()->search;
                    $data['subCategories'] = ShopSubCategory::whereHas('subCategory', function($q) use($search){
                        $q->where('name', 'Like', '%'.$search.'%');
                    })->orWhereHas('shop', function($s) use($search){
                        $s->where('name', 'Like', '%'.$search.'%');
                    })->orderBy('order')->paginate(10);
            }

                return view('product_category.product_categories_table', array('subCategories' => $data['subCategories']))->render();
            }
        return view('product_category.product_categories', $data);
    }

    public function filter($shop){
        if($shop === 'all' || (auth()->user()->hasAnyRole('vendor') && auth()->user()->shop->id != $shop)){
            return redirect(route('product-categories.index'));
        }
        $data['subCategories'] = ShopSubCategory::with('shop', 'subCategory')->where('shop_id', $shop)->orderBy('order')->paginate(10);
        $data['shops'] = \App\Shop::all();
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                    $search = request()->search;
                    $data['subCategories'] = ShopSubCategory::where('shop_id', $shop)->whereHas('subCategory', function($q) use($search){
                        $q->where('name', 'Like', '%'.$search.'%');
                    })->orderBy('order')->paginate(10);
            }

                return view('product_category.product_categories_table', array('subCategories' => $data['subCategories']))->render();
            }
        return view('product_category.product_categories', $data);
    }

    public function orderChange(Request $request, ShopSubCategory $category){
        $old_order = $category->order;
        $category->update(['order' => null]);
        $old_service = ShopSubCategory::where('shop_id', $request->shop)->where('order', $request->num)->first();
        ShopSubCategory::where('shop_id', $request->shop)->where('order', $request->num)->update(['order' => $old_order]);
        $category->update(['order' => $request->num]);
        return response(['status' => true, 'id' => $old_service->id ?? null, 'order' => $old_order]);
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
                'name' => 'required|unique:sub_categories,name',
            ]);
            if($validator->fails()) {
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
        SubCategory::create($request->all());
        flash()->success('Product Category created Successfully');
        return back();
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
        $id = request()->product_category;
        $validator = Validator::make($request->all(), [
                'name' => 'required|unique:sub_categories,name,'.$id,
            ]);
            if($validator->fails()) {
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
        SubCategory::find($id)->update(['name' => $request->name]);
        flash()->success('Product Category updated Successfully');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $subCategory)
    {
        $id = request()->product_category;
        SubCategory::find($id)->delete();
        flash()->info('Product Category Deleted Successfully');
        return back();
    }
}