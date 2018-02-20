@extends('layouts.master')

@section('content')

  <img src="/octopus-33147_640.png" style="width:200px; float:right;">

        @if (isset($accounts))
        <div>
            Kontoer:
            @foreach ($accounts as $acc)
              <div style="display: inline-block;background:#eee;border-radius: 15px; padding-right:8px;">
                  <img src="{{ $acc->userinfo['picture'] }}" style="width:30px; border-radius:15px;">
                  {{ $acc->userinfo['name'] }}
                  @can('edit')
                  <a href="{{ action('GoogleAuthController@logout', ['email' => $acc->id]) }}">[X]</a>
                  @endcan
              </div>
            @endforeach
            @can('edit')
            <div style="display: inline-block;">
                <a href="{{ action('GoogleAuthController@initiate') }}">Legg til</a>
            </div>
            @endcan
        </div>
        @endif

        <div style="margin-top: 1em;">
            Siste fullstendige høsting:
            @if (isset($lastHarvest))
                @if ($lastHarvest->deleted_at)
                    {{ $lastHarvest->deleted_at->tz('Europe/Oslo')->formatLocalized('%d. %B %Y, %H:%M') }}
                @else
                    <em>startet {{ $lastHarvest->created_at->tz('Europe/Oslo') }} (last siden på nytt for å sjekke om den er ferdig)</em>
                @endif
            @else
                aldri
            @endif
        </div>
        <div style="margin-top: 1em;">
            @can('edit')
                @if (!isset($lastHarvest) || $lastHarvest->deleted_at)
                    <a class="btn btn-primary" href="{{ action('HarvestsController@harvest')  }}">Start ny høsting (tilkall blekkspruten)</a>
                @endif
            @endcan
            <a class="btn btn-default" href="{{ action('HarvestsController@log') }}">Logg</a>
        </div>
        <p style="margin-top: 1em;">
            Hvordan funker dette? Blekkio oppdaterer enkeltvideor når den får et ping fra YouTube, men det kan av og til bli krøll.
            Hvis det blir krøll, logg inn og trykk "Start ny høsting (tilkall blekkspruten)" for å gjøre en fullstendig
            høsting (tar 1-2 minutter) fulgt av en oppdatering av UB live. Fullstendige høstinger gjøres også automatisk 2 ganger i døgnet.
        </p>



  <!-- Default panel contents -->
  	<h3>Opptak</h3>

    <p>
      @if ($missingDate)
        <a href="{{ $urlWithoutMissingDate }}">Skjul opptak som mangler dato</a>
      @else
        <a href="{{ $urlWithMissingDate }}">Vis opptak som mangler dato</a>
      @endif
      |
      @if ($private)
        <a href="{{ $urlWithoutPrivate }}">Skjul private</a>
      @else
        <a href="{{ $urlWithPrivate }}">Vis private</a>
      @endif
    </p>



  <!--<div class="panel-body">
    <p>...</p>
  </div>-->

  <!-- List group -->
  <ul class="events">
@foreach ($events as $gid => $event)

  <li class="{{ count($event['recordings']) && $event['recordings'][0]->upcoming() ? ' list-group-item-warning' : '' }}">

  <div>

    @if (!is_null($event['vortexEvent']) && !is_null($event['vortexEvent']->thumbnail))
      <img src="{{ $event['vortexEvent']->thumbnail }}" style="width:260px; float:right;" title="Vortex thumb for Titan">
    @endif

    @if (!is_null($event['vortexEvent']))
      {{ $event['vortexEvent']->formatStartEndDateTime() }}
    @endif


    <strong>{{ $event['title'] }}</strong>

    @if ($event['publicVideos'] > 1 && isset($event['playlist']))

      <a href="{{ $event['playlist']->youtubeLink() }}" style="margin:0 0 0 10px;">
        <i class="zmdi zmdi-link"></i> YouTube (playlist)
      </a>

      @if ($event['vortexEvent'])
      <a href="{{ $event['vortexEvent']->url }}" style="margin:0 0 0 10px;">
        <i class="zmdi zmdi-link"></i>
        Arrangement
      </a>
      @endif

      ({{ $event['publicVideos']}} / {{ count($event['recordings'])}})

    @else

      @if ($event['publicVideos'] == 1)
        <a href="{{ $event['recordings'][0]->youtubeLink('edit') }}" style="margin:0 0 0 10px;">
          <i class="zmdi zmdi-link"></i>
          YouTube
        </a>
      @endif

      @if ($event['vortexEvent'])
          <a href="{{ $event['vortexEvent']->url }}" style="margin:0 0 0 10px;">
            <i class="zmdi zmdi-link"></i>
            Arrangement
          </a>
      @else
        <span style="background:#FF9F4F; color: white; display:inline-block; border-radius: 3px; padding: 0 3px; margin: 0 1px;">Lenke til arrangement mangler</span>
      @endif

    @endif
  </div>

