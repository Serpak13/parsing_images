<!DOCTYPE html>
<html>
<head>
    <title>Upload Image by URL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body>
<div class="container">
    <div class="card mt-5">
        <h3 class="card-header p-3"><i class="fa fa-upload"></i> Upload Image by URL</h3>
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

            @session('success')
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>

            <div class="row">
                <div class="col-md-4">
                    <strong>Original Image:</strong>
                    <br/>
                    <img src="/images/{{ session('imageName') }}" width="300px" />
                </div>
                <div class="col-md-4">
                    <strong>Thumbnail Image:</strong>
                    <br/>
                    <img src="/images/thumbnail/{{ session('imageName') }}" />
                </div>
            </div>
            @endsession

            <form action="{{ route('urlImage.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="imageUrl">Image URL:</label>
                    <input
                        type="text"
                        name="imageUrl"
                        id="imageUrl"
                        class="form-control @error('imageUrl') is-invalid @enderror">

                    @error('imageUrl')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
