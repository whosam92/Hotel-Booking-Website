<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Page;

class VideoController extends Controller
{
    public function index()
    {
        $video_all = Video::paginate(12);
        $global_page_data = Page::first(); // Fetch page settings
        return view('front.video_gallery', compact('video_all' ,'global_page_data'));
    }
}
