@extends('layouts.app')

@section('content')
<div class="feedback-page">
    <div class="feedback-container">
        <div class="feedback-header">
            <div class="header-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <h1 class="page-title">Send Feedback</h1>
            <p class="page-subtitle">We value your feedback! Please let us know about any issues, suggestions, or feature requests.</p>
        </div>

        <div class="feedback-card">
            <form action="{{ route('feedback.store') }}" method="POST" class="feedback-form">
                @csrf
                
                <div class="form-group">
                    <label for="type" class="form-label">
                        <i class="fas fa-tag"></i> Feedback Type
                    </label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">Select feedback type...</option>
                        <option value="bug_report" {{ old('type') == 'bug_report' ? 'selected' : '' }}>
                            🐛 Bug Report
                        </option>
                        <option value="feature_request" {{ old('type') == 'feature_request' ? 'selected' : '' }}>
                            💡 Feature Request
                        </option>
                        <option value="complaint" {{ old('type') == 'complaint' ? 'selected' : '' }}>
                            ⚠️ Complaint
                        </option>
                        <option value="suggestion" {{ old('type') == 'suggestion' ? 'selected' : '' }}>
                            💭 Suggestion
                        </option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>
                            📝 Other
                        </option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="subject" class="form-label">
                        <i class="fas fa-heading"></i> Subject
                    </label>
                    <input type="text" 
                           class="form-input @error('subject') is-invalid @enderror" 
                           id="subject" 
                           name="subject" 
                           value="{{ old('subject') }}" 
                           placeholder="Brief description of your feedback" 
                           required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">
                        <i class="fas fa-align-left"></i> Message
                    </label>
                    <textarea class="form-textarea @error('message') is-invalid @enderror" 
                              id="message" 
                              name="message" 
                              rows="8" 
                              placeholder="Please provide detailed information about your feedback..."
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="char-count">
                        <span id="charCount">0</span> / 2000 characters
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ url()->previous() }}" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send Feedback</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.feedback-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
    min-height: calc(100vh - 200px);
}

.feedback-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.feedback-header {
    text-align: center;
    margin-bottom: 10px;
}

.header-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.header-icon i {
    font-size: 2.5rem;
    color: white;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 12px 0;
}

.page-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.6;
}

.feedback-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.feedback-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label i {
    color: #667eea;
}

.form-select,
.form-input,
.form-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.2s ease;
    background: white;
}

.form-select:focus,
.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 200px;
    line-height: 1.6;
}

.char-count {
    font-size: 0.85rem;
    color: #6c757d;
    text-align: right;
    margin-top: 4px;
}

.char-count span {
    font-weight: 600;
    color: #667eea;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-top: 10px;
    padding-top: 24px;
    border-top: 1px solid #e9ecef;
}

.btn-cancel,
.btn-submit {
    padding: 14px 28px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
}

.btn-cancel {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #e9ecef;
}

.btn-cancel:hover {
    background: #e9ecef;
    color: #495057;
    text-decoration: none;
    transform: translateY(-1px);
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .feedback-page {
        padding: 30px 15px;
    }
    
    .feedback-card {
        padding: 30px 24px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-cancel,
    .btn-submit {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    
    if (messageTextarea && charCount) {
        // Update character count
        function updateCharCount() {
            const length = messageTextarea.value.length;
            charCount.textContent = length;
            
            if (length > 2000) {
                charCount.style.color = '#dc3545';
            } else if (length > 1800) {
                charCount.style.color = '#ffc107';
            } else {
                charCount.style.color = '#667eea';
            }
        }
        
        messageTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count
    }
});
</script>
@endsection
