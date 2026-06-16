@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('content')
@include('products.form', ['product' => new App\Models\Product()])
@endsection
