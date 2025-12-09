@extends('layouts.app')

@section('title', 'Contact')

@section('content')
<section class="contact-section">
    <div class="section-header">
        <h1>Get In Touch</h1>
        <div class="section-line"></div>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="contact-info">
                <h2>Let's Connect</h2>
                <p class="lead">I'm always open to discussing new projects, creative ideas or opportunities to be part of your vision.</p>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Email</h3>
                            <p><a href="mailto:your.janchristophermanzano@gmail.com">janchristophermanzano@gmail.com</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="bi bi-linkedin"></i>
                        </div>
                        <div class="contact-details">
                            <h3>LinkedIn</h3>
                            <p><a href="https://www.linkedin.com/in/jan-christopher-manzano-2888bb288/" target="_blank">https://www.linkedin.com/in/jan-christopher-manzano-2888bb288/</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Location</h3>
                            <p>Pangasinan, Philippines</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="contact-form-container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your Name" value="{{ old('name') }}" required>
                        <label for="name">Your Name</label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Your Email" value="{{ old('email') }}" required>
                        <label for="email">Your Email</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" placeholder="Your Message" style="height: 150px" required>{{ old('message') }}</textarea>
                        <label for="message">Your Message</label>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
