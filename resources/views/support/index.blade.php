@extends(Auth::guard('patient')->check() ? 'layouts.patient' : 'layouts.app')

@section('title', 'Help & Support')

@section('content')
<div class="container" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
    <div style="background: white; border-radius: 8px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <i class="fas fa-headset" style="font-size: 64px; color: #059669; margin-bottom: 1rem;"></i>
            <h1 style="font-size: 2rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">Help & Support</h1>
            <p style="color: #6b7280;">We're here to help! Send us your questions or concerns.</p>
        </div>

        @if (session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; display: flex; align-items: center;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #fef2f2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <strong><i class="fas fa-exclamation-circle"></i> Error:</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Support Form -->
        <form method="POST" action="{{ Auth::guard('patient')->check() ? route('patient.support.submit') : route('staff.support.submit') }}">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                    <i class="fas fa-tag"></i> Subject
                </label>
                <input 
                    type="text" 
                    name="subject" 
                    value="{{ old('subject') }}"
                    required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem;"
                    placeholder="Brief description of your issue"
                >
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">
                    <i class="fas fa-comment-dots"></i> Message
                </label>
                <textarea 
                    name="message" 
                    required 
                    rows="6"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; resize: vertical;"
                    placeholder="Please describe your question or concern in detail..."
                >{{ old('message') }}</textarea>
            </div>

            <button 
                type="submit" 
                style="background: #059669; color: white; padding: 0.875rem 2rem; border: none; border-radius: 4px; font-size: 1rem; font-weight: 600; cursor: pointer; width: 100%;"
            >
                <i class="fas fa-paper-plane"></i> Send Message
            </button>
        </form>

        <!-- FAQ Section -->
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">
                <i class="fas fa-question-circle"></i> Frequently Asked Questions
            </h2>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <details style="border: 1px solid #e5e7eb; border-radius: 4px; padding: 1rem;">
                    <summary style="font-weight: 600; cursor: pointer; color: #059669;">
                        How do I request a queue number?
                    </summary>
                    <p style="margin-top: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                        Go to "Request Queue" from your dashboard, select the service type, and submit your request. You'll receive a queue number once approved by staff.
                    </p>
                </details>

                <details style="border: 1px solid #e5e7eb; border-radius: 4px; padding: 1rem;">
                    <summary style="font-weight: 600; cursor: pointer; color: #059669;">
                        How can I check my queue status?
                    </summary>
                    <p style="margin-top: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                        Your current queue status is displayed on your dashboard. You'll also receive notifications when your status changes.
                    </p>
                </details>

                <details style="border: 1px solid #e5e7eb; border-radius: 4px; padding: 1rem;">
                    <summary style="font-weight: 600; cursor: pointer; color: #059669;">
                        How do I view my medical records?
                    </summary>
                    <p style="margin-top: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                        Click on "Medical History" in the menu to view all your past consultations, prescriptions, and medical records.
                    </p>
                </details>

                <details style="border: 1px solid #e5e7eb; border-radius: 4px; padding: 1rem;">
                    <summary style="font-weight: 600; cursor: pointer; color: #059669;">
                        What if I forgot my password?
                    </summary>
                    <p style="margin-top: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                        Click "Forgot Password" on the login page. Enter your email and we'll send you a verification code to reset your password.
                    </p>
                </details>
            </div>
        </div>

        <!-- Previous Messages -->
        @if(count($messages) > 0)
            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">
                    <i class="fas fa-history"></i> Your Previous Messages
                </h2>

                @foreach($messages as $msg)
                    <div style="border: 1px solid #e5e7eb; border-radius: 4px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: #1f2937;">{{ $msg->subject }}</strong>
                            <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 4px; 
                                @if($msg->status === 'replied') background: #d1fae5; color: #065f46;
                                @elseif($msg->status === 'closed') background: #e5e7eb; color: #6b7280;
                                @else background: #fef3c7; color: #92400e;
                                @endif">
                                {{ ucfirst($msg->status) }}
                            </span>
                        </div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">{{ $msg->message }}</p>
                        <small style="color: #9ca3af;">{{ $msg->created_at->format('M d, Y h:i A') }}</small>

                        @if($msg->admin_reply)
                            <div style="background: #f0fdf4; border-left: 4px solid #059669; padding: 0.75rem; margin-top: 1rem; border-radius: 4px;">
                                <strong style="color: #059669; font-size: 0.875rem;">
                                    <i class="fas fa-reply"></i> Admin Reply:
                                </strong>
                                <p style="color: #374151; font-size: 0.875rem; margin: 0.5rem 0 0;">{{ $msg->admin_reply }}</p>
                                <small style="color: #6b7280;">{{ $msg->replied_at->format('M d, Y h:i A') }}</small>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Contact Info -->
        <div style="background: #f9fafb; border-radius: 4px; padding: 1.5rem; margin-top: 2rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">
                <i class="fas fa-phone"></i> Other Ways to Reach Us
            </h3>
            <div style="font-size: 0.875rem; color: #6b7280;">
                <p style="margin-bottom: 0.5rem;">
                    <i class="fas fa-map-marker-alt" style="width: 20px;"></i> Mabini Health Center
                </p>
                <p style="margin-bottom: 0.5rem;">
                    <i class="fas fa-phone" style="width: 20px;"></i> Contact: (Available at reception)
                </p>
                <p style="margin-bottom: 0;">
                    <i class="fas fa-clock" style="width: 20px;"></i> Hours: Monday - Friday, 8:00 AM - 5:00 PM
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
