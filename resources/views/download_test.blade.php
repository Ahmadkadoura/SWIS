<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Download Test</title>
</head>
<body>
<h1>Test File Download</h1>
<form action="http://localhost:8000/api/files/downloader" method="post">
    <!-- Include CSRF token for Laravel -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <input type="hidden" name="api_token" value="{{ $token }}">

    <label for="url">File URL:</label><br>
    <input type="text" id="url" name="url" style="width: 100%; padding: 8px;" placeholder="Enter the file URL" required><br><br>

    <button type="submit" style="padding: 10px 20px;">Download File</button>
</form>
</body>
</html>
