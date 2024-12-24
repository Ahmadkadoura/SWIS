<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<h1>User List</h1>
<table class="table table-bordered">
    <thead class="thead-light">
    <tr>
        <th>ID</th>
        <th>Name (EN)</th>
        <th>Name (AR)</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Created At</th>
        <th>Updated At</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->getTranslation('name', 'en') }}</td>
            <td>{{ $user->getTranslation('name', 'ar') }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
            <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
