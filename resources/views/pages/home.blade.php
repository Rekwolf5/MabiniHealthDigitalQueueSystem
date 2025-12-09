@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="hero-section">
    <div class="row align-items-center">
        <div class="col-lg-6 hero-content">
            <h1 class="hero-title">Hello, I'm <span class="highlight">Jan Christopher</span></h1>
            <p class="hero-subtitle">IT Student & Cloud Computing Enthusiast</p>
            <p class="hero-text">I'm passionate about creating innovative solutions with modern technologies, focusing on cloud computing, web development, and cybersecurity.</p>
            <div class="hero-buttons">
                <a href="{{ route('about') }}" class="btn btn-primary btn-lg">About Me</a>
                <a href="{{ route('portfolio') }}" class="btn btn-outline-primary btn-lg">View My Work</a>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <div class="profile-image-container">
                <img src="{{ asset('images/profile1.jpg') }}" alt="My Profile Picture" class="profile-image">
                <div class="floating-shape shape-1"></div>
                <div class="floating-shape shape-2"></div>
                <div class="floating-shape shape-3"></div>
            </div>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-code-slash"></i></div>
                <h3 class="stat-number">3+</h3>
                <p class="stat-title">Years Coding</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-laptop"></i></div>
                <h3 class="stat-number">10+</h3>
                <p class="stat-title">Projects</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-award"></i></div>
                <h3 class="stat-number">3</h3>
                <p class="stat-title">Certifications</p>
            </div>
        </div>
    </div>
</section>
@endsection
