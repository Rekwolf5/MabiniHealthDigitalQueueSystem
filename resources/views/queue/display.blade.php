<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Display - Mabini Health Center</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%);
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .header {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 3px solid rgba(255, 255, 255, 0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #6fffe9 0%, #5bc0be 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: bold;
        }

        .header .date-time {
            font-size: 1.2rem;
            color: #a8b2d1;
            margin-top: 5px;
        }

        .service-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
        }

        .service-indicator {
            display: flex;
            gap: 8px;
        }

        .service-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
            cursor: pointer;
        }

        .service-dot.active {
            background: #6fffe9;
            width: 30px;
            border-radius: 6px;
        }

        .carousel-container {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .service-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .service-slide.active {
            opacity: 1;
        }

        .service-header {
            text-align: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.3);
        }

        .service-header h2 {
            font-size: 2rem;
            color: #e0f2fe;
            margin: 0;
        }

        .now-serving {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .now-serving h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #d1fae5;
        }

        .current-number {
            font-size: 5rem;
            font-weight: bold;
            color: #6fffe9;
            text-shadow: 0 0 30px rgba(111, 255, 233, 0.5);
            margin: 5px 0;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .patient-name {
            font-size: 1.5rem;
            color: #ffffff;
            margin-top: 5px;
            font-weight: 600;
        }

        .waiting-section {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            overflow: hidden;
        }

        .waiting-section h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #6fffe9;
            text-align: center;
        }

        .queue-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            max-height: calc(100vh - 420px);
            overflow-y: auto;
        }

        .queue-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s;
        }

        .queue-card.priority {
            border-color: #fbbf24;
            background: rgba(251, 191, 36, 0.1);
        }

        .queue-card.emergency {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            animation: emergency-blink 1s infinite;
        }

        @keyframes emergency-blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .queue-number {
            font-size: 2rem;
            font-weight: bold;
            color: #6fffe9;
            text-align: center;
            margin-bottom: 8px;
        }

        .queue-patient {
            font-size: 1rem;
            text-align: center;
            color: #e5e7eb;
        }

        .queue-priority {
            text-align: center;
            font-size: 0.85rem;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: 600;
        }

        .queue-priority.normal {
            background: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }

        .queue-priority.senior {
            background: rgba(251, 191, 36, 0.3);
            color: #fcd34d;
        }

        .queue-priority.pwd {
            background: rgba(139, 92, 246, 0.3);
            color: #c4b5fd;
        }

        .queue-priority.emergency {
            background: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.5rem;
        }

        .queue-grid::-webkit-scrollbar {
            width: 8px;
        }

        .queue-grid::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .queue-grid::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-hospital"></i> Mabini Health Center</h1>
        <div class="date-time" id="dateTime"></div>
    </div>

    <div class="service-nav">
        <div class="service-indicator" id="serviceIndicator">
            @foreach($services as $index => $service)
                <div class="service-dot {{ $index === 0 ? 'active' : '' }}" 
                     data-index="{{ $index }}"
                     onclick="goToSlide({{ $index }})"></div>
            @endforeach
        </div>
    </div>

    <div class="carousel-container">
        @foreach($services as $index => $service)
            <div class="service-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                <div class="service-header">
                    <h2>{{ $service->name }}</h2>
                </div>

                @php
                    $nowServing = $service->frontDeskQueues->whereIn('status', ['called', 'in_progress'])->first();
                    $waiting = $service->frontDeskQueues->where('status', 'waiting');
                @endphp

                @if($nowServing)
                    <div class="now-serving">
                        <h3><i class="fas fa-bell"></i> NOW SERVING</h3>
                        <div class="current-number">{{ $nowServing->queue_number }}</div>
                        <div class="patient-name">{{ $nowServing->patient_name }}</div>
                    </div>
                @else
                    <div class="now-serving">
                        <h3><i class="fas fa-info-circle"></i> NO PATIENT BEING SERVED</h3>
                        <div style="font-size: 1.5rem; color: #d1fae5; margin-top: 10px;">
                            Please wait for the next call
                        </div>
                    </div>
                @endif

                <div class="waiting-section">
                    <h3><i class="fas fa-list-ul"></i> Waiting Queue ({{ $waiting->count() }})</h3>
                    
                    @if($waiting->count() > 0)
                        <div class="queue-grid">
                            @foreach($waiting as $queue)
                                <div class="queue-card {{ $queue->priority === 'emergency' ? 'emergency' : ($queue->priority !== 'normal' ? 'priority' : '') }}">
                                    <div class="queue-number">{{ $queue->queue_number }}</div>
                                    <div class="queue-patient">{{ $queue->patient_name }}</div>
                                    <div class="queue-priority {{ $queue->priority }}">
                                        @if($queue->priority === 'emergency')
                                            <i class="fas fa-ambulance"></i> EMERGENCY
                                        @elseif($queue->priority === 'senior')
                                            <i class="fas fa-user-shield"></i> SENIOR
                                        @elseif($queue->priority === 'pwd')
                                            <i class="fas fa-wheelchair"></i> PWD
                                        @else
                                            <i class="fas fa-user"></i> REGULAR
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-clipboard-check"></i>
                            <p>No patients waiting</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($services->count() === 0)
            <div class="service-slide active">
                <div class="empty-state" style="padding-top: 150px;">
                    <i class="fas fa-hospital-alt"></i>
                    <p>No services available</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = {{ $services->count() }};
        const slideInterval = 10000;

        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('dateTime').textContent = now.toLocaleDateString('en-US', options);
        }

        function nextSlide() {
            if (totalSlides === 0) return;
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        function goToSlide(index) {
            currentSlide = index;
            showSlide(currentSlide);
        }

        function showSlide(index) {
            document.querySelectorAll('.service-slide').forEach(slide => {
                slide.classList.remove('active');
            });

            const currentSlideElement = document.querySelector(`[data-slide="${index}"]`);
            if (currentSlideElement) {
                currentSlideElement.classList.add('active');
            }

            document.querySelectorAll('.service-dot').forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        if (totalSlides > 1) {
            setInterval(nextSlide, slideInterval);
        }

        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
