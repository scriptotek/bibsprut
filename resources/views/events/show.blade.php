@extends('layouts.master')

@section('content')

<h2>{{ $event->title}}</h2>

<p>
	<a href="{{ action('EventsController@edit', $event->id) }}">[Rediger]</a>
  <a href="{{ action('EventsController@editResources', $event->id) }}">[Ressurser]</a>
</p>

<p>
  Dato: {{ $event->start_date }}, identifier: {{ $event->uuid }} / {{ $event->sha1() }},
  <a href="{{ $event->archiveLink() }}">Archive</a>
</p>

<strong>
{!! $event->teaserAsHtml() !!}
</strong>

{!! $event->descriptionAsHtml() !!}


@if ($event->youtubePlaylist)
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <h3>
      YouTube-spilleliste
    </h3>
  </div>

  <div class="panel-body">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list={{ $event->youtubePlaylist->youtube_id }}" frameborder="0" allowfullscreen></iframe>
  </div>
</div>
@endif

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	<h3>Program / presentasjoner</h3>
  </div>

  <!-- List group -->
  <ul class="list-group">
	  @foreach ($event->presentations as $presentation)
	  <li class="list-group-item">
		<div>Start: {{ $presentation->start_time }} Slutt: {{ $presentation->end_time }}</div>
		<div>Personer: ...</div>

		<h4>Opptak</h4>

		@if ($presentation->recording)
			<div>
				Offentlig: {{ array_get($presentation->recording->youtube_meta, 'is_public') ? 'ja' : 'nei' }},
				Lisens: {{ array_get($presentation->recording->youtube_meta, 'license') }},
				ID: {{ $presentation->recording->youtube_id }}
			</div>

			<div>
				@foreach (array_get($presentation->recording->youtube_meta, 'tags', []) as $tag)
			  	  <a href="#" class="label label-success" style="display:inline-block;">{{ $tag }}</a>
			  	@endforeach

			  	@foreach ($presentation->recording->playlists as $plist)
			  	  <a href="#" class="label label-info" style="display:inline-block;">{{ $tag }}</a>
			  	@endforeach
			</div>

		<div style="margin-top:1em;">
			<iframe width="560" height="315" src="{{ $presentation->recording->youtubeLink('embed') }}" frameborder="0" allowfullscreen></iframe>
		</div>

		@else
		<em>Intet opptak tilgjengelig</em>
		@endif

	  </li>
	  @endforeach
  </ul>

  <div class="panel-footer">
  	<a href="#">[Legg til]</a>
  </div>


</div>


<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <h3>
      <a href="{{ $event->vortex_url }}">Vortex</a>
    </h3>
  </div>

  <div class="panel-body">

  <div class="description">

    @if ($vortex)

      <p>
        <div style="float:right; margin-left:20px;background:#eee; border:1px solid #ccc;">
          <img src="https://www.{{ $vortex->inst }}.uio.no{{ $vortex->properties->picture }}">
          <div style="padding:3px;">{!! isset($vortex->properties->caption) ? $vortex->properties->caption : ''  !!}</div>
        </div>

        Fra: {{ $vortex->properties->{"start-date"} }}.
          Til: {{ isset($vortex->properties->{"end-date"}) ? $vortex->properties->{"end-date"} : '???' }}<br>
          Sted: <a href="{{ $vortex->properties->mapurl }}">{{ $vortex->properties->location }}</a>
      <br>
        Organisert av:

      @foreach ($vortex->properties->organizers as $org)
        <a href="{{ $org->{'organizer-url'} }}" class="label label-danger" style="display:inline-block;">{{ $org->organizer }}</a>
      @endforeach
      <br>
      Nøkkelord:
      @if (isset($vortex->properties->tags))
            @foreach ($vortex->properties->tags as $tag)
              <a href="#" class="label label-success" style="display:inline-block;">{{ $tag }}</a>
            @endforeach
      @else
        <em>(ingen)</em>
      @endif

      </p>

      <hr>

      <div style="font-style: italic">
        {!! $vortex->properties->introduction !!}
      </div>

      <hr>

      <div>
      {!! $vortex->properties->content !!}
      </div>
    @else
      <em>Fant ikke Vortex-side</em>
    @endif
  </div>

  </div>
</div>

@endsection


@section('script')

<script type="text/javascript">
  $(function() {
    $('.description').readmore({
      speed: 100,
      collapsedHeight: 270,
      moreLink: '<a href="#"><em>Vis mer...</em></a>',
      lessLink: '<a href="#"><em>Vis mindre</em></a>'
    });
  });
</script>

@endsection