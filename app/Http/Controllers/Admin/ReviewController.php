<?php

namespace App\Http\Controllers\Admin;

use App\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        if (request()->ajax()) {
            $data['reviews'] = Review::with('user', 'deliveryBoy')->latest()->paginate(10);
            if(isset(request()->search) && !empty(request()->search)){
                    $search = request()->search;
                    if(Auth::user()->hasAnyRole('vendor')){
                        $data['reviews'] = Review::whereHas('user', function($q) use($search){
                            $q->where('name', 'Like', '%'.$search.'%');
                        })->orWhereHas('deliveryBoy', function($d) use($search){
                            $d->where('name', 'Like', '%'.$search.'%');
                        })->orWhere('rating', 'Like', '%'.request()->search.'%')
                        ->orWhere('comment', 'Like','%'.request()->search.'%')
                        ->orWhere('shop_id' ==  Auth::user()->shop->id)
                        ->latest()->paginate(10);}
                    else {
                    $data['reviews'] = Review::whereHas('user', function($q) use($search){
                                            $q->where('name', 'Like', '%'.$search.'%');
                                        })->orWhereHas('deliveryBoy', function($d) use($search){
                                            $d->where('name', 'Like', '%'.$search.'%');
                                        })->orWhere('rating', 'Like', '%'.request()->search.'%')
                                        ->orWhere('comment', 'Like','%'.request()->search.'%')
                                        ->latest()->paginate(10);
                                    }
                }
            return view('reviews.review_table', array('reviews' => $data['reviews']))->render();
        }else{
            $data['reviews'] = Review::with('user', 'deliveryBoy')->latest()->paginate(10);
        }
        return view('reviews.review', $data);
    
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        //
    }
}