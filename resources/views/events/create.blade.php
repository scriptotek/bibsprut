@extends('layouts.master')

@section('content')

@if (isset($id))
  <h2>Rediger arrangement</h2>
@else
  <h2>Nytt arrangement</h2>
@endif

<form method="POST" action="{{ isset($id) ? action('EventsController@update', $id) : action('EventsController@store') }}" class="form-horizontal">
  {!! csrf_field() !!}

  <div class="panel panel-default">
    <div class="panel-heading">Beskrivelse</div>
    <div class="panel-body">

          <div class="form-group">
            <label for="inputTitle" class="col-sm-2 control-label">Tittel</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="title" id="inputTitle" placeholder="Tittel" value="{{ old('title') ?: $title }}">
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-2" style="text-align:right;">
              <label for="inputIntro" class="control-label">Teaser</label>
              <div><a href="https://daringfireball.net/projects/markdown/" target="_blank">?  Markdown</a></div>
              <div><span id="ccIntro"></span> tegn, <span id="wcIntro"></span> ord</div>
            </div>
            <div class="col-sm-10">
              <textarea class="form-control" rows="6" name="intro" id="inputIntro" placeholder="Teaser">{{ old('intro') ?: $intro }}</textarea>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-2" style="text-align:right;">
              <label for="inputDescription" class="control-label">Beskrivelse</label>
              <div><a href="https://daringfireball.net/projects/markdown/" target="_blank">? Markdown</a></div>
              <div><span id="ccDescription"></span> tegn, <span id="wcDescription"></span> ord</div>
            </div>
            <div class="col-sm-10">
              <textarea class="form-control" rows="14" name="description" id="inputDescription" placeholder="Beskrivelse">{{ old('description') ?: $description }}</textarea>
            </div>
          </div>

          <p>
            (Tittel og beskrivelse på engelsk også?)
          </p>

          <div class="form-group">
            <label for="inputStartDate" class="col-sm-2 control-label">Dato</label>
            <div class="col-sm-3">
              <div class="input-group date">
                <input type="text" class="form-control" name="start_date" id="inputStartDate" value="{{ old('start_date') ?: $start_date }}"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="inputLocation" class="col-sm-2 control-label">Sted</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" name="location" id="inputLocation" value="{{ old('location') ?: $location }}">
            </div>
          </div>

          <div class="form-group">
            <label for="inputPerson1" class="col-sm-2 control-label">Organisert av</label>

            <div class="col-sm-10 help-block">
              TODO: En eller flere, se vortex, må sjekke mot tidligere innlagte, autocomplete.
            </div>

          </div>

          <div class="form-group">
            <label for="inputVortexUrl" class="col-sm-2 control-label">Vortex</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" name="vortex_url" id="inputVortexUrl" placeholder="Vortex-URL" value="{{ old('vortex_url') ?: $vortex_url }}">
            </div>
            <div class="col-sm-3 help-block">(for eksisterende. Hm, norsk/engelsk)</div>
          </div>

          <div class="form-group">
            <label for="inputFacebookId" class="col-sm-2 control-label">Facebook</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="facebook_id" id="inputFacebookId" placeholder="Facebook Event ID" value="{{ old('facebook_id') ?: $facebook_id }}">
            </div>
          </div>

          <div class="form-group">
            <label for="inputYouTubePlayList" class="col-sm-2 control-label">YouTube</label>
            <div class="col-sm-10">

              {!! Form::select('youtube_playlist_id',
                $youtube_playlists,
                old('youtube_playlist_id') ?: $youtube_playlist_id,
              ['class' => 'form-control', 'id' => 'inputYouTubePlayList']
              ) !!}
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Titan</label>
            <div class="col-sm-10 help-block">
              Tja… trenger man å velge noe aktivt?
              <!--<div class="checkbox">
                <label>
                  <input type="checkbox" name="titan" checked="checked">
                  Feed til Titan (når arrangementet er ferdig og opptak er lagt ut)
                </label>
              </div>
            </div>-->
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Identifier</label>
            <div class="col-sm-10 help-block">
                {{ $uuid ?: '(not set yet)' }}
            </div>
          </div>


    </div>
</div>


<div class="panel panel-default">
  <div class="panel-heading">Presentasjon 1</div>
  <div class="panel-body">

    <!--################## Personer ##################-->
    <div class="form-group">
      <label for="inputPerson1" class="col-sm-2 control-label">Person(er):</label>

      <div class="col-sm-10">

          <div class="input-group">

            <div class="input-group-btn">

                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">foredragsholder <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><a href="#">foredragsholder</a></li>
                  <li><a href="#">innleder</a></li>
                  <li><a href="#">tilfeldig person</a></li>
                </ul>
            </div><!-- /input-group-btn -->
            <input disabled="disabled" type="text" class="form-control" name="p1_person1" id="inputPerson1" placeholder="Navn (forslag fra BARE, VIAF, ISNI, Wikidata, Cristin?)" value="{{ old('p1_person1') ?: $p1_person1 }}">
          </div>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
            <button type="button" class="btn btn-success disabled" disabled="disabled">+</button>
      </div>
    </div>

    <!-- ################## Tidspunkt ##################-->
    <div class="form-group">
      <label for="inputP1StartTime" class="col-sm-2 control-label">Start:</label>
      <div class="col-sm-2">
        <input type="text" class="form-control" name="p1_start_time" id="inputP1StartTime" value="{{ old('p1_start_time') ?: $p1_start_time }}">
      </div>
      <label for="inputP1EndTime" class="col-sm-1 control-label">Slutt:</label>
      <div class="col-sm-2">
        <input type="text" class="form-control" name="p1_end_time" id="inputP1EndTime" value="{{ old('p1_end_time') ?: $p1_end_time }}">
      </div>
    </div>


    <!-- ################## Stream/Opptak ##################-->
    <div class="form-group">
      <label for="inputP1YoutubeId" class="col-sm-2 control-label">YouTube:</label>
      <div class="col-sm-2">
        <input type="text" class="form-control" name="p1_youtube_id" id="inputP1YoutubeId" placeholder="Eksisterende ID" value="{{ old('p1_youtube_id') ?: $p1_youtube_id }}">
      </div>
      <div class="col-sm-3 help-block">(video eller spilleliste) eller</div>
      <div class="col-sm-5">
          <div class="checkbox disabled">
            <label>
              <input type="checkbox" name="p1_youtube_create" disabled="disabled">
              Opprett planlagt direktesending ved lagring
            </label>
          </div>
      </div>
    </div>

  </div>
</div>

  <button type="submit" class="btn btn-primary">Lagre</button>
</form>

@endsection

@section('script')

<script>

$(function() {

  $('#inputStartTime').timepicker({
    timeFormat: 'H:i',
    step: 15   // 15 minutes
  });
  $('#inputEndTime').timepicker({
    timeFormat: 'H:i',
    step: 15   // 15 minutes
  });
  $('.input-group.date').datepicker({
    format: "yyyy-dd-mm",
    weekStart: 1,
    language: "no",
    autoclose: true
  });

  function introWc() {
    $('#ccIntro').text($('#inputIntro').val().length);
    $('#wcIntro').text(($('#inputIntro').val().match(/\S+/g) || []).length);
  }
  function descriptionWc() {
    $('#ccDescription').text($('#inputDescription').val().length);
    $('#wcDescription').text(($('#inputDescription').val().match(/\S+/g) || []).length);
  }
  $('#inputIntro').on('input', introWc);
  $('#inputDescription').on('input', descriptionWc);
  introWc();
  descriptionWc();

});

</script>
@endsection