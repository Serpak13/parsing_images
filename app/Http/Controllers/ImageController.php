<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Validation\ValidationException; // Добавьте это
class ImageController extends Controller
{

    use \Illuminate\Foundation\Validation\ValidatesRequests; // Добавьте этот трейт
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        return view('imageUpload');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = Image::read($request->file('image'));

        // Main Image Upload on Folder Code
        $imageName = time().'-'.$request->file('image')->getClientOriginalName();
        $destinationPath = public_path('images/');
        $image->save($destinationPath.$imageName);

        // Generate Thumbnail Image Upload on Folder Code
        $destinationPathThumbnail = public_path('images/thumbnail/');
        $image->resize(200,200);
        $image->save($destinationPathThumbnail.$imageName);

        /**
         * Write Code for Image Upload Here,
         *
         * $upload = new Images();
         * $upload->file = $imageName;
         * $upload->save();
         */

        return back()
            ->with('success','Image Upload successful')
            ->with('imageName',$imageName);
    }
}
