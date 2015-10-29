@extends('layouts.master')

@section('content')

<h2>Nytt arrangement</h2>

<form method="POST" action="{{ action('EventsController@store') }}">
  {!! csrf_field() !!}


  <div class="panel panel-default">
    <div class="panel-heading">Beskrivelse</div>
    <div class="panel-body">

          <div class="form-group">
            <label for="inputTitle">Tittel</label>
            <input type="text" class="form-control" name="title" id="inputTitle" placeholder="Tittel" value="{{ old('title') ?: $title }}">
          </div>

          <div class="form-group">
            <label for="inputDescription">Beskrivelse</label>
            <textarea class="form-control" rows="10" name="description" id="inputDescription" placeholder="Beskrivelse">{{ old('description') ?: $description }}</textarea>
          </div>

          <p>
            (Trenger vi både kort og lang beskrivelse?)
          </p>

          <p>
            (Tittel og beskrivelse på engelsk også?)
          </p>


          <div class="form-group">
            <label for="inputVortexUrl">Vortex-URL</label> (for eksisterende. Hm, norsk/engelsk)
            <input type="text" class="form-control" name="vortex_url" id="inputVortexUrl" placeholder="Vortex-URL" value="{{ old('vortex_url') ?: $vortex_url }}">
          </div>

          <div class="form-group">
            <label for="inputFacebookUrl">Facebook-URL</label>
            <input type="text" class="form-control" name="facebook_url" id="inputFacebookUrl" placeholder="Facebook-URL" value="{{ old('facebook_url') ?: $facebook_url }}">
          </div>

          <div class="form-group">
            <label for="inputPerson1">Organisert av</label>

            <div class="input-group">

              TODO: En eller flere, se vortex, må sjekke mot tidligere innlagte, autocomplete.
            </div>

          </div>
    </div>
  </div>


<div class="panel panel-default">
  <div class="panel-heading">Presentasjon 1</div>
  <div class="panel-body">

    <div class="form-group">
      <label for="inputYoutubeId">YouTube-ID</label> (Video eller spilleliste)
       <div class="radio">

          <label class="radio-inline">
            <input type="radio" id="inlineradio1" value="option1" name="youtubeOption"> Intet opptak
          </label>
          <label class="radio-inline">
            <input type="radio" id="inlineradio2" value="option2" name="youtubeOption"> Opprett Youtube-stream
          </label>
          <label class="radio-inline">
            <input type="radio" id="inlineradio3" value="option3" name="youtubeOption" checked="checked"> Bruk eksisterende: 
          </label>

      <input type="text" class="form-control" name="youtube_id" id="inputYoutubeId" placeholder="YouTube-ID" value="{{ old('youtube_id') ?: $youtube_id }}" style="display:inline-block;width:170px;">
      (Kan vel egentlig være en selectbox)
      </div>
    </div>

    <div class="form-group">
      <label for="inputPerson1">Person(er)</label> (forslag fra BARE, VIAF, ISNI, Wikidata, Cristin?)

      <div class="input-group">

        <div class="input-group-btn">

            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">foredragsholder <span class="caret"></span></button>
            <ul class="dropdown-menu">
              <li><a href="#">foredragsholder</a></li>
              <li><a href="#">innleder</a></li>
              <li><a href="#">tilfeldig person</a></li>
            </ul>
        </div><!-- /input-group-btn -->
        <input type="text" class="form-control" name="person1" id="inputPerson1" placeholder="Navn" value="{{ old('person_1') ?: $person_1 }}">
      </div>

    </div>

    <button type="button" class="btn btn-success">+</button>

   </div>
  </div>

<div class="panel panel-default">
  <div class="panel-heading">Bilder</div>
  <div class="panel-body">
    YouTube-thumbnail, Vortex/Titan-thumbnail... Husk kreditering
  </div>
</div>


  <!--
  <div class="form-group">
    <label for="exampleInputFile">File input</label>
    <input type="file" id="exampleInputFile">
    <p class="help-block">Example block-level help text here.</p>
  </div>
  <div class="checkbox">
    <label>
      <input type="checkbox"> Check me out
    </label>
  </div>
  -->
  <button type="submit" class="btn btn-primary">Lagre</button>
</form>

@endsection