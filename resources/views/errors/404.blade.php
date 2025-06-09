@extends('layouts.app')

@section('title', 'Página não encontrada')

@section('content')
<div class="container text-center mt-5">
    <h1 class="display-4">404</h1>
    <p class="lead">A página que você está procurando não foi encontrada.</p>
    <a href="{{ route('web.products.index') }}" class="btn btn-primary">Voltar à página inicial</a>
</div>
@endsection