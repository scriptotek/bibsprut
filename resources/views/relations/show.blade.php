@extends('layouts.master')

@section('content')
    <div class="container">
    	<a href="{{ action('RelationController@index') }}">Entity relations</a>
        <h2>{{ $entityRelation->label }}</h2>
        <p>
        	<a href="{{ action('RelationController@edit', $entityRelation->id)}}">Rediger</a>
        </p>
        <p>{{ $entityRelation->description }}</p>
    </div>
@endsection
