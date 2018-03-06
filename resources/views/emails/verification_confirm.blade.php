@extends('layouts.master')
@section('content')
<div class="w3-container w3-padding w3-card-4 w3-round-large w3-margin w3-white">
    <strong>Registration Confirmed</strong>
    <div>
        <p>Your Email is successfully verified. Click here to <a href="{{url('/login')}}">login</a></p>
    </div>
</div>
@endsection