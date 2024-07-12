@extends('layouts.app')

@section('content')
    <h2>Welcome, {{ Auth::user()->name }}</h2>
    <p>Email: {{ Auth::user()->email }}</p>

@endsection