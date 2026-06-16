@extends('layouts.app')
@section('title', 'Edit Pelanggan')
@section('content')
@include('customers.form', ['customer' => $customer])
@endsection
