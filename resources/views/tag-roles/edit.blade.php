@extends('layouts.master')

@section('content')
    <div class="container">
        @if ($tagRole->id)
          <h2>Rediger relasjon</h2>
        @else
          <h2>Ny relasjon</h2>
        @endif

        <form class="form-horizontal" action="{{ $tagRole->id ? action('TagRoleController@update', $tagRole->id) : action('TagRoleController@store') }}" method="post" accept-charset="utf-8">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="{{ $tagRole->id ? 'PUT' : 'POST' }}">

          <div class="form-group">
            <label for="inputLabel" class="col-sm-2 control-label">Label</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="label" id="inputLabel" placeholder="Label" value="{{ old('label', $tagRole->label) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputDescription" class="col-sm-2 control-label">Description</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="description" id="inputDescription" placeholder="Description" value="{{ old('description', $tagRole->description) }}">
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