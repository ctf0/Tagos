<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '')</title>

    {{-- styles --}}
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
    <link rel="stylesheet" href="{{ mix('assets/vendor/Tagos/style.css') }}">
</head>

<body>
    <section id="app" v-cloak>

        {{-- notif --}}
        <div class="notif-container">
            <my-notification></my-notification>
        </div>

        {{-- Body --}}
        <div class="container">
            <div class="columns">
                <div class="column">
                    @yield('content')
                </div>
            </div>
        </div>
    </section>

    {{-- app --}}
    <script src="{{ asset("path/to/app.js") }}"></script>
</body>
</html>