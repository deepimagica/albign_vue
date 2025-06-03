<!DOCTYPE html>
<html lang="en" class="page-html">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $_ENV['APP_NAME'] }}</title>
    <link href="{{ public_path('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_path('assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_path('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
</head>

