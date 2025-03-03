<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Page;

class PhotoController extends Controller
{
    public function index()
    {
        $photo_all = Photo::paginate(12);
        $global_page_data = Page::first(); // Fetch page settings

        return view('front.photo_gallery', compact('photo_all', 'global_page_data'));
    }
}
