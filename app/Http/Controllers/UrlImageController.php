<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Http;

class UrlImageController extends Controller
{
    use \Illuminate\Foundation\Validation\ValidatesRequests;

    /**
     * Show form for uploading image by URL.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('urlImageUpload');
    }

    /**
     * Handle image upload by URL.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Валидация URL
        $this->validate($request, [
            'imageUrl' => 'required|url',
        ]);

        $imageUrl = $request->input('imageUrl');

        // Загружаем изображение по ссылке
        $response = Http::get($imageUrl);

        // Проверка, что ответ успешен и это изображение
        if ($response->successful() && strpos($response->header('Content-Type'), 'image/') === 0) {
            // Сохраняем изображение на сервер
            $imageName = time() . '-' . basename($imageUrl);
            $destinationPath = public_path('images/');
            $image = Image::read($response->body()); // Создаем объект изображения
            $image->save($destinationPath . $imageName);

            // Генерируем миниатюру
            $destinationPathThumbnail = public_path('images/thumbnail/');
            $image->resize(200, 200);
            $image->save($destinationPathThumbnail . $imageName);

            return back()
                ->with('success', 'Image Upload successful')
                ->with('imageName', $imageName);
        } else {
            return back()->withErrors('Failed to download the image. Please check the URL or the image type.');
        }
    }
}
