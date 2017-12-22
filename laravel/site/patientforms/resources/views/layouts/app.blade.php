<!DOCTYPE html>
<html>
    <head>
        <title>Patient Forms · @yield('title')</title>
        <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">
    </head>
    <body>
        <div>
            @include('navbar')
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="container-fluid">
                            @yield('content')
                        </div>
                    </div>

                    <div class="col-lg-3 panel panel-default">
                        <div class="panel-body">
                            @section('sidebar')
                                This is the master sidebar.
                            @show
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
        <script src="{{ URL::asset('js/app.js') }}"></script>
    </body>
</html>