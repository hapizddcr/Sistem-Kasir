@extends('layouts.app')
@section('title', 'Tambah User')
@section('content')
@include('users.form', ['user' => new App\Models\User()])
@endsection
