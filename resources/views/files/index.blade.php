@extends('layouts.master')

@section('content')

<div class="w3-container w3-padding w3-card-4 w3-round-large w3-margin w3-white">
    <ul>
        @foreach($files as $file)
        <li>
            <a href="files/{{$file->id}}">{{$file->filename}}</a>
        </li>
        @endforeach
    </ul>
</div>

@endsection