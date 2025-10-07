<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Image</title>
</head>
<body>
    <form method="POST" action="{{ url('test/upload-image/do-upload') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>