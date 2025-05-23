<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/js/app.js'])

    {{-- for user css --}}
    <link href="{{ asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet"
        type="text/css" />
    @if (!Request::is('admin*'))
        <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/util.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jquery-confirm.min.css') }}" />

        <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/fengyuanchen.github.io_cropperjs_css_cropper.css') }}" rel="stylesheet"
            type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    @endif

    {{-- for admin css --}}
    @if (Request::is('admin*'))
        {{-- <link href="{{ asset('assets/css/admin/font-awesome.min.css') }}" rel="stylesheet" type="text/css" /> --}}
        {{-- <link href="{{ asset('assets/css/admin/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" /> --}}
        <link href="{{ asset('assets/css/admin/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/admin/uniform.default.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/admin/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/pages/css/login.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
        {{-- <link href="{{ asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    @endif

    <style>
        .inertia-progress-bar {
            display: none !important;
        }
    </style>

</head>

<body class="login">
    @inertia
</body>

<script src="{{ asset('assets/js/jquery-3.4.0.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/common_scripts.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/lucide.min.js') }}"></script>

<script src="{{ asset('assets/js/main.js') }} "></script>
<script src="{{ asset('assets/js/sweetalert.js') }} "></script>
<script src="{{ asset('assets/js/fengyuanchen.github.io_cropperjs_js_cropper.js') }} "></script>
<script src="{{ asset('assets/js/select2.js') }} "></script>
<script src="{{ asset('assets/js/jquery-confirm.min.js') }}"></script>
<script>
    function showErrorDialog(message) {
        $.confirm({
            title: 'Error',
            content: message,
            type: 'red',
            typeAnimated: true,
            buttons: {
                ok: {
                    text: 'OK',
                    btnClass: 'btn-red',
                    action: function() {}
                }
            }
        });
    }
</script>

</html>
