<!DOCTYPE html>
<html lang="en">
<head>
    @stack('title')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Khula:300,400,700" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <style>
    @stack('styles')
    </style>
</head>
<body>
    <main class="py-2">
        @stack('body')
    </main>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    @stack('scripts')
    </script>
</body>
</html>