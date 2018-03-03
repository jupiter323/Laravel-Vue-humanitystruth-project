<?php
    $vision = "An open-source intelligence community promoting a decentralized economy of abundance for all humanity on earth by exposing suppressed knowledge.";
?>

<html lang="en">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{$vision}}">
    <title>Humanitys Truth</title>
    
    @include('layouts.head')

    @include('layouts.notification')
    
    @include('layouts.banner')
    
    @include('layouts.menubar')
    

    <body>
        
    	@yield('content')
        
    </body>

    @include('layouts.footer')
    
</html>








