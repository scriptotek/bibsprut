@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Video-entity relation types</h2>

        <ul>
            @foreach($tagRoles as $tagRole)
                <li>
                    <a href="{{ action('TagRoleController@show', $tagRole->id) }}">{{ $tagRole->label }}</a>
                    : <em>{{ $tagRole->description }}</em>
                </li>
            @endforeach
        </ul>
        <a href="{{ action('TagRoleController@create') }}">+ Opprett ny</a>
    </div>
@endsection
