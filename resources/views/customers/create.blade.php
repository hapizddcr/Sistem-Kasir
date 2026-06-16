@extends('layouts.app')
@section('title', 'Tambah Pelanggan')
@section('content')
@include('customers.form', ['customer' => new App\Models\Customer()])
@endsection
