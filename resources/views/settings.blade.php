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
                            </div>
                        </div>
                        
                        <!-- Navigation Menu -->
                        <div class="profile-nav">
                            <a href="{{ route('profile.updateProfile') }}" class="nav-item">
                                <i class="fas fa-user-edit"></i>
                                <span>Edit Profile</span>
                            </a>
                            <a href="{{ route('profile.updateSettings') }}" class="nav-item active">
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
                            <h2><i class="fas fa-cog"></i> Search Settings</h2>
                            <p>Customize your match preferences and discovery options</p>
                        </div>
                        
                        <form method='post' action="{{ route('profile.updateSettings') }}" class="profile-form">
                            @csrf
                            @method('put')

                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-sliders-h"></i> Basic Filters
                                </h4>
                                
                                <div class="form-group row">
                                    <label for="search_age_range" class="col-md-4 col-form-label">
                                        <i class="fas fa-birthday-cake"></i> Age Range
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" class="js-range-slider"
                                               name="search_age_range"
                                               id="search_age_range"
                                               value=""
                                               data-type="double"
                                               data-min="18"
                                               data-max="100"
                                               data-from="{{ $userSettings->search_age_from }}"
                                               data-to="{{ $userSettings->search_age_to }}"
                                        />
                                        <small class="form-text text-muted">Select the age range you're interested in</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">
                                        <i class="fas fa-venus-mars"></i> I am looking for
                                    </label>
                                    <div class="col-md-8">
                                        <div class="checkbox-group">
                                            <div class="custom-checkbox">
                                                <input class="form-check-input" type="checkbox" name="search_male"
                                                       id="search_male" value="1"
                                                       @if($userSettings->search_male == 1) checked @endif>
                                                <label class="form-check-label" for="search_male">
                                                    <i class="fas fa-mars"></i> <span>Male</span>
                                                </label>
                                            </div>
                                            <div class="custom-checkbox">
                                                <input class="form-check-input" type="checkbox" name="search_female"
                                                       id="search_female" value="1"
                                                       @if($userSettings->search_female == 1) checked @endif>
                                                <label class="form-check-label" for="search_female">
                                                    <i class="fas fa-venus"></i> <span>Female</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">
                                        <i class="fas fa-star"></i> Interests
                                    </label>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select class="form-control" id="search_tag1" name="search_tag1">
                                                    <option value="{{ $userSettings->search_tag1 }}">{{ $userSettings->search_tag1 ?: 'None' }}</option>
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
                                            <div class="col-md-4">
                                                <select class="form-control" id="search_tag2" name="search_tag2">
                                                    <option value="{{ $userSettings->search_tag2 }}">{{ $userSettings->search_tag2 ?: 'None' }}</option>
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
                                            <div class="col-md-4">
                                                <select class="form-control" id="search_tag3" name="search_tag3">
                                                    <option value="{{ $userSettings->search_tag3 }}">{{ $userSettings->search_tag3 ?: 'None' }}</option>
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
                            </div>

                            <!-- Advanced Filters -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-filter"></i> Advanced Filters
                                </h4>
                                
                                <div class="form-group row">
                                    <label for="search_country" class="col-md-4 col-form-label">
                                        <i class="fas fa-globe"></i> Country
                                    </label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="search_country" name="search_country">
                                            <option value="">Any Country</option>
                                            <option value="Latvia" {{ $userSettings->search_country == 'Latvia' ? 'selected' : '' }}>Latvia</option>
                                            <option value="United States" {{ $userSettings->search_country == 'United States' ? 'selected' : '' }}>United States</option>
                                            <option value="United Kingdom" {{ $userSettings->search_country == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                            <option value="Canada" {{ $userSettings->search_country == 'Canada' ? 'selected' : '' }}>Canada</option>
                                            <option value="Germany" {{ $userSettings->search_country == 'Germany' ? 'selected' : '' }}>Germany</option>
                                            <option value="France" {{ $userSettings->search_country == 'France' ? 'selected' : '' }}>France</option>
                                            <option value="Spain" {{ $userSettings->search_country == 'Spain' ? 'selected' : '' }}>Spain</option>
                                            <option value="Italy" {{ $userSettings->search_country == 'Italy' ? 'selected' : '' }}>Italy</option>
                                            <option value="Netherlands" {{ $userSettings->search_country == 'Netherlands' ? 'selected' : '' }}>Netherlands</option>
                                            <option value="Poland" {{ $userSettings->search_country == 'Poland' ? 'selected' : '' }}>Poland</option>
                                            <option value="Sweden" {{ $userSettings->search_country == 'Sweden' ? 'selected' : '' }}>Sweden</option>
                                            <option value="Norway" {{ $userSettings->search_country == 'Norway' ? 'selected' : '' }}>Norway</option>
                                            <option value="Denmark" {{ $userSettings->search_country == 'Denmark' ? 'selected' : '' }}>Denmark</option>
                                            <option value="Finland" {{ $userSettings->search_country == 'Finland' ? 'selected' : '' }}>Finland</option>
                                            <option value="Australia" {{ $userSettings->search_country == 'Australia' ? 'selected' : '' }}>Australia</option>
                                            <option value="New Zealand" {{ $userSettings->search_country == 'New Zealand' ? 'selected' : '' }}>New Zealand</option>
                                            <option value="Japan" {{ $userSettings->search_country == 'Japan' ? 'selected' : '' }}>Japan</option>
                                            <option value="South Korea" {{ $userSettings->search_country == 'South Korea' ? 'selected' : '' }}>South Korea</option>
                                            <option value="China" {{ $userSettings->search_country == 'China' ? 'selected' : '' }}>China</option>
                                            <option value="India" {{ $userSettings->search_country == 'India' ? 'selected' : '' }}>India</option>
                                            <option value="Brazil" {{ $userSettings->search_country == 'Brazil' ? 'selected' : '' }}>Brazil</option>
                                            <option value="Mexico" {{ $userSettings->search_country == 'Mexico' ? 'selected' : '' }}>Mexico</option>
                                            <option value="Argentina" {{ $userSettings->search_country == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                                            <option value="Russia" {{ $userSettings->search_country == 'Russia' ? 'selected' : '' }}>Russia</option>
                                            <option value="Turkey" {{ $userSettings->search_country == 'Turkey' ? 'selected' : '' }}>Turkey</option>
                                            <option value="South Africa" {{ $userSettings->search_country == 'South Africa' ? 'selected' : '' }}>South Africa</option>
                                            <option value="Egypt" {{ $userSettings->search_country == 'Egypt' ? 'selected' : '' }}>Egypt</option>
                                            <option value="Thailand" {{ $userSettings->search_country == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                                            <option value="Singapore" {{ $userSettings->search_country == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                                            <option value="Malaysia" {{ $userSettings->search_country == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                            <option value="Philippines" {{ $userSettings->search_country == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                                            <option value="Indonesia" {{ $userSettings->search_country == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                            <option value="Vietnam" {{ $userSettings->search_country == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                                            <option value="UAE" {{ $userSettings->search_country == 'UAE' ? 'selected' : '' }}>United Arab Emirates</option>
                                            <option value="Saudi Arabia" {{ $userSettings->search_country == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia</option>
                                            <option value="Israel" {{ $userSettings->search_country == 'Israel' ? 'selected' : '' }}>Israel</option>
                                            <option value="Greece" {{ $userSettings->search_country == 'Greece' ? 'selected' : '' }}>Greece</option>
                                            <option value="Portugal" {{ $userSettings->search_country == 'Portugal' ? 'selected' : '' }}>Portugal</option>
                                            <option value="Ireland" {{ $userSettings->search_country == 'Ireland' ? 'selected' : '' }}>Ireland</option>
                                            <option value="Switzerland" {{ $userSettings->search_country == 'Switzerland' ? 'selected' : '' }}>Switzerland</option>
                                            <option value="Austria" {{ $userSettings->search_country == 'Austria' ? 'selected' : '' }}>Austria</option>
                                            <option value="Belgium" {{ $userSettings->search_country == 'Belgium' ? 'selected' : '' }}>Belgium</option>
                                            <option value="Czech Republic" {{ $userSettings->search_country == 'Czech Republic' ? 'selected' : '' }}>Czech Republic</option>
                                            <option value="Hungary" {{ $userSettings->search_country == 'Hungary' ? 'selected' : '' }}>Hungary</option>
                                            <option value="Romania" {{ $userSettings->search_country == 'Romania' ? 'selected' : '' }}>Romania</option>
                                            <option value="Bulgaria" {{ $userSettings->search_country == 'Bulgaria' ? 'selected' : '' }}>Bulgaria</option>
                                            <option value="Croatia" {{ $userSettings->search_country == 'Croatia' ? 'selected' : '' }}>Croatia</option>
                                            <option value="Serbia" {{ $userSettings->search_country == 'Serbia' ? 'selected' : '' }}>Serbia</option>
                                            <option value="Ukraine" {{ $userSettings->search_country == 'Ukraine' ? 'selected' : '' }}>Ukraine</option>
                                            <option value="Other" {{ $userSettings->search_country == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="search_relationship" class="col-md-4 col-form-label">
                                        <i class="fas fa-heart"></i> Relationship Status
                                    </label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="search_relationship" name="search_relationship">
                                            <option value="">Any Status</option>
                                            <option value="Single" {{ $userSettings->search_relationship == 'Single' ? 'selected' : '' }}>Single</option>
                                            <option value="Taken" {{ $userSettings->search_relationship == 'Taken' ? 'selected' : '' }}>Taken</option>
                                            <option value="Engaged" {{ $userSettings->search_relationship == 'Engaged' ? 'selected' : '' }}>Engaged</option>
                                            <option value="Married" {{ $userSettings->search_relationship == 'Married' ? 'selected' : '' }}>Married</option>
                                            <option value="It's complicated" {{ $userSettings->search_relationship == "It's complicated" ? 'selected' : '' }}>It's complicated</option>
                                            <option value="Open relationship" {{ $userSettings->search_relationship == 'Open relationship' ? 'selected' : '' }}>Open relationship</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">
                                        <i class="fas fa-toggle-on"></i> Options
                                    </label>
                                    <div class="col-md-8">
                                        <div class="checkbox-group">
                                            <div class="custom-checkbox">
                                                <input class="form-check-input" type="checkbox" name="search_has_photos" 
                                                       id="search_has_photos" value="1"
                                                       {{ $userSettings->search_has_photos == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="search_has_photos">
                                                    <i class="fas fa-images"></i> <span>Only show users with photos</span>
                                                </label>
                                            </div>
                                            <div class="custom-checkbox">
                                                <input class="form-check-input" type="checkbox" name="search_online_now" 
                                                       id="search_online_now" value="1"
                                                       {{ $userSettings->search_online_now == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="search_online_now">
                                                    <i class="fas fa-circle"></i> <span>Show online users first</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Save Settings
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
        $(".js-range-slider").ionRangeSlider({
            skin: "round",
            type: "double",
            min: 18,
            max: 100,
            from: {{ $userSettings->search_age_from }},
            to: {{ $userSettings->search_age_to }},
            grid: true,
            grid_num: 10
        });
    </script>
@endsection

<style>
    /* Reuse styles from profile page */
    .profile-page {
        min-height: calc(100vh - 76px);
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

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .custom-checkbox {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f7fafc;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .custom-checkbox:hover {
        background: #edf2f7;
    }

    .custom-checkbox input[type="checkbox"] {
        width: 22px;
        height: 22px;
        cursor: pointer;
        accent-color: #667eea;
        margin: 0;
        flex-shrink: 0;
        margin-right: 5px;
    }

    .custom-checkbox label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }
    
    .custom-checkbox label i {
        margin-right: 0;
        width: 18px;
        text-align: center;
    }
    
    .custom-checkbox label span {
        margin-left: 0;
    }

    .custom-checkbox label i {
        color: #667eea;
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

    .dark-mode .custom-checkbox {
        background: #4a5568;
    }

    .dark-mode .custom-checkbox:hover {
        background: #5a6578;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .profile-sidebar {
            position: static;
            margin-bottom: 30px;
        }
    }
</style>
