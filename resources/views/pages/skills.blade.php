@extends('layouts.app')

@section('title', 'Skills')

@section('content')
<section class="skills-section">
    <div class="section-header">
        <h1>My Skills</h1>
        <div class="section-line"></div>
    </div>

    <div class="skill-categories">
        <div class="category-tabs">
            <button class="category-tab active" data-target="technical">Technical Skills</button>
            <button class="category-tab" data-target="soft">Soft Skills</button>
            <button class="category-tab" data-target="certifications">Certifications</button>
        </div>
    </div>

    <div class="skill-content" id="technical">
        <div class="row">
            <div class="col-md-6 col-lg-4">
                <div class="skill-card">
                    <div class="skill-icon"><i class="bi bi-cloud"></i></div>
                    <h3>Cloud Computing</h3>
                    <p>Primary focus with knowledge of major platforms and services</p>
                    <div class="skill-level">
                        <div class="skill-progress" style="width: 85%"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="skill-card">
                    <div class="skill-icon"><i class="bi bi-code"></i></div>
                    <h3>Front-end Development</h3>
                    <p>HTML, CSS, JavaScript, React</p>
                    <div class="skill-level">
                        <div class="skill-progress" style="width: 80%"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="skill-card">
                    <div class="skill-icon"><i class="bi bi-server"></i></div>
                    <h3>Back-end Development</h3>
                    <p>Node.js, Python, Java</p>
                    <div class="skill-level">
                        <div class="skill-progress" style="width: 75%"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="skill-card">
                    <div class="skill-icon"><i class="bi bi-database"></i></div>
                    <h3>Database Management</h3>
                    <p>SQL, NoSQL, MongoDB, PostgreSQL</p>
                    <div class="skill-level">
                        <div class="skill-progress" style="width: 70%"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="skill-card">
                    <div class="skill-icon"><i class="bi bi-shield-lock"></i></div>
                    <h3>Cybersecurity</h3>
                    <p>Risk management, network security, encryption</p>
                    <div class="skill-level">
                        <div class="skill-progress" style="width: 65%"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="skill-card">
                    <div class="skill-icon"><i class="bi bi-robot"></i></div>
                    <h3>Generative AI</h3>
                    <p>AI model development, prompt engineering</p>
                    <div class="skill-level">
                        <div class="skill-progress" style="width: 60%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="skill-content" id="soft" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-lightbulb"></i></div>
                    <div class="soft-skill-info">
                        <h3>Problem-Solving</h3>
                        <p>Analytical thinking, troubleshooting</p>
                    </div>
                </div>
                
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-chat-dots"></i></div>
                    <div class="soft-skill-info">
                        <h3>Communication</h3>
                        <p>Technical writing, public speaking</p>
                    </div>
                </div>
                
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-people"></i></div>
                    <div class="soft-skill-info">
                        <h3>Collaboration</h3>
                        <p>Teamwork, cross-functional coordination</p>
                    </div>
                </div>
                
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <div class="soft-skill-info">
                        <h3>Adaptability</h3>
                        <p>Quick learning, embracing new technologies</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-star"></i></div>
                    <div class="soft-skill-info">
                        <h3>Leadership</h3>
                        <p>Mentoring, project management</p>
                    </div>
                </div>
                
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-graph-up"></i></div>
                    <div class="soft-skill-info">
                        <h3>Critical Thinking</h3>
                        <p>Decision-making, evaluating risks</p>
                    </div>
                </div>
                
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-palette"></i></div>
                    <div class="soft-skill-info">
                        <h3>Creativity</h3>
                        <p>Innovative solutions, design thinking</p>
                    </div>
                </div>
                
                <div class="soft-skill-card">
                    <div class="soft-skill-icon"><i class="bi bi-clock"></i></div>
                    <div class="soft-skill-info">
                        <h3>Time Management</h3>
                        <p>Prioritization, efficiency</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="skill-content" id="certifications" style="display: none;">
        <div class="certifications-container">
            <div class="certification-card">
                <div class="certification-icon"><i class="bi bi-award"></i></div>
                <div class="certification-content">
                    <h3>Introduction to Cybersecurity for Business</h3>
                    <p>Coursera</p>
                    <a href="https://coursera.org/share/e288bde432ec3008161e2153b3c3ddb9" class="cert-link" target="_blank">View Certificate <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="certification-card">
                <div class="certification-icon"><i class="bi bi-award"></i></div>
                <div class="certification-content">
                    <h3>Cybersecurity Foundations for Risk Management</h3>
                    <p>Coursera</p>
                    <a href="https://coursera.org/share/12f7b88d7d8ab4e1ab0dfa451b71f275" class="cert-link" target="_blank">View Certificate <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="certification-card">
                <div class="certification-icon"><i class="bi bi-award"></i></div>
                <div class="certification-content">
                    <h3>Introduction to Generative AI</h3>
                    <p>Coursera</p>
                    <a href="https://coursera.org/share/9e11710c0b87a1fc09eb98b43dda4b4a" class="cert-link" target="_blank">View Certificate <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
                        <div class="certification-card">
                <div class="certification-icon"><i class="bi bi-award"></i></div>
                <div class="certification-content">
                    <h3>Responsive Web Design</h3>
                    <p>FreeCodeCamp</p>
                    <a href="https://www.freecodecamp.org/certification/janchristopher/responsive-web-design" class="cert-link" target="_blank">View Certificate <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="certification-card">
                <div class="certification-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="certification-content">
                    <h3>AWS Cloud Practitioner</h3>
                    <p>Udemy (In Progress)</p>
                    <span class="progress-badge">In Progress</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
