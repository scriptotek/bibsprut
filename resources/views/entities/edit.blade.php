@extends('layouts.master')

@section('content')
    <div class="container">
        @if ($entity->id)
          <h2>Edit entity</h2>
        @else
          <h2>New entity</h2>
        @endif

        <form class="form-horizontal" action="{{ $entity->id ? action('EntitiesController@update', $entity->id) : action('EntitiesControllerer@store') }}" method="post" accept-charset="utf-8">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="{{ $entity->id ? 'PUT' : 'POST' }}">

          <div class="form-group">
            <label for="inputLabel" class="col-sm-2 control-label">Label</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="entity_label" id="inputLabel" placeholder="Label" value="{{ old('entity_label', $entity->entity_label) }}">
              <p class="help-block">Hvis du endrer verdien blir alle videoer oppdatert.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="inputDescription" class="col-sm-2 control-label">Type</label>
            <div class="col-sm-10">
              <entity-type-select
                name="entity_type"
                :values='{!! json_encode($entityTypes) !!}'
                value="{{ $entity->entity_type }}">
              </entity-type-select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-default">Lagre</button>
            </div>
          </div>
        </form>
    </div>
@endsection
