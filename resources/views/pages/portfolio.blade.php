@extends('layouts.app')

@section('title', 'Portfolio')

@section('content')
<section class="portfolio-section">
    <div class="section-header">
        <h1>My Projects</h1>
        <div class="section-line"></div>
    </div>

    <div class="portfolio-filters">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="web">Web Development</button>
        <button class="filter-btn" data-filter="app">Applications</button>
    </div>

    <div class="row portfolio-grid">
        @foreach($projects as $index => $project)
        <div class="col-md-6 col-lg-4 portfolio-item" data-category="{{ $index % 2 == 0 ? 'web' : 'app' }}">
            <div class="project-card">
                <div class="project-image">
                    <img src="{{ asset($project['image']) }}" alt="{{ $project['title'] }}">
                    <div class="project-overlay">
                        <a href="{{ $project['url'] }}" class="project-link">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="project-info">
                    <h3>{{ $project['title'] }}</h3>
                    <p>{{ $project['description'] }}</p>
                    <div class="project-tech">
                        @foreach(explode(', ', $project['technologies']) as $tech)
                        <span class="tech-badge">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection
