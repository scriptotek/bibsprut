@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Entity relation types</h2>

        <ul>
            @foreach($entityRelations as $entityRelation)
                <li>
                    <a href="{{ action('RelationController@show', $entityRelation->id) }}">{{ $entityRelation->label }}</a>
                    : <em>{{ $entityRelation->description }}</em>
                </li>
            @endforeach
        </ul>
        <a href="{{ action('RelationController@create') }}">+ Opprett ny</a>
    </div>
@endsection
