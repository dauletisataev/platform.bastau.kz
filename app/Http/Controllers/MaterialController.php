<?php

namespace App\Http\Controllers;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Storage;

class MaterialController extends Controller
{
    public function uploadFile(request $request) {
        $file = $request->file('file');
        $url = Storage::putFile('public', new File($file));
        $text = url('/').'/storage/'.substr($url,7);
        return $text;
    }
}
