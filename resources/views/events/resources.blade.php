@extends('layouts.master')

@section('content')

    <h2>Ressurser</h2>

    <p>
        For hendelsen: <a href="{{ action('EventsController@show', $event->id) }}">{{ $event->title }}</a>
    </p>

    <form action="{{ action('EventsController@storeResource', $event->id) }}"
          class="dropzone"
          id="my-awesome-dropzone">
        {!! csrf_field() !!}
    </form>

    <hr>

    <form method="POST" action="{{ action('EventsController@updateResources', $event->id) }}">
        {!! csrf_field() !!}

        <table class="table">
        @foreach ($resources as $resource)
            <tr>
                <td>
                    <img src="{{ url('uploads/' . $resource->filename) }}" >
                </td>
                <td>
                    Attribution:
                    <input type="text" name="attribution_{{ $resource->id  }}" class="form-control" value="{{ old('attribution_' . $resource->id) ?: $resource->attribution }}">
                    <br>
                    License: <input type="text" name="license_{{ $resource->id  }}" class="form-control" value="{{ old('license_' . $resource->id) ?: $resource->license }}">
                    <br>
                    Image size: {{ $resource->width }}x{{ $resource->height }}
                </td>
            </tr>
        @endforeach
        </table>

        <button type="submit" class="btn btn-primary">Lagre, ja</button>


    </form>

@endsection

@section('script')

    <script>

        $(function() {

            // "myAwesomeDropzone" is the camelized version of the HTML element's ID
            Dropzone.options.myAwesomeDropzone = {
                paramName: "file", // The name that will be used to transfer the file
                addRemoveLinks: true,
                acceptedFiles: 'image/jpg,image/jpeg,image/png',
                removedfile: function(file) {
                    console.log('Removed file, need to remove from server');
                    console.log(file.name);
                    return true;
                }
            };

        });

    </script>
@endsection