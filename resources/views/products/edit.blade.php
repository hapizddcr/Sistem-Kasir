@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content')
@include('products.form', ['product' => $product])
@endsection
