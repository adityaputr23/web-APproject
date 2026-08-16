@extends('layouts.admin')

@section('title', 'Inquiries Inbox | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Client Inquiries</h1>
            <p class="page-subtitle">Read and manage messages received through the portfolio contact form.</p>
        </div>
    </div>

    <div class="enquiries-inbox-grid">
        <!-- Message List Card -->
        <div class="content-card inbox-card">
            <div class="card-header">
                <h3 class="card-title">Inbox Messages</h3>
            </div>
            
            <div class="message-items-list">
                @forelse($enquiries as $enquiry)
                    <div class="message-item-card {{ !$enquiry->is_read ? 'unread' : '' }}" data-id="{{ $enquiry->id }}">
                        <div class="message-item-header">
                            <span class="sender-name">{{ $enquiry->name }}</span>
                            <span class="message-time">{{ $enquiry->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="message-item-body">
                            <span class="message-subject">{{ $enquiry->subject ?? '(No Subject)' }}</span>
                            <p class="message-excerpt">{{ \Illuminate\Support\Str::limit($enquiry->message, 80) }}</p>
                        </div>
                        <div class="message-item-footer">
                            <span class="sender-email"><i class="ri-mail-line"></i> {{ $enquiry->email }}</span>
                            
                            <div class="message-item-actions">
                                <form method="POST" action="{{ route('admin.enquiries.toggle-read', $enquiry->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-msg-action read-toggle" title="{{ $enquiry->is_read ? 'Mark as Unread' : 'Mark as Read' }}">
                                        @if($enquiry->is_read)
                                            <i class="ri-mail-open-line"></i> Mark Unread
                                        @else
                                            <i class="ri-mail-unread-line"></i> Mark Read
                                        @endif
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry->id) }}" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-msg-action delete" title="Delete Message">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Hidden container for full message detail view -->
                        <div id="full-msg-{{ $enquiry->id }}" class="hide">
                            <div class="msg-detail-view">
                                <div class="msg-detail-header">
                                    <div class="sender-meta">
                                        <span class="sender-title">{{ $enquiry->name }}</span>
                                        <span class="sender-mail-link">&lt;{{ $enquiry->email }}&gt;</span>
                                    </div>
                                    <span class="msg-full-date">{{ $enquiry->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <div class="divider"></div>
                                <div class="msg-detail-subject">
                                    <strong>Subject:</strong> {{ $enquiry->subject ?? '(No Subject)' }}
                                </div>
                                <div class="msg-detail-content">
                                    {!! nl2br(e($enquiry->message)) !!}
                                </div>
                                <div class="msg-detail-actions">
                                    <a href="mailto:{{ $enquiry->email }}?subject=Re: {{ rawurlencode($enquiry->subject) }}" class="btn btn-primary"><i class="ri-reply-line"></i> Reply via Email</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-inbox">
                        <i class="ri-mail-line empty-icon"></i>
                        <p>Your inbox is clear. No client inquiries yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Detail Message View (Interactive Right Panel) -->
        <div class="content-card message-viewer-card">
            <div id="viewerPlaceholder" class="viewer-placeholder">
                <i class="ri-mail-open-line placeholder-icon"></i>
                <p>Select a message from the list to display the details.</p>
            </div>
            <div id="viewerContent" class="viewer-content hide">
                <!-- Loaded Dynamically via JS -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const messageCards = document.querySelectorAll('.message-item-card');
            const viewerPlaceholder = document.getElementById('viewerPlaceholder');
            const viewerContent = document.getElementById('viewerContent');

            messageCards.forEach(card => {
                card.addEventListener('click', (e) => {
                    // Ignore clicks on buttons/forms
                    if (e.target.closest('button') || e.target.closest('form') || e.target.closest('a')) {
                        return;
                    }

                    // Remove active status from other cards
                    messageCards.forEach(c => c.classList.remove('active'));
                    card.classList.add('active');

                    // Extract and show full message content
                    const msgId = card.getAttribute('data-id');
                    const hiddenContent = document.getElementById(`full-msg-${msgId}`).innerHTML;

                    viewerPlaceholder.classList.add('hide');
                    viewerContent.classList.remove('hide');
                    viewerContent.innerHTML = hiddenContent;
                });
            });
        });
    </script>
@endsection
