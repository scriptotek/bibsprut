@extends('layouts.master')

@section('content')

<h2>{{ $event->title}}</h2>

<p>
	<a href="{{ action('EventsController@edit', $event->id) }}">[Rediger]</a>
</p>

{!! $event->descriptionAsHtml() !!}


<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	<h3>
  		<a href="{{ $event->vortex_url }}">Vortex</a>
  	</h3>
  </div>

  <div class="panel-body">

  <div class="description">

    <p>
      <div style="float:right; margin-left:20px;background:#eee; border:1px solid #ccc;">
        <img src="https://www.ub.uio.no{{ $vortex->properties->picture }}">
        <div style="padding:3px;">{!! isset($vortex->properties->caption) ? $vortex->properties->caption : ''  !!}</div>
      </div>

    	Fra: {{ $vortex->properties->{"start-date"} }}. Til: {{ isset($vortex->properties->{"end-date"}) ? $vortex->properties->{"end-date"} : '???' }}<br>
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
  </div>

  </div>
</div>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	<h3>Program / presentasjoner</h3>
  </div>

  <!-- List group -->
  <ul class="list-group">
	  @foreach ($event->presentations as $presentation)
	  <li class="list-group-item">
		<div>Start: .. Slutt: ..</div>
		<div>Personer: ...</div>

		<h4>Opptak</h4>

		@if ($presentation->video)
			<div>
				Offentlig: {{ $presentation->video->is_public ? 'ja' : 'nei' }}, 
				Lisens: {{ $presentation->video->license }},
				ID: {{ $presentation->video->youtube_id }}
			</div>

			<div>
				@foreach ($presentation->video->tags as $tag)
			  	  <a href="#" class="label label-success" style="display:inline-block;">{{ $tag }}</a>
			  	@endforeach

			  	@foreach ($presentation->video->playlists as $plist)
			  	  <a href="#" class="label label-info" style="display:inline-block;">{{ $tag }}</a>
			  	@endforeach
			</div>

		<div style="margin-top:1em;">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $presentation->video->youtube_id }}" frameborder="0" allowfullscreen></iframe>
		</div>

		@else
		<em>No video available</em>
		@endif

	  </li>
	  @endforeach
  </ul>

  <div class="panel-footer">
  	<a href="#">[Legg til]</a>
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