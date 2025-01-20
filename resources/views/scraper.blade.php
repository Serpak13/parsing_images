<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Парсинг изображений</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Парсинг изображений с сайта</h3>
    <form action="{{ route('scrape') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="siteUrl" class="form-label">Ссылка на сайт</label>
            <input type="url" class="form-control" id="siteUrl" name="siteUrl" required>
        </div>
        <div class="mb-3">
            <label for="imageText" class="form-label">Текст для наложения</label>
            <input type="text" class="form-control" id="imageText" name="imageText" required>
        </div>
        <div class="mb-3">
            <label for="min_width" class="form-label">Минимальная ширина</label>
            <input type="number" class="form-control" id="min_width" name="min_width" value="200" required>
        </div>
        <div class="mb-3">
            <label for="min_height" class="form-label">Минимальная высота</label>
            <input type="number" class="form-control" id="min_height" name="min_height" value="200" required>
        </div>
        <button type="submit" class="btn btn-primary">Скачать</button>
    </form>

    @if (count($errors) > 0)
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
</body>
</html>
