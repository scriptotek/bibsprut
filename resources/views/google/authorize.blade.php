
@extends('layouts.master')

@section('content')

<h3>Google authorization required</h3>

<p>
    You need to <a href="{{ $authUrl }}">authorize access</a> before proceeding.
<p>

@endsection