@extends('layouts.app')

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>

@section('content')
    <div class="profile-page">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row">
                <!-- Left Sidebar -->
                <div class="col-lg-4">
                    <div class="profile-sidebar">
                        <!-- Profile Card -->
                        <div class="profile-card">
                            <div class="profile-avatar-section">
                                <div class="profile-avatar-wrapper">
                                    <img src="{{ $userInfo->getPicture() }}" id="profile_picture" alt="Profile picture" class="profile-avatar">
                                    <div class="avatar-overlay">
                                        <label for="profile-picture-upload" class="avatar-upload-btn">
                                            <i class="fas fa-camera"></i>
                                            <span>Change Photo</span>
                                        </label>
                                    </div>
                                </div>
                                <form action="{{ route('profile.updateProfilePicture') }}" enctype="multipart/form-data" method="post" id="avatar-form" style="display: none;">
                                    @csrf
                                    @method('put')
                                    <input type="file" name="picture" id="profile-picture-upload" onchange="document.getElementById('avatar-form').submit();" accept="image/*">
                                </form>
                            </div>
                            
                            <div class="profile-info">
                                <h3 class="profile-name">{{ $userInfo->name . ' ' . $userInfo->surname }}</h3>
                                <p class="profile-joined">
                                    <i class="fas fa-calendar-alt"></i> Joined {{ $user->created_at->format('M Y') }}
                                </p>
                                
                                <!-- Profile Completeness -->
                                @php
                                    $completeness = $userInfo->getCompletenessPercentage();
                                    $missingFields = $userInfo->getMissingFields();
                                @endphp
                                <div class="profile-completeness">
                                    <div class="completeness-header">
                                        <span class="completeness-label">
                                            <i class="fas fa-chart-line"></i> Profile Completeness
                                        </span>
                                        <span class="completeness-percentage">{{ $completeness }}%</span>
                                    </div>
                                    <div class="progress completeness-progress">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $completeness }}%" 
                                             aria-valuenow="{{ $completeness }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    @if(count($missingFields) > 0 && $completeness < 100)
                                        <div class="completeness-tips">
                                            <i class="fas fa-info-circle"></i> 
                                            <span>Add: {{ implode(', ', array_slice($missingFields, 0, 3)) }}{{ count($missingFields) > 3 ? '...' : '' }}</span>
                                        </div>
                                    @elseif($completeness == 100)
                                        <div class="completeness-complete">
                                            <i class="fas fa-check-circle"></i> Profile complete!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Menu -->
                        <div class="profile-nav">
                            <a href="{{ route('profile.updateProfile') }}" class="nav-item active">
                                <i class="fas fa-user-edit"></i>
                                <span>Edit Profile</span>
                            </a>
                            <a href="{{ route('profile.updateSettings') }}" class="nav-item">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                            <form action="{{ route('profile.destroy') }}" method="post" class="delete-form">
                                @csrf
                                @method('delete')
                                <button type="submit" class="nav-item nav-item-danger" onclick="return confirm('Are you sure you want to delete your profile? This action cannot be undone.');">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>Delete Profile</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="col-lg-8">
                    <div class="profile-content">
                        <div class="content-header">
                            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                            <p>Update your personal information and preferences</p>
                        </div>
                        
                        <form method='post' action="{{ route('profile.updateProfile') }}" class="profile-form">
                            @csrf
                            
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user"></i> Personal Information
                                </h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">
                                                <i class="fas fa-signature"></i> Name
                                            </label>
                                            <input class="form-control" type="text" name="name" id="name"
                                                   value="{{ $userInfo->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="surname">
                                                <i class="fas fa-signature"></i> Surname
                                            </label>
                                            <input class="form-control" type="text" name="surname" id="surname"
                                                   value="{{ $userInfo->surname }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">
                                                <i class="fas fa-envelope"></i> E-mail
                                            </label>
                                            <input class="form-control" type="email" name="email" id="email"
                                                   value="{{ $user->email }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">
                                                <i class="fas fa-phone"></i> Phone Number
                                            </label>
                                            <input class="form-control" type="tel" name="phone" id="phone"
                                                   value="{{ $userInfo->phone }}" required
                                                   placeholder="+1 (555) 123-4567"
                                                   pattern="[\+]?[0-9\s\-\(\)]+">
                                            <small class="form-text text-muted">Format: +1 (555) 123-4567</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="gender">
                                                <i class="fas fa-venus-mars"></i> Gender
                                            </label>
                                            <div class="radio-group">
                                                <div class="radio-option">
                                                    <input type="radio" name="gender" id="gender_male" value="male" {{ $userInfo->gender == 'male' ? 'checked' : '' }} required>
                                                    <label for="gender_male">
                                                        <i class="fas fa-mars"></i> Male
                                                    </label>
                                                </div>
                                                <div class="radio-option">
                                                    <input type="radio" name="gender" id="gender_female" value="female" {{ $userInfo->gender == 'female' ? 'checked' : '' }} required>
                                                    <label for="gender_female">
                                                        <i class="fas fa-venus"></i> Female
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age">
                                                <i class="fas fa-birthday-cake"></i> Age
                                            </label>
                                            <input type="text" class="js-range-slider" name="age" id="age" value=""
                                                   data-type="single"
                                                   data-min="18"
                                                   data-max="100"
                                                   data-from="{{ $userInfo->age }}"
                                                   data-grid="false"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="relationship">
                                                <i class="fas fa-heart"></i> Relationship Status
                                            </label>
                                            <select class="form-control form-select" id="relationship" name="relationship" required>
                                                <option value="">Select relationship status</option>
                                                <option value="Single" {{ $userInfo->relationship == 'Single' ? 'selected' : '' }}>Single</option>
                                                <option value="Taken" {{ $userInfo->relationship == 'Taken' ? 'selected' : '' }}>Taken</option>
                                                <option value="Engaged" {{ $userInfo->relationship == 'Engaged' ? 'selected' : '' }}>Engaged</option>
                                                <option value="Married" {{ $userInfo->relationship == 'Married' ? 'selected' : '' }}>Married</option>
                                                <option value="It's complicated" {{ $userInfo->relationship == "It's complicated" ? 'selected' : '' }}>It's complicated</option>
                                                <option value="Open relationship" {{ $userInfo->relationship == 'Open relationship' ? 'selected' : '' }}>Open relationship</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-globe"></i> Location & Languages
                                </h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="country">
                                                <i class="fas fa-globe"></i> Country
                                            </label>
                                            <select class="form-control form-select" id="country" name="country" required>
                                                <option value="">Select country</option>
                                                @include('partials.countries', ['selected' => $userInfo->country])
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="languages">
                                                <i class="fas fa-language"></i> Languages
                                            </label>
                                            <textarea class="form-control" id="languages" name="languages" rows="2"
                                                      style="resize:none;" required
                                                      placeholder="e.g., English, Chinese, Spanish">{{ $userInfo->languages }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-info-circle"></i> About Me
                                </h4>
                                <div class="form-group">
                                    <label for="description">
                                        <i class="fas fa-align-left"></i> Bio
                                    </label>
                                    <textarea class="form-control" id="description" name="description" rows="5"
                                              style="resize:none;" required
                                              placeholder="Tell others about yourself...">{{ $userInfo->description }}</textarea>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-star"></i> Interests
                                </h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tag1">Interest 1</label>
                                            <select class="form-control" id="tag1" name="tag1">
                                                <option value="{{ $userInfo->tag1 }}">{{ $userInfo->tag1 ?: 'None' }}</option>
                                                <option value="">None</option>
                                                <option value="Movie">Movie</option> 
                                                <option value="Travelling">Travelling</option> 
                                                <option value="Art">Art</option> 
                                                <option value="Sport">Sport</option> 
                                                <option value="Cooking">Cooking</option> 
                                                <option value="Gaming">Gaming</option> 
                                                <option value="Anime">Anime</option> 
                                                <option value="Reading">Reading</option> 
                                                <option value="Volunteering">Volunteering</option> 
                                                <option value="Photography">Photography</option>  
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tag2">Interest 2</label>
                                            <select class="form-control" id="tag2" name="tag2">
                                                <option value="{{ $userInfo->tag2 }}">{{ $userInfo->tag2 ?: 'None' }}</option>
                                                <option value="">None</option>
                                                <option value="Movie">Movie</option> 
                                                <option value="Travelling">Travelling</option> 
                                                <option value="Art">Art</option> 
                                                <option value="Sport">Sport</option> 
                                                <option value="Cooking">Cooking</option> 
                                                <option value="Gaming">Gaming</option> 
                                                <option value="Anime">Anime</option> 
                                                <option value="Reading">Reading</option> 
                                                <option value="Volunteering">Volunteering</option> 
                                                <option value="Photography">Photography</option>  
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tag3">Interest 3</label>
                                            <select class="form-control" id="tag3" name="tag3">
                                                <option value="{{ $userInfo->tag3 }}">{{ $userInfo->tag3 ?: 'None' }}</option>
                                                <option value="">None</option>
                                                <option value="Movie">Movie</option> 
                                                <option value="Travelling">Travelling</option> 
                                                <option value="Art">Art</option> 
                                                <option value="Sport">Sport</option> 
                                                <option value="Cooking">Cooking</option> 
                                                <option value="Gaming">Gaming</option> 
                                                <option value="Anime">Anime</option> 
                                                <option value="Reading">Reading</option> 
                                                <option value="Volunteering">Volunteering</option> 
                                                <option value="Photography">Photography</option>  
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(".js-range-slider").ionRangeSlider();
    </script>
@endsection

<style>
    .profile-page {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 30px 0;
    }

    .profile-sidebar {
        position: sticky;
        top: 20px;
    }

    .profile-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .profile-avatar-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-avatar-wrapper {
        position: relative;
        display: inline-block;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid #667eea;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .profile-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(102, 126, 234, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 50%;
    }

    .profile-avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }

    .avatar-upload-btn {
        color: white;
        cursor: pointer;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }

    .avatar-upload-btn i {
        font-size: 1.5rem;
    }

    .profile-info {
        text-align: center;
    }

    .profile-name {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .profile-joined {
        color: #718096;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .profile-joined i {
        margin-right: 5px;
        color: #667eea;
    }

    .profile-completeness {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 12px;
        margin-top: 15px;
    }

    .completeness-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .completeness-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .completeness-label i {
        color: #667eea;
    }

    .completeness-percentage {
        font-weight: 700;
        color: #667eea;
        font-size: 1.2rem;
    }

    .completeness-progress {
        height: 10px;
        border-radius: 10px;
        background: #e2e8f0;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .completeness-progress .progress-bar {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .completeness-tips {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #718096;
    }

    .completeness-tips i {
        color: #4299e1;
    }

    .completeness-complete {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #48bb78;
        font-weight: 600;
    }

    .profile-nav {
        background: white;
        border-radius: 20px;
        padding: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        border-radius: 12px;
        text-decoration: none;
        color: #2d3748;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-bottom: 5px;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .nav-item i {
        width: 20px;
        color: #667eea;
        font-size: 1.1rem;
    }

    .nav-item:hover {
        background: #f7fafc;
        color: #667eea;
        transform: translateX(5px);
    }

    .nav-item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .nav-item.active i {
        color: white;
    }

    .nav-item-danger {
        color: #f56565 !important;
    }

    .nav-item-danger i {
        color: #f56565 !important;
    }

    .nav-item-danger:hover {
        background: #fed7d7;
        color: #c53030 !important;
    }

    .delete-form {
        margin: 0;
        padding: 0;
    }

    .profile-content {
        background: white;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .content-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
    }

    .content-header h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .content-header h2 i {
        color: #667eea;
    }

    .content-header p {
        color: #718096;
        font-size: 1rem;
        margin: 0;
    }

    .form-section {
        margin-bottom: 35px;
        padding-bottom: 30px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #667eea;
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
    }

    .form-group label i {
        color: #667eea;
        font-size: 0.9rem;
    }

    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Enhanced Select Dropdown Styling */
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 12px;
        padding-right: 40px;
        cursor: pointer;
    }

    .form-select:hover {
        border-color: #cbd5e0;
    }

    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .dark-mode .form-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23a0aec0' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e2e8f0;
    }

    .btn-lg {
        padding: 12px 30px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #2d3748;
        border: none;
    }

    .btn-secondary:hover {
        background: #cbd5e0;
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 12px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        background: #c6f6d5;
        color: #22543d;
    }

    .alert-success i {
        margin-right: 8px;
    }

    /* Dark Mode */
    .dark-mode .profile-page {
        background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    }

    .dark-mode .profile-card,
    .dark-mode .profile-nav,
    .dark-mode .profile-content {
        background: var(--card-bg);
        color: var(--text-color);
    }

    .dark-mode .profile-name,
    .dark-mode .content-header h2,
    .dark-mode .section-title {
        color: var(--text-color);
    }

    .dark-mode .form-control {
        background: #4a5568;
        border-color: var(--border-color);
        color: var(--text-color);
    }

    .dark-mode .form-control:focus {
        border-color: #667eea;
    }

    .dark-mode .nav-item:hover {
        background: #4a5568;
    }

    /* Radio Group Styles */
    .radio-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .radio-option {
        flex: 1;
        min-width: 120px;
    }

    .radio-option input[type="radio"] {
        display: none;
    }

    .radio-option label {
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
        background: #ffffff;
        color: #1a202c;
    }

    .radio-option input[type="radio"]:checked + label {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .radio-option label:hover {
        border-color: #667eea;
    }

    .form-text {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 0.25rem;
        display: block;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .profile-sidebar {
            position: static;
            margin-bottom: 30px;
        }
    }
</style>

<script>
    // Phone number formatting - supports international formats
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // If starts with country code (longer numbers), format as international
        if (value.length > 10) {
            // International format: +XX (XXX) XXX-XXXX or +XXX (XXX) XXX-XXXX
            if (value.length <= 12) {
                value = '+' + value.slice(0, value.length - 10) + ' (' + value.slice(value.length - 10, value.length - 7) + ') ' + value.slice(value.length - 7, value.length - 4) + '-' + value.slice(value.length - 4);
            } else {
                // Very long numbers (like +60 11 535 9091)
                value = '+' + value.slice(0, value.length - 9) + ' (' + value.slice(value.length - 9, value.length - 6) + ') ' + value.slice(value.length - 6, value.length - 3) + '-' + value.slice(value.length - 3);
            }
        } else if (value.length > 0) {
            // Local format for shorter numbers
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else if (value.length <= 10) {
                value = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6);
            }
        }
        e.target.value = value;
    });
</script>
