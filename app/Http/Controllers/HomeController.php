<?php

namespace App\Http\Controllers;

use App\TimeAgo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     * @throws \Exception
     */
    public function index()
    {
        $price_list = (array) json_decode(cache()->get('price-list') ?? '[]');

        return view('home', [
            'last_upload' => TimeAgo::get(cache()->get('last_upload')),
            'last_request' => TimeAgo::get(cache()->get('last_request')),
            'price_items_amount' => count($price_list, COUNT_RECURSIVE) - count($price_list),
            'price_categories_amount' => count($price_list),
            'price_list' => $price_list,
        ]);
    }
}
