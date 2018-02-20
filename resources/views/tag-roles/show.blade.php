@extends('layouts.master')

@section('content')
    <div class="container">
    	<a href="{{ action('TagRoleController@index') }}">Relasjoner</a>
        <h2>{{ $tagRole->label }}</h2>
        <p>
        	<a href="{{ action('TagRoleController@edit', $tagRole->id)}}">Rediger</a>
        </p>
        <p>{{ $tagRole->description }}</p>
    </div>
@endsection
