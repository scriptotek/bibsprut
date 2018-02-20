@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Entities by type</h2>

        @foreach($tag_types as $tag_type => $tags)

        <h3>{{ $tag_type ? $tag_type : '(unknown)' }}</h3>
        <ul>
            @foreach($tags as $tag)
                <li>
                    <a href="{{ action('TagsController@show', $tag->id) }}">{{ $tag->tag_name }}</a>
                    ({{ count($tag->videos) }} videoer)
                </li>
            @endforeach
        </ul>
        @endforeach

    </div>
@endsection
