<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @viteReactRefresh
        @vite([
            'resources/css/app.scss',
            'resources/js/index.jsx'
        ])

    </head>
    <body>
    <div id="app"/>
    </body>
</html>
