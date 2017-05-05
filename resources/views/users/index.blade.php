@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Brukere</h2>

        <ul>
            @foreach($users as $user)
                <li>
                    <a href="{{ action('UsersController@show', $user->id) }}">{{ $user->name }}</a>
                    ({{ $user->activate ? 'aktivert' : 'ikke aktivert' }})
                </li>
            @endforeach
        </ul>

    </div>
@endsection
