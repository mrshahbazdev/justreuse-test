<!DOCTYPE html>
<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">       
        <script src="{{ URL::to('js/jquery-1.10.2.js') }}"></script>    
        <script src="{{ URL::to('js/jquery.min.js') }}"></script> 
        <title>Classified</title>
        @livewireScripts
    </head>
    <body class="antialiased">       
        <main>           
            <!--content start--> 
            @yield('content')
            <!--content end-->
        </main>       
    </body>
</html>
