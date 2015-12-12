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
@foreach ($recordings as $recording)
  <li class="list-group-item{{ !array_get($recording->youtube_meta, 'is_public') ? ' disabled' : '' }}{{ in_array(array_get($recording->youtube_meta, 'broadcast_status'), ['created','ready']) ? ' list-group-item-warning' : '' }}">
  	<strong>{{ array_get($recording->youtube_meta, 'title') }}</strong>
    <a href="{{ $recording->youtubeLink('edit') }}" style="margin:0 0 0 10px;">
      <i class="zmdi zmdi-link"></i>
      Rediger på YouTube
    </a>
    @if (is_null($recording->presentation))
      <a href="{{ action('EventsController@create', ['from_recording' => $recording['id']]) }}" style="margin:0 0 0 10px;">
        <i class="zmdi zmdi-plus-circle-o"></i>
        Opprett arrangement
      </a>
    @else
      <a href="{{ action('EventsController@show', $recording->presentation->event->id) }}" style="margin:0 0 0 10px;">
        <i class="zmdi zmdi-link"></i>
        Arrangement
      </a>
    @endif
    <div>
      Recording date: {{ $recording->recorded_at ?: '(none)' }},
      Broadcast status: {{ array_get($recording->youtube_meta, 'broadcast_status') ?: '(none)' }},
      Public: {{ array_get($recording->youtube_meta, 'is_public') ? 'true' : 'false' }},
      Duration: {{ $recording->duration ?: '(none)' }},
      Views: {{ array_get($recording->youtube_meta, 'views') }}
    </div>

  	<div>
  	@foreach (array_get($recording->youtube_meta, 'tags', []) as $tag)
  	  <span class="label label-success">{{ $tag }}</span>
  	@endforeach

    @foreach (array_get($recording->youtube_meta, 'playlists', []) as $plist)
  	  <span class="label label-info">{{ $plist->title }}</span>
  	@endforeach
  	</div>
    <div class="description">
      {!! $recording->youtubeDescriptionAsHtml() !!}
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