@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('content')
@php $supplier = new App\Models\Supplier(); @endphp
@include('suppliers.form')
@endsection
