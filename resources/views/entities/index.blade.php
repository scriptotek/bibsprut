@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Entities by type</h2>

        @foreach($entity_types as $entity_type => $entities)

        <h3>{{ $entity_type ? $entity_type : '(unknown)' }}</h3>
        <ul>
            @foreach($entities as $entity)
                <li>
                    <a href="{{ action('EntitiesController@show', $entity->id) }}">{{ $entity->entity_label }}</a>
                    ({{ count($entity->videos) }} videos)
                </li>
            @endforeach
        </ul>
        @endforeach

    </div>
@endsection
