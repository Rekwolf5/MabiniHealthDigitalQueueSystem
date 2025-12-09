<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Centralized Queue Announcements - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1f2937;
            overflow-x: hidden;
            height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .status-bar {
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: center;
            margin-top: 1rem;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f3f4f6;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .announcements-container {
            flex: 1;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .announcements-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .announcements-header h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .counter {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .announcements-list {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .announcement-item {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 1.5rem;
            align-items: center;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .announcement-item.announcing {
            background: #fef3c7;
            border-left-color: #f59e0b;
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .announcement-item.completed {
            opacity: 0.6;
        }

        .queue-number {
            background: #667eea;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            min-width: 120px;
            text-align: center;
        }

        .announcement-details {
            flex: 1;
        }

        .patient-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .service-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 1rem;
        }

        .priority-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-emergency {
            background: #fee2e2;
            color: #dc2626;
        }

        .priority-senior, .priority-pwd {
            background: #fef3c7;
            color: #d97706;
        }

        .priority-normal {
            background: #dbeafe;
            color: #2563eb;
        }

        .timestamp {
            text-align: right;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .speaker-icon {
            font-size: 2rem;
            color: #667eea;
            animation: speakerPulse 1s ease-in-out infinite;
        }

        @keyframes speakerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.25rem;
        }

        /* Scrollbar styling */
        .announcements-list::-webkit-scrollbar {
            width: 8px;
        }

        .announcements-list::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        .announcements-list::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .announcements-list::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-volume-up"></i> Centralized Queue Announcements</h1>
            <p>Automated voice announcements for all services</p>
            <div class="status-bar">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <span>System Active</span>
                </div>
                <div class="status-indicator">
                    <i class="fas fa-clock"></i>
                    <span id="current-time">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="announcements-container">
            <div class="announcements-header">
                <h2>
                    <i class="fas fa-bullhorn"></i>
                    Recent Announcements
                </h2>
                <div class="counter">
                    <span id="announcement-count">0</span> Today
                </div>
            </div>

            <div class="announcements-list" id="announcements-list">
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>Waiting for queue announcements...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lastAnnouncedId = 0;
        let announcementQueue = [];
        let isAnnouncing = false;
        let totalAnnouncements = 0;

        // Update current time every second
        setInterval(() => {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }, 1000);

        // Fetch pending announcements
        async function fetchAnnouncements() {
            try {
                console.log('Fetching announcements with last_id:', lastAnnouncedId);
                const response = await fetch(`/api/queue/pending-announcements?last_id=${lastAnnouncedId}`);
                const data = await response.json();

                console.log('API Response:', data);

                if (data.success && data.announcements.length > 0) {
                    console.log(`Found ${data.announcements.length} new announcements`);
                    // Add new announcements to queue
                    data.announcements.forEach(announcement => {
                        announcementQueue.push(announcement);
                        addAnnouncementToUI(announcement);
                        lastAnnouncedId = Math.max(lastAnnouncedId, announcement.id);
                        totalAnnouncements++;
                    });

                    // Update counter
                    document.getElementById('announcement-count').textContent = totalAnnouncements;

                    // Start processing queue if not already announcing
                    if (!isAnnouncing) {
                        processAnnouncementQueue();
                    }
                } else {
                    console.log('No new announcements found');
                }
            } catch (error) {
                console.error('Error fetching announcements:', error);
            }
        }

        // Add announcement to UI
        function addAnnouncementToUI(announcement) {
            const list = document.getElementById('announcements-list');
            
            // Remove empty state if exists
            const emptyState = list.querySelector('.empty-state');
            if (emptyState) {
                emptyState.remove();
            }

            // Create announcement element
            const item = document.createElement('div');
            item.className = 'announcement-item';
            item.id = `announcement-${announcement.id}`;
            
            // Priority badge
            let priorityClass = 'priority-normal';
            let priorityText = announcement.priority || 'Normal';
            if (announcement.priority === 'emergency') priorityClass = 'priority-emergency';
            else if (announcement.priority === 'senior' || announcement.priority === 'pwd') priorityClass = 'priority-senior';

            item.innerHTML = `
                <div class="queue-number">${announcement.queue_number}</div>
                <div class="announcement-details">
                    <div class="patient-name">${announcement.patient_name}</div>
                    <div class="service-info">
                        <i class="fas fa-hospital"></i>
                        <span>${announcement.service_name}</span>
                        <span class="priority-badge ${priorityClass}">${priorityText}</span>
                    </div>
                </div>
                <div class="timestamp">
                    <i class="fas fa-clock"></i> ${announcement.called_at}
                </div>
            `;

            // Add to top of list
            list.insertBefore(item, list.firstChild);

            // Keep only last 20 announcements
            const items = list.querySelectorAll('.announcement-item');
            if (items.length > 20) {
                items[items.length - 1].remove();
            }
        }

        // Process announcement queue
        async function processAnnouncementQueue() {
            if (announcementQueue.length === 0) {
                isAnnouncing = false;
                return;
            }

            isAnnouncing = true;
            const announcement = announcementQueue.shift();

            // Highlight current announcement
            const element = document.getElementById(`announcement-${announcement.id}`);
            if (element) {
                element.classList.add('announcing');
            }

            // Speak the announcement
            await announceQueue(announcement);

            // Mark as completed
            if (element) {
                element.classList.remove('announcing');
                element.classList.add('completed');
            }

            // Wait 5 seconds before next announcement
            setTimeout(() => {
                processAnnouncementQueue();
            }, 5000);
        }

        // Voice announcement function
        function announceQueue(announcement) {
            return new Promise((resolve) => {
                if ('speechSynthesis' in window) {
                    // Cancel any ongoing speech
                    window.speechSynthesis.cancel();

                    const text = `Queue number ${announcement.queue_number}. ${announcement.patient_name}. Please proceed to ${announcement.service_name}.`;

                    function speakAnnouncement() {
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'en-US';
                        utterance.rate = 0.9;
                        utterance.pitch = 1.1;
                        utterance.volume = 1;

                        // Get available voices
                        const voices = window.speechSynthesis.getVoices();
                        
                        // Try to select a female voice
                        const femaleVoice = voices.find(voice =>
                            voice.name.toLowerCase().includes('zira') ||
                            voice.name.toLowerCase().includes('female') ||
                            voice.name.toLowerCase().includes('samantha') ||
                            voice.name.toLowerCase().includes('karen') ||
                            (voice.name.toLowerCase().includes('google') && voice.name.toLowerCase().includes('us'))
                        );

                        if (femaleVoice) {
                            utterance.voice = femaleVoice;
                        }

                        utterance.onend = () => {
                            // Repeat once after 2 seconds
                            setTimeout(() => {
                                const utterance2 = new SpeechSynthesisUtterance(text);
                                utterance2.lang = 'en-US';
                                utterance2.rate = 0.9;
                                utterance2.pitch = 1.1;
                                utterance2.volume = 1;
                                if (femaleVoice) {
                                    utterance2.voice = femaleVoice;
                                }
                                utterance2.onend = () => resolve();
                                window.speechSynthesis.speak(utterance2);
                            }, 2000);
                        };

                        window.speechSynthesis.speak(utterance);
                    }

                    // Wait for voices to load if necessary
                    const voices = window.speechSynthesis.getVoices();
                    if (voices.length === 0) {
                        window.speechSynthesis.onvoiceschanged = function() {
                            speakAnnouncement();
                            window.speechSynthesis.onvoiceschanged = null;
                        };
                    } else {
                        speakAnnouncement();
                    }
                } else {
                    console.warn('Speech synthesis not supported');
                    resolve();
                }
            });
        }

        // Poll for new announcements every 3 seconds
        setInterval(fetchAnnouncements, 3000);

        // Initial fetch
        fetchAnnouncements();
    </script>
</body>
</html>
