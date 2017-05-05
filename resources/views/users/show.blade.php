@extends('layouts.master')

@section('content')
    <div class="container">
        <h2><a href="/users">Brukere</a> : {{ $user->name }}</h2>

        <p>
            Feide-ID: {{ $user->feide_id }}<br>
            Aktivert: {{ $user->activate ? 'ja' : 'nei' }}
        </p>

    </div>
@endsection
