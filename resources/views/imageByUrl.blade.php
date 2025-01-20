<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image by URL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <h3 class="card-header">Upload Image by URL with Text</h3>
        <div class="card-body">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                <div>
                    <strong>Image:</strong><br>
                    <img src="/images/{{ session('imageName') }}" width="200px">
                </div>
            @endif

            <form action="{{ route('urlImageText.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="imageUrl" class="form-label">Image URL</label>
                    <input type="url" class="form-control" id="imageUrl" name="imageUrl" required>
                </div>
                <div class="mb-3">
                    <label for="imageText" class="form-label">Text to overlay on image</label>
                    <input type="text" class="form-control" id="imageText" name="imageText" required>
                </div>
                <div class="mb-3">
                    <label for="min_width" class="form-label">Min Width (200px)</label>
                    <input type="number" class="form-control" id="min_width" name="min_width" value="200" readonly>
                </div>
                <div class="mb-3">
                    <label for="min_height" class="form-label">Min Height (200px)</label>
                    <input type="number" class="form-control" id="min_height" name="min_height" value="200" readonly>
                </div>
                <button type="submit" class="btn btn-primary">Upload Image</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
