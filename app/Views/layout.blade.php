<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wizard Form</title>
    @stack('head')
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div>
    @yield('content')
</div>
@stack('scripts')
</body>
</html>