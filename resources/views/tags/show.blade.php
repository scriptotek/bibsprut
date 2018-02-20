@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>#{{ $tag->tag_name }}</h2>
        <p>
            <a href="{{ action('TagsController@edit', $tag->id) }}">Rediger knagg</a>
        </p>
        <p>
            ID: {{ $tag->id }}
        </p>
        <p>
            Type: {{ $tag->tag_type ? $tag->tag_type : '(ukjent)' }}
        </p>
        <p>
            Sletta? {{ $tag->trashed() ? 'Ja' : 'Nei' }}
        </p>

        <h3>Bruk</h3>

        <p>
            Brukt på {{ count($tag->videos) }} video(er):
        </p>
        <ul>
            @foreach ($tag->videos as $video)
                <li>
                    som {{ $video->pivot->tagRole->label }} i
                    <a href="{{ action('VideosController@show', $video->youtube_id) }}">{{ $video->yt('title') }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
