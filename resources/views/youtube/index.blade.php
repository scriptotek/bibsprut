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
    <a href="https://www.youtube.com/edit?video_id={{ $video['youtube_id'] }}" style="margin:0 0 0 10px;">
      <i class="zmdi zmdi-link"></i>
      Rediger på YouTube
    </a>
    @if (is_null($video->presentation))
      <a href="{{ action('EventsController@create', ['from_youtube_video' => $video['id']]) }}" style="margin:0 0 0 10px;">
        <i class="zmdi zmdi-plus-circle-o"></i>
        Opprett arrangement
      </a>
    @else
      <a href="{{ action('EventsController@show', $video->presentation->event->id) }}" style="margin:0 0 0 10px;">
        <i class="zmdi zmdi-link"></i>
        Arrangement
      </a>
    @endif

  	<div>
  	@foreach ($video->tags as $tag)
  	  <span class="label label-success">{{ $tag }}</span>
  	@endforeach

  	@foreach ($video->playlists as $plist)
  	  <span class="label label-info">{{ $plist->title }}</span>
  	@endforeach
  	</div>
    <div class="description">
      {!! $video->descriptionAsHtml() !!}
    </div>
  </li>

@endforeach
</ul>
</div>


@endsection

@section('script')

<script type="text/javascript">
  $(function() {
    $('.description').readmore({
      speed: 100,
      collapsedHeight: 100,
      moreLink: '<a href="#"><em>Vis mer...</em></a>',
      lessLink: '<a href="#"><em>Vis mindre</em></a>'
    });
  });
</script>

@endsection