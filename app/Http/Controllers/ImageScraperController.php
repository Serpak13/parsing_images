<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;

class ImageScraperController extends Controller
{
    use ValidatesRequests;
    public function scrape(Request $request)
    {
        set_time_limit(300);
        // Валидация URL
        $this->validate($request, [
            'siteUrl' => 'required|url',
            'imageText' => 'required|string|max:255',
            'min_width' => 'required|integer|min:200',
            'min_height' => 'required|integer|min:200',
        ]);

        $siteUrl = $request->input('siteUrl');
        $imageText = $request->input('imageText');
        $minWidth = $request->input('min_width');
        $minHeight = $request->input('min_height');

        // Получение HTML страницы
        $response = Http::get($siteUrl);
        if (!$response->successful()) {
            return back()->withErrors('Не удалось загрузить сайт. Проверьте URL.');
        }

        $html = $response->body();

        // Парсинг HTML
        $crawler = new Crawler($html);

        // Поиск всех изображений
        $images = $crawler->filter('img')->each(function (Crawler $node) use ($siteUrl) {
            $src = $node->attr('src');
            if (!filter_var($src, FILTER_VALIDATE_URL)) {
                // Обработка относительных ссылок
                $src = rtrim($siteUrl, '/') . '/' . ltrim($src, '/');
            }
            return $src;
        });

        if (empty($images)) {
            return back()->withErrors('На указанной странице не найдено изображений.');
        }

        $processedImages = [];
        foreach ($images as $imageUrl) {
            // Загрузка изображения по ссылке
            $response = Http::get($imageUrl);
            if (!$response->successful() || strpos($response->header('Content-Type'), 'image/') !== 0) {
                continue;
            }

            try {

                $image = Image::read($response->body());

                // Проверка минимальных размеров изображения
                if ($image->width() < $minWidth || $image->height() < $minHeight) {
                    continue;
                }

                // Изменение размера и наложение текста
                $image->resize(null, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->crop(200, 200)->text($imageText, 100, 100, function ($font) {
                    $font->file(public_path('fonts/Arial.ttf'));
                    $font->size(20);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('center');
                });

                // Кодирование изображения в формат JPG
                $encodedImage = $image->encode();

                // Сохранение изображения в массив для скачивания
                $fileName = 'image_' . time() . '_' . md5($imageUrl) . '.jpg';
                $processedImages[] = [
                    'content' => $encodedImage,
                    'name' => $fileName,
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        if (empty($processedImages)) {
            return back()->withErrors('Не удалось обработать изображения.');
        }

        // Создание архива с изображениями
        $zip = new \ZipArchive();
        $zipFileName = 'processed_images_' . time() . '.zip';
        $zipFilePath = storage_path($zipFileName);

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
            foreach ($processedImages as $image) {
                $zip->addFromString($image['name'], $image['content']);
            }
            $zip->close();
        } else {
            return back()->withErrors('Не удалось создать архив.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend();
    }
}
