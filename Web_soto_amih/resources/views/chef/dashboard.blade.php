<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard</title>
</head>
<body>
    <h1>Selamat Datang, chef! 👑</h1>
    <p>Anda login sebagai: <strong>{{ auth()->user()->name }}</strong></p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>