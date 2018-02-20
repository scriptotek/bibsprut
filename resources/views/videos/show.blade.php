@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>{{ $video->yt('title') }}</h2>

        <p>
            {{ $video->yt('description') }}
        </p>
        <p>
            Dato: {{ $video->start_time }} - {{ $video->end_time }}
        </p>

        <p>
            Lenker:
            @if ($video->vortexEvent)
                <a href="{{ $video->vortexEvent->url }}">Arrangement (Vortex)</a>
            @else
                (lenke til Vortex mangler)
            @endif

            /

            <a href="{{ $video->youtubeLink() }}">YouTube</a>
        </p>


        <iframe width="560" height="315" src="{{ $video->youtubeLink('embed') }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>

        <tag-editor
            video-id='{{ $video->youtube_id }}'
            property-data='{{ json_encode($tagRoles, JSON_UNESCAPED_UNICODE) }}'
            tag-data='{{ json_encode($tags, JSON_UNESCAPED_UNICODE) }}'></tag-editor>
    </div>
@endsection
