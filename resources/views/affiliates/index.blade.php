@extends('layouts.master')
<?php
    $vision = "An open-source intelligence community promoting a decentralized economy of abundance for all humanity on earth by exposing suppressed knowledge.";
?>
@section('content')

<div class="w3-container w3-padding w3-card-4 w3-round-large w3-margin w3-white">
    <h1>Affilates</h1>
    <div class="w3-container w3-padding w3-row align-center">
            @foreach($affiliates as $affiliate)
            <div class="w3-left w3-margin" style="width:150px;">
                <a href="{{$affiliate->website}}" target="_blank" >
                    <img class="" src="{{asset('uploads/'.$affiliate->logo)}}" width="150px" height="150px;">
                    <p class="w3-margin-bottom w3-center" >{{$affiliate->name}}</p>
                </a>
            </div>    
            @endforeach
    </div>
</div>    
        
@endsection