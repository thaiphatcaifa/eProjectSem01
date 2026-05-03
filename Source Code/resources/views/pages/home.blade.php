@extends('layouts.app')

@section('content')
<div class="container">
    <div class="jumbotron text-center bg-light p-5 rounded mb-4">
        <h1 class="display-4">Welcome to MediConnect</h1>
        <p class="lead">Your health, our priority. Book appointments with top specialists easily.</p>
        <a class="btn btn-primary btn-lg" href="{{ route('sitemap') }}" role="button">View Sitemap</a>
    </div>

    <h3 class="mb-3">Latest Medical News</h3>
    <div class="row mb-5">
        @forelse($news as $article)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="badge bg-primary mb-2">News</span>
                        <h5 class="card-title">{{ $article->title }}</h5>
                        <p class="card-text">{{ Str::limit($article->content, 100) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p>No news available.</p>
        @endforelse
    </div>

    <h3 class="mb-3">Health Information: Diseases, Preventions & Cures</h3>
    <div class="row">
        @forelse($healthInfo as $info)
            <div class="col-md-4">
                <div class="card h-100 bg-light border-info">
                    <div class="card-body">
                        <span class="badge bg-info text-dark mb-2">{{ strtoupper($info->type) }}</span>
                        <h5 class="card-title">{{ $info->title }}</h5>
                        <p class="card-text">{{ Str::limit($info->content, 120) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p>No health information available.</p>
        @endforelse
    </div>
</div>
@endsection