@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-heart"></i>
                    <span>FriendZone</span>
                </div>
                <h2>Create Your Account</h2>
                <p>Join thousands of users finding their perfect match</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
                @csrf

                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-user"></i> Personal Information
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-signature"></i> First Name
                                </label>
                                <input id="name" 
                                       type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autocomplete="name" 
                                       autofocus
                                       placeholder="Enter your first name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="surname">
                                    <i class="fas fa-signature"></i> Last Name
                                </label>
                                <input id="surname" 
                                       type="text" 
                                       class="form-control @error('surname') is-invalid @enderror" 
                                       name="surname" 
                                       value="{{ old('surname') }}" 
                                       required 
                                       autocomplete="surname"
                                       placeholder="Enter your last name">
                                @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <input id="email" 
                                       type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email"
                                       placeholder="Enter your email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input id="phone" 
                                       type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       required 
                                       autocomplete="tel"
                                       placeholder="+1 (555) 123-4567"
                                       pattern="[\+]?[0-9\s\-\(\)]+">
                                <small class="form-text text-muted">Format: +1 (555) 123-4567</small>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="age">
                                    <i class="fas fa-birthday-cake"></i> Age
                                </label>
                                <input type="text" 
                                       class="js-range-slider" 
                                       name="age" 
                                       id="age" 
                                       value=""
                                       data-type="single"
                                       data-min="18"
                                       data-max="100"
                                       data-from="25"
                                       data-grid="false">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </label>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input type="radio" name="gender" id="male" value="male" {{ old('gender') == 'male' ? 'checked' : '' }} required>
                                        <label for="male">
                                            <i class="fas fa-mars"></i> Male
                                        </label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" name="gender" id="female" value="female" {{ old('gender') == 'female' ? 'checked' : '' }} required>
                                        <label for="female">
                                            <i class="fas fa-venus"></i> Female
                                        </label>
                                    </div>
                                </div>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-lock"></i> Security
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">
                                    <i class="fas fa-key"></i> Password
                                </label>
                                <div class="password-input-wrapper">
                                    <input id="password" 
                                           type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Create a password">
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password-confirm">
                                    <i class="fas fa-key"></i> Confirm Password
                                </label>
                                <div class="password-input-wrapper">
                                    <input id="password-confirm" 
                                           type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Confirm your password">
                                    <button type="button" class="password-toggle" onclick="togglePassword('password-confirm')">
                                        <i class="fas fa-eye" id="passwordConfirmToggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle"></i> About You
                    </h4>
                    
                    <div class="form-group">
                        <label for="description">
                            <i class="fas fa-pen"></i> Tell us about yourself
                        </label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  required
                                  placeholder="Share a bit about yourself...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="relationship">
                                    <i class="fas fa-heart"></i> Relationship Status
                                </label>
                                <select class="form-control" id="relationship" name="relationship" required>
                                    <option value="">Select status</option>
                                    <option value="Single" {{ old('relationship') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Taken" {{ old('relationship') == 'Taken' ? 'selected' : '' }}>Taken</option>
                                    <option value="Engaged" {{ old('relationship') == 'Engaged' ? 'selected' : '' }}>Engaged</option>
                                    <option value="Married" {{ old('relationship') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="It's complicated" {{ old('relationship') == "It's complicated" ? 'selected' : '' }}>It's complicated</option>
                                    <option value="Open relationship" {{ old('relationship') == 'Open relationship' ? 'selected' : '' }}>Open relationship</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country">
                                    <i class="fas fa-globe"></i> Country
                                </label>
                                <select class="form-control" id="country" name="country" required>
                                    <option value="">Select country</option>
                                    @include('partials.countries')
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="languages">
                            <i class="fas fa-language"></i> Languages (comma-separated)
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="languages" 
                               name="languages" 
                               value="{{ old('languages') }}" 
                               required
                               placeholder="English, Spanish, French">
                        <small class="form-text text-muted">Separate multiple languages with commas</small>
                        @error('languages')
                            <span class="invalid-feedback" role="alert">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-search"></i> Search Preferences
                    </h4>
                    
                    <div class="form-group">
                        <label for="search_age_range">
                            <i class="fas fa-calendar-alt"></i> Age Range
                        </label>
                        <input type="text" 
                               class="js-range-slider" 
                               name="search_age_range" 
                               id="search_age_range"
                               value=""
                               data-type="double"
                               data-min="18"
                               data-max="100"
                               data-from="18"
                               data-to="100">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-users"></i> I'm looking for
                        </label>
                        <div class="checkbox-group">
                            <div class="checkbox-option">
                                <input type="checkbox" name="search_male" id="search_male" value="1" {{ old('search_male', true) ? 'checked' : '' }}>
                                <label for="search_male">
                                    <i class="fas fa-mars"></i> Male
                                </label>
                            </div>
                            <div class="checkbox-option">
                                <input type="checkbox" name="search_female" id="search_female" value="1" {{ old('search_female', true) ? 'checked' : '' }}>
                                <label for="search_female">
                                    <i class="fas fa-venus"></i> Female
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>

                <div class="auth-divider">
                    <span>Already have an account?</span>
                </div>

                <a href="{{ route('login') }}" class="btn btn-outline btn-block">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
            </form>
        </div>
    </div>
</div>

<style>
    .auth-page {
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
    }

    .auth-container {
        width: 100%;
        max-width: 800px;
    }

    .auth-card {
        background: var(--white, #ffffff);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        padding: 2.5rem;
        animation: slideUp 0.5s ease;
        max-height: 90vh;
        overflow-y: auto;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
    }

    .auth-header h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark, #1a202c);
        margin-bottom: 0.5rem;
    }

    .auth-header p {
        color: var(--text-light, #718096);
        font-size: 0.95rem;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-dark, #1a202c);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color, #667eea);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--text-dark, #1a202c);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-group label i {
        color: var(--primary-color, #667eea);
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--white, #ffffff);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color, #667eea);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .password-input-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-light, #718096);
        cursor: pointer;
        padding: 0.5rem;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: var(--primary-color, #667eea);
    }

    .radio-group, .checkbox-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .radio-option, .checkbox-option {
        flex: 1;
        min-width: 120px;
    }

    .radio-option input[type="radio"],
    .checkbox-option input[type="checkbox"] {
        display: none;
    }

    .radio-option label,
    .checkbox-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        background: var(--white, #ffffff);
    }

    .radio-option input[type="radio"]:checked + label,
    .checkbox-option input[type="checkbox"]:checked + label {
        border-color: var(--primary-color, #667eea);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .radio-option label:hover,
    .checkbox-option label:hover {
        border-color: var(--primary-color, #667eea);
    }

    .invalid-feedback {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .form-text {
        font-size: 0.875rem;
        color: var(--text-light, #718096);
        margin-top: 0.25rem;
    }

    .btn {
        padding: 0.875rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .btn-outline {
        background: transparent;
        color: var(--primary-color, #667eea);
        border: 2px solid var(--primary-color, #667eea);
    }

    .btn-outline:hover {
        background: var(--primary-color, #667eea);
        color: white;
    }

    .btn-block {
        width: 100%;
    }

    .btn-lg {
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
    }

    .auth-divider {
        text-align: center;
        margin: 1.5rem 0;
        position: relative;
        color: var(--text-light, #718096);
        font-size: 0.9rem;
    }

    .auth-divider::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
        background: #e2e8f0;
    }

    .auth-divider span {
        background: var(--white, #ffffff);
        padding: 0 1rem;
        position: relative;
    }

    .row {
        display: flex;
        gap: 1rem;
        margin-bottom: 0;
    }

    .col-md-6 {
        flex: 1;
    }

    @media (max-width: 768px) {
        .row {
            flex-direction: column;
        }
        
        .radio-group, .checkbox-group {
            flex-direction: column;
        }
    }
</style>

<script>
    $(".js-range-slider").ionRangeSlider();

    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(fieldId === 'password' ? 'passwordToggleIcon' : 'passwordConfirmToggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Phone number formatting
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else if (value.length <= 10) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6);
            } else {
                value = '+' + value.slice(0, 1) + ' (' + value.slice(1, 4) + ') ' + value.slice(4, 7) + '-' + value.slice(7, 11);
            }
        }
        e.target.value = value;
    });
</script>
@endsection
