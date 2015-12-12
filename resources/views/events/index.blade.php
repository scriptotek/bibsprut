@extends('layouts.master')

@section('content')



<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	<h3>Arrangementer</h3>
  </div>
  <!--<div class="panel-body">
    <p>...</p>
  </div>-->

  <!-- List group -->
  <ul class="list-group">
    @foreach ($events as $event)
    <li class="list-group-item">
      <strong>{{ $event['title'] }}</strong>, {{ $event['start_date'] }},
      @if ($event->vortex_url)
        <a href="{{ $event->vortex_url }}">
          <em class="zmdi zmdi-link"></em>
          Vortex
        </a>
      @endif
      @if ($event->facebook_url)
        <a href="{{ $event->facebook_url }}">
          <em class="zmdi zmdi-link"></em>
          Facebook
        </a>
      @endif

      <a href="{{ action('EventsController@edit', $event->id) }}">
        <em class="zmdi zmdi-edit"></em>
        Rediger
      </a>

      @foreach ($event->presentations as $presentation)
        @if ($presentation->video)
          <div>
            {{ $presentation->start_time }}–{{ $presentation->end_time }}: <a href="{{ $presentation->video->link() }}">Opptak på YouTube</a>
          </div>
        @endif

      @endforeach
  </li>

@endforeach
</ul>
</div>


@endsection