<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, DB, Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['categories'] = \App\Category::where('type_id', 1)->orderBy('order')->paginate(10);
        if (request()->ajax()) {
            if(isset(request()->search) && !empty(request()->search)){
                    $data['categories'] = \App\Category::where('type_id', 1)->where('name', 'Like', '%'.request()->search.'%')
                                        ->latest()->paginate(10);
            }
                return view('category.category_table', array('categories' => $data['categories']))->render();
            }
        return view('category.category', $data ?? NULL);
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
        try {
            // dd($request);
            $validator = Validator::make($request->all(), [
                'type_id' => 'required',
                'name' => 'required',
                'image' => 'required',
                'banner' => 'required'
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            $category = new Category;
            $category->name = $request->name;
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {

                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."cat." .$extension;
                    $request->image->move(config('constants.category_icon_image'), $file_path);
                    $category->image = $file_path;
                }
            }
            if($request->hasFile('banner')){
                if($request->file('banner')->isValid())
                {

                    $extension = $request->banner->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."bancat." .$extension;
                    $request->banner->move(config('constants.category_banner_image'), $file_path);
                    $category->banner = $file_path;
                }
            }
            $category->order = Category::count() + 1;
            $category->type_id = 1;
            $category->save();
            flash()->success('Category Created');
            return redirect(route('categories.index'));
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return response($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type_id' => 'required',
                'name' => 'required',
            ]);
            if($validator->fails()){
                flash()->error($validator->messages()->first());
                return back()->withInput();
            }
            // dd($request);
            $category->name = $request->name;
            if($request->hasFile('image')){
                if($request->file('image')->isValid())
                {
                    $extension = $request->image->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."cat." .$extension;
                    $request->image->move(config('constants.category_icon_image'), $file_path);
                    $category->image = $file_path;
                }
            }
            if($request->hasFile('banner')){
                if($request->file('banner')->isValid())
                {

                    $extension = $request->banner->extension();
                    $file_path = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->name)).time()."cat." .$extension;
                    $request->banner->move(config('constants.category_banner_image'), $file_path);
                    $category->banner = $file_path;
                }
            }
            $category->type_id = 1;
            $category->save();
            flash()->success('Category Updated');
            return redirect(route('categories.index'));
        } catch (\Throwable $th) {
            return catchResponse();
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }

    public function orderChange(Request $request, Category $category){
        $old_order = $category->order;
        $category->update(['order' => null]);
        $old_service = Category::where('order', $request->num)->first();
        Category::where('order', $request->num)->update(['order' => $old_order]);
        $category->update(['order' => $request->num]);
        return response(['status' => true, 'id' => $old_service->id ?? null, 'order' => $old_order]);
    }

}