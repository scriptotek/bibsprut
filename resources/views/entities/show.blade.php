@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>#{{ $entity->entity_label }}</h2>
        <p>
            <a href="{{ action('EntitiesController@edit', $entity->id) }}">Rediger knagg</a>
        </p>
        <p>
            ID: {{ $entity->id }}
        </p>
        <p>
            Type: {{ $entity->entity_type ? $entity->entity_type : '(ukjent)' }}
        </p>
        <p>
            Sletta? {{ $entity->trashed() ? 'Ja' : 'Nei' }}
        </p>

        <h3>Bruk</h3>

        <p>
            Brukt på {{ count($entity->videos) }} video(er):
        </p>
        <ul>
            @foreach ($entity->videos as $video)
                <li>
                    som {{ $video->pivot->entityRelation->label }} i
                    <a href="{{ action('VideosController@show', $video->youtube_id) }}">{{ $video->yt('title') }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
