@extends('layouts.app')

@section('content')
<div class="container">
    <div class="jumbotron text-center bg-light p-5 rounded mb-4 shadow-sm hover-pop">
        <h1 class="display-4 text-primary-dark fw-bold">Welcome to MediConnect</h1>
        <p class="lead">Your health, our priority. Book appointments with top specialists easily.</p>
        <a class="btn btn-primary-dark btn-lg px-5 shadow-sm" href="{{ route('sitemap') }}" role="button">
            <i class="bi bi-map me-2"></i>View Sitemap
        </a>
    </div>

    <h3 class="mb-4 text-primary-dark fw-bold">Latest Medical News</h3>
    <div class="row mb-5">
        @forelse($news as $article)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm hover-pop">
                    <div class="card-body">
                        <span class="badge bg-primary mb-3">News</span>
                        <h5 class="card-title fw-bold text-dark">{{ $article->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($article->content, 100) }}</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No news available at the moment.</div>
            </div>
        @endforelse
    </div>

    <h3 class="mb-4 text-primary-dark fw-bold">Health Information: Diseases, Preventions & Cures</h3>
    <div class="row">
        @forelse($healthInfo as $info)
            <div class="col-md-4 mb-4">
                <div class="card h-100 bg-white border-0 shadow-sm hover-pop">
                    <div class="card-body">
                        <span class="badge bg-info text-dark mb-3">{{ strtoupper($info->type) }}</span>
                        <h5 class="card-title fw-bold text-dark">{{ $info->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($info->content, 120) }}</p>
                        <a href="#" class="btn btn-sm btn-link p-0 text-decoration-none">Learn more <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No health information available at the moment.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection