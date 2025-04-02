<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>{{ config('app.name', 'RCMS') }}</title>
    <link href="{{asset('/assets/favicon/favicon.ico')}}" type="image/x-icon" rel="icon">
    <link href="{{asset('/assets/favicon/favicon.ico')}}" type="image/x-icon" rel="shortcut icon">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="{{asset('/vendors/simplebar/css/simplebar.css')}}">
    <link rel="stylesheet" href="{{asset('/css/vendors/simplebar.css')}}">
    <link rel="stylesheet" href="{{asset('/css/font-awesome/css/font-awesome.min.css')}}">
    <!-- Main styles for this application-->
    <link href="{{asset('/css/style.css')}}" rel="stylesheet">
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    <link href="{{asset('/css/examples.css')}}" rel="stylesheet">
    <link href="{{asset('/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #dataTable_length {
            margin-bottom: 20px !important;
        }

        .dataTables_paginate {
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
@include('layouts.include.sidebar')
<div class="wrapper d-flex flex-column min-vh-100 bg-light">
    @include('layouts.include.header')
    <div class="body flex-grow-1 px-3">
        @include('layouts.include.flash')
        @yield('content')
    </div>
    @include('layouts.include.footer')
</div>
<script src="{{asset('/vendors/@coreui/coreui/js/coreui.bundle.min.js')}}"></script>
<script src="{{asset('/js/jquery-1.8.2.min.js')}}"></script>
<script src="{{asset('/js/jquery.dataTable.min.js')}}"></script>
<script src="{{asset('/js/main.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#dataTable').dataTable({
            pagingType: "full_numbers",
            responsive: true,
        });
    });
</script>
@yield('script')
</body>
</html>