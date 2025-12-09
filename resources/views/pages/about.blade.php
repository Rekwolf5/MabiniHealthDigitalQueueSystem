@extends('layouts.app')

@section('title', 'About Me')

@section('content')
<section class="about-section">
    <div class="section-header">
        <h1>About Me</h1>
        <div class="section-line"></div>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="about-content">
                <h2>Who Am I</h2>
                <p class="lead">An enthusiastic IT student with a passion for cloud computing and innovative technologies.</p>
                <p>I am constantly exploring new technologies and methodologies to expand my knowledge and skills in the ever-evolving tech landscape. My journey in technology started with a curiosity about how digital systems work and has evolved into a dedicated pursuit of expertise in cloud computing, web development, and cybersecurity.</p>
                
                <div class="quote-box">
                    <i class="bi bi-quote quote-icon"></i>
                    <p class="quote-text">
                        "I'm really into learning new things, especially when it comes to coding, databases, and cloud computing. 
                        I enjoy discovering how everything connects and finding smarter ways to work with technology."
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="education-box">
                <h2>Education Journey</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h3>BS Information Technology</h3>
                            <p>Pangasinan State University</p>
                            <span class="timeline-date">Current</span>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h3>STEM</h3>
                            <p>Great Plebeian College</p>
                            <span class="timeline-date">Completed</span>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h3>Elementary Education</h3>
                            <p>Palamis Elementary School</p>
                            <span class="timeline-date">Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
