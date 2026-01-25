<!DOCTYPE html>
<html>
<head>
    <title>Mon App</title>
    @vite('resources/css/app.css')
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>