<ul style="padding: 0;">
@foreach ($event['recordings'] as $recording)
<li class="video{{ !array_get($recording->youtube_meta, 'is_public') ? ' disabled' : '' }}">

    <div style="display:flex">

      <div style="flex: 0 0 140px; padding-right:1em;">
        <img src="{{ array_get($recording->youtube_meta, 'thumbnail') }}" style="width:100%;">
      </div>

      <div style="flex: 1 1 auto;">
        <strong><a href="{{ action('VideosController@show', $recording->youtube_id) }} ">{{ array_get($recording->youtube_meta, 'title') }}</a></strong>

{{--        @if (is_null($recording->presentation))
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
        --}}
        <div style="font-family: monospace;">
              @if ($recording->account)
                  <img src="{{ $recording->account->userinfo['picture'] }}" style="width:30px; border-radius:15px;">
              @endif

          @if (is_null($event['vortexEvent']) || ($event['vortexEvent']->formatStartEndDateTime() != $recording->formatStartEndDateTime()))
            {{ $recording->formatStartEndDateTime() }}
          @endif

          @if (!$recording->start_time)
              <span style="background:#FF9F4F; color: white; display:inline-block; border-radius: 3px; padding: 0 3px; margin: 0 1px;">YouTube start time not set</span>
          @endif
          {{--
          @if (!$recording->end_time)
              <span style="background:#FF9F4F; color: white; display:inline-block; border-radius: 3px; padding: 0 3px; margin: 0 1px;">YouTube end time not set</span>
          @endif
          --}}

          @if (array_get($recording->youtube_meta, 'duration'))
              {{ array_get($recording->youtube_meta, 'duration') ?: '(none)' }}
          @endif
          @if (array_get($recording->youtube_meta, 'views'))
            //
            Views: {{ array_get($recording->youtube_meta, 'views') }}
          @endif
          @if (isset($recording->playlist_position))
            //
            Playlist position: {{ $recording->playlist_position }}
          @endif

          @if ($recording->yt('is_public'))
              <span style="background:#56DF56; color: white; display:inline-block; border-radius: 3px; padding: 0 3px; margin: 0 1px;">Public</span>
          @else
              <span style="background:#FF9F4F; color: white; display:inline-block; border-radius: 3px; padding: 0 3px; margin: 0 1px;">Private</span>
          @endif

          @if ($recording->yt('broadcast_status') == 'created')
              <span style="background:#FF9F4F; color: white; display:inline-block; border-radius: 3px; padding: 0 3px; margin: 0 1px;">Check ingestion settings</span>
          @elseif ($recording->yt('broadcast_status') == 'ready')
              <span style="background:#56DF56; color: white; display:inline-block; border-radius: 3px; padding: 0 3px; margin: 0 1px;">Ready for broadcast</span>
          @endif

        @if (count($event['recordings']) > 1)
          <a href="{{ $recording->youtubeLink('edit') }}" style="margin:0 0 0 10px;"><i class="zmdi zmdi-link"></i> Rediger på YouTube</a>
        @endif

        @if (Auth::check())
          <a href="{{ action('VideosController@hide', $recording->id) }}"><i class="zmdi zmdi-delete"></i> Skjul</a>
        @endif
        </div>

        <div>
          @foreach ($recording->entities as $entity)
            <a class="label label-success" href="{{ action('EntitiesControllerller', $entity->id) }}">{{ $entity->entity_label }}</a>
          @endforeach

          @foreach ($recording->playlists as $plist)
            <span class="label label-info">{{ $plist->title }}</span>
          @endforeach
        </div>

        <div class="description">
          {!! $recording->youtubeDescriptionAsHtml() !!}
        </div>

      </div>
    </div>
    <div style="clear:both"></div>
</li>
@endforeach
</ul>
  </li>
@endforeach
</ul>


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
