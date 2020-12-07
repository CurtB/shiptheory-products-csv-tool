<?php
    if(empty($page_meta['title'])){
        $page_meta['title'] = "Shiptheory product import";
    }

    if(empty($page_meta['description'])){
        $page_meta['description'] = "Import products to Shiptheory.com using a CSV file.";
    }

    if(empty($page_meta['keywords'])){
        $page_meta['keywords'] = "shiptheory, ship, theory, product, csv, import, export";
    }else{
        $page_meta['keywords'] = $page_meta['keywords'].", shiptheory, ship, theory, product, csv, import, export";
    }

    $fonts['heading'] = "Contrail One";
    $fonts['body'] = "Open Sans";
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="{{ $page_meta['description'] }}">
        <meta name="keywords" content="{{$page_meta['keywords']}}">
        <meta name="author" content="Curtis Barnett">

        <title>{{$page_meta['title']}}</title>

        @include('components.favicon')

        <meta property="og:title" content="{{$page_meta['title']}}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{URL::asset('images/shiptheory-logo.png')}}" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- Bootstrap -->
        <!-- <script src="https://unpkg.com/@popperjs/core@2"></script> -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


        <link href="{{ asset('css/normalize.css') }}" rel="stylesheet">
        <link href="{{ asset('css/skeleton.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">

        <?php // Page specific scripts
        // <script src="{{ asset('js/app.js') }}" defer></script>
        if(!empty($view_name)){
            if(file_exists(public_path('js/'.$view_name.'.js'))){echo('
                <script src="'.asset("js/".$view_name.".js").'" defer></script>
            ');}
            if(!empty($head_scripts)){ foreach($head_scripts as $head_script){ echo('
                '.$head_script.'');
            }}
        }
        ?>

        @yield('style')
    </head>
    <body>


    @include('components.navbar')
    <x-flash_message />
    @yield('content')
    @include('components.footer')

    @yield('script')

    </body>
</html>
