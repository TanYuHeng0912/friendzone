<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/admin.Home', 'HomeController@adminHome')->name('admin.dashboard');
    // Other admin routes
});
Route::get('/matches', 'MatchesController@matches')->name('matches');

Route::get('/pictures', 'UserPicturesController@show')->name('pictures.show');
Route::post('/pictures', 'UserPicturesController@addPictures')->name('pictures.add');
Route::delete('/pictures/{id}', 'UserPicturesController@destroyPicture')->name('pictures.destroy');

Route::get('/profile/edit', 'EditUserProfileController@show')->name('profile.showEditProfile');
Route::post('/profile/edit', 'EditUserProfileController@updateProfile')->name('profile.updateProfile');
Route::put('/profile/edit', 'EditUserProfileController@updateProfilePicture')->name('profile.updateProfilePicture');

Route::get('/profile/settings', 'EditUserProfileController@showSettings')->name('profile.showSettings');
Route::put('/profile/settings', 'EditUserProfileController@updateSettings')->name('profile.updateSettings');

Route::delete('/profile', 'EditUserProfileController@destroyProfile')->name('profile.destroy');
Route::post('/profile/like/{id}', 'ReactionController@like')->name('like');
Route::post('/profile/dislike/{id}', 'ReactionController@dislike')->name('dislike');
Route::get('/feedback', 'FeedbackController@create')->name('feedback.create');
Route::post('/feedback', 'FeedbackController@store')->name('feedback.store');
// Chat routes
Route::middleware('auth')->group(function () {
    // Chat index - show all chats
    Route::get('/chat', 'ChatController@index')->name('chat.index');
    
    // Show specific chat room
    Route::get('/chat/{chat}', 'ChatController@show')->name('chat.show');
    
    // Create chat with a matched user
    Route::post('/chat/create/{user}', 'ChatController@createChat')->name('chat.create');
    
    // Send message
    Route::post('/chat/{chat}/send', 'ChatController@sendMessage')->name('chat.send');
    
    // Get new messages (for AJAX polling)
    Route::get('/chat/{chat}/messages/{lastMessageId?}', 'ChatController@getMessages')->name('chat.messages');

    Route::post('/chat/{chat}/send-voice', 'ChatController@sendVoiceMessage')->name('chat.sendVoice');
});

// Community routes
Route::middleware('auth')->group(function () {
    // Community index - show all communities
    Route::get('/communities', 'CommunityController@index')->name('community.index');
    
    // Show specific community
    Route::get('/community/{community}', 'CommunityController@show')->name('community.show');
    
    // Join/Leave community
    Route::post('/community/{community}/join', 'CommunityController@join')->name('community.join');
    Route::delete('/community/{community}/leave', 'CommunityController@leave')->name('community.leave');
    
    // Post management
    Route::get('/community/{community}/create-post', 'CommunityController@createPost')->name('community.create-post');
    Route::post('/community/{community}/posts', 'CommunityController@storePost')->name('community.store-post');
    Route::get('/community/{community}/post/{post}', 'CommunityController@showPost')->name('community.post');
    
    // Post interactions
    Route::post('/community/post/{post}/like', 'CommunityController@likePost')->name('community.like-post');
    Route::post('/community/post/{post}/comment', 'CommunityController@storeComment')->name('community.comment');
});

// Add these routes to your existing web.php file

// Admin routes (add after the existing admin routes section)
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', 'Admin\AdminDashboardController@index')->name('dashboard');
    
    // User Management
    Route::get('/users', 'Admin\AdminDashboardController@users')->name('users');
    Route::patch('/users/{user}/ban', 'Admin\AdminDashboardController@banUser')->name('users.ban');
    Route::patch('/users/{user}/unban', 'Admin\AdminDashboardController@unbanUser')->name('users.unban');
    Route::patch('/users/{user}/suspend', 'Admin\AdminDashboardController@suspendUser')->name('users.suspend');
    Route::patch('/users/{user}/unsuspend', 'Admin\AdminDashboardController@unsuspendUser')->name('users.unsuspend');
    
    // Community Management
    Route::get('/communities', 'Admin\AdminDashboardController@communities')->name('communities');
    Route::get('/communities/create', 'Admin\AdminDashboardController@createCommunity')->name('communities.create');
    Route::post('/communities', 'Admin\AdminDashboardController@storeCommunity')->name('communities.store');
    Route::get('/communities/{community}/edit', 'Admin\AdminDashboardController@editCommunity')->name('communities.edit');
    Route::patch('/communities/{community}', 'Admin\AdminDashboardController@updateCommunity')->name('communities.update');
    Route::delete('/communities/{community}', 'Admin\AdminDashboardController@deleteCommunity')->name('communities.delete');
    
    // Feedback Management
    Route::get('/feedback', 'Admin\AdminDashboardController@feedback')->name('feedback');
    Route::get('/feedback/{feedback}', 'Admin\AdminDashboardController@showFeedback')->name('feedback.show');
    Route::patch('/feedback/{feedback}/mark-read', 'Admin\AdminDashboardController@markFeedbackAsRead')->name('feedback.mark-read');
    Route::delete('/feedback/{feedback}', 'Admin\AdminDashboardController@deleteFeedback')->name('feedback.delete');
});