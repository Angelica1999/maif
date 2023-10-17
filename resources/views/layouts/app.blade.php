<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- base:css -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/typicons.font/font/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject --> 
    <!-- plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Lobibox/lobibox.css') }}"/>
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <!-- End plugin css for this page -->  
    <link rel="shortcut icon" href="{{ asset('images/doh-logo.png') }}" />
    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>
<body>
    <div id="app">
        <div class="container-scroller">
            @include('layouts.partials._navbar')
            <div class="container-fluid page-body-wrapper">
                {{-- @include('layouts.partials._settings-panel') --}}
                @include('layouts.partials._sidebar')
                <div class="main-panel">   
                    <div class="content-wrapper">
                        <div class="text-center p-2" style="background-color: #067536;width:100%;margin-bottom:30px;">
                            <img src="{{ asset('images/maip_banner_2023.png') }}" alt="banner"/>  
                        </div>   
                        <div class="row">
                            @yield('content')
                        </div>
                    </div>
                    @include('layouts.partials._footer')
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin/vendors/js/vendor.bundle.base.js') }}"></script>

    <script src="{{ asset('admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('admin/js/template.js') }}"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script src="{{ asset('admin/js/todolist.js') }}"></script>
    <script src="{{ asset('admin/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('Lobibox/lobibox.js?v=').date('His') }}"></script>
    
    <script>
        var path_gif = "{{ asset('images/loading.gif') }}";
        var loading = '<center><img src="'+path_gif+'" alt=""></center>';

        @if(session('patient_save'))
            <?php session()->forget('patient_save'); ?>
            Lobibox.notify('success', {
                msg: 'Successfully saved patient!'
            });
        @endif
        @if(session('fundsource_save'))
            <?php session()->forget('fundsource_save'); ?>
            Lobibox.notify('success', {
                msg: 'Successfully saved Fund Source!'
            });
        @endif
    </script>

    @yield('js')
</body>
</html>
