@extends('layouts.master')

@section('content')



<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	<h3>YouTube-videoer</h3>
  </div>
  <!--<div class="panel-body">
    <p>...</p>
  </div>-->

  <!-- List group -->
  <ul class="list-group">
@foreach ($videos as $video)
  <li class="list-group-item">
  	<strong>{{ $video['title'] }}</strong>
    (<a href="https://www.youtube.com/watch?v={{ $video['youtube_id'] }}">YouTube</a>)
  	<div>
  	@foreach ($video->tags as $tag)
  	  <span class="label label-success">{{ $tag }}</span>
  	@endforeach

  	@foreach ($video->playlists as $plist)
  	  <span class="label label-info">{{ $plist->title }}</span>
  	@endforeach
  	</div>
    {!! $video->descriptionAsHtml() !!}
    <div>
    @if (is_null($video->presentation))
    <a href="{{ action('EventsController@getCreate', ['videoId' => $video['id']]) }}" class="btn btn-primary">Opprett arrangement</a>
    @endif
    </div>
  </li>

@endforeach
</ul>
</div>


@endsection