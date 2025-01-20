<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ImageByUrlController extends Controller
{
    use ValidatesRequests;
    /**
     * Отображение формы загрузки изображения по URL.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('imageByUrl');
    }

    /**
     * Handle image upload by URL.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadByUrl(Request $request): RedirectResponse
    {

        // Валидация URL и текста
        $this->validate($request, [
            'imageUrl' => 'required|url',
            'imageText' => 'required|string|max:255',
            'min_width' => 'required|integer|min:200',
            'min_height' => 'required|integer|min:200',
        ]);

        $imageUrl = $request->input('imageUrl');
        $imageText = $request->input('imageText');
        $minWidth = $request->input('min_width');
        $minHeight = $request->input('min_height');

        // Загружаем изображение по ссылке
        $response = Http::get($imageUrl);

        // Проверка, что ответ успешен и это изображение
        if ($response->successful() && strpos($response->header('Content-Type'), 'image/') === 0) {
            try {
                // Создаем объект изображения
                $image = Image::read($response->body());

                // Проверка минимальных размеров изображения
                if ($image->width() < $minWidth || $image->height() < $minHeight) {
                    return back()->withErrors("The image should be at least {$minWidth}x{$minHeight}px.");
                }

                // Изменяем изображение по высоте до 200px, сохраняя пропорции
                $image->resize(null, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // Обрезаем изображение до квадрата 200x200px
                $image->crop(200, 200);

                // Накладываем текст на изображение
                $image->text($imageText, 100, 100, function ($font) {
                    $font->file(public_path('fonts/Arial.ttf'));
                    $font->size(20);
                    $font->color('#000000');
                    $font->align('center');
                    $font->valign('center');
                });

                // Сохраняем изображение в публичную папку
                $imageName = time() . '-' . basename($imageUrl);
                $destinationPath = public_path('images/');
                $image->save($destinationPath . $imageName);

                // Генерация миниатюры
                $destinationPathThumbnail = public_path('images/thumbnail/');
                $image->resize(200, 200);
                $image->save($destinationPathThumbnail . $imageName);

                return back()
                    ->with('success', 'Загрузка изображения прошла успешно')
                    ->with('imageName', $imageName);
            } catch (\Exception $e) {
                return back()->withErrors('Error processing the image: ' . $e->getMessage());
            }
        } else {
            return back()->withErrors('Не удалось загрузить изображение. Пожалуйста, проверьте URL-адрес или тип изображения.');
        }
    }
}
