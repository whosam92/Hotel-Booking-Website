<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class AboutController extends Controller
{
    public function index()
    {
        $about_data = Page::where('id',2)->first();
        $global_page_data = Page::first(); // Fetch page settings
        return view('front.about', compact('about_data', 'global_page_data'));
    }
}
