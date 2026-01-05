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
Route::post('/pictures/reorder', 'UserPicturesController@reorderPictures')->name('pictures.reorder');
Route::delete('/pictures/{id}', 'UserPicturesController@destroyPicture')->name('pictures.destroy');

Route::get('/profile/edit', 'EditUserProfileController@show')->name('profile.showEditProfile');
Route::post('/profile/edit', 'EditUserProfileController@updateProfile')->name('profile.updateProfile');
Route::put('/profile/edit', 'EditUserProfileController@updateProfilePicture')->name('profile.updateProfilePicture');

Route::get('/profile/settings', 'EditUserProfileController@showSettings')->name('profile.showSettings');
Route::put('/profile/settings', 'EditUserProfileController@updateSettings')->name('profile.updateSettings');

Route::delete('/profile', 'EditUserProfileController@destroyProfile')->name('profile.destroy');
Route::post('/profile/like/{id}', 'ReactionController@like')->name('like');
Route::post('/profile/dislike/{id}', 'ReactionController@dislike')->name('dislike');
Route::get('/profile/compatibility/{id}', 'ReactionController@getCompatibility')->name('compatibility');
Route::get('/test-matches', 'TestMatchController@test')->name('test.matches')->middleware('auth');
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
    Route::get('/chat/{chat}/messages', 'ChatController@getMessages')->name('chat.messages');

    Route::post('/chat/{chat}/send-voice', 'ChatController@sendVoiceMessage')->name('chat.sendVoice');
    
    // Typing indicator
    Route::post('/chat/{chat}/typing', 'ChatController@setTyping')->name('chat.typing');
    Route::get('/chat/{chat}/typing-status', 'ChatController@getTypingStatus')->name('chat.typingStatus');
    
    // Message reactions
    Route::post('/chat/message/{message}/reaction', 'ChatController@addReaction')->name('chat.addReaction');
    Route::delete('/chat/message/{message}/reaction/{reaction}', 'ChatController@removeReaction')->name('chat.removeReaction');
    
    // Reply to message
    Route::post('/chat/{chat}/reply', 'ChatController@replyToMessage')->name('chat.reply');
    
    // Message search
    Route::get('/chat/{chat}/search', 'ChatController@searchMessages')->name('chat.search');
    
    // Media gallery
    Route::get('/chat/{chat}/media', 'ChatMediaController@index')->name('chat.media');
    Route::get('/chat/{chat}/media/api', 'ChatMediaController@getMedia')->name('chat.media.api');
    
    // Call routes
    Route::post('/call/initiate', 'CallController@initiate')->name('call.initiate');
    Route::post('/call/{callId}/answer', 'CallController@answer')->name('call.answer');
    Route::post('/call/{callId}/end', 'CallController@end')->name('call.end');
    Route::post('/call/{callId}/ice-candidate', 'CallController@iceCandidate')->name('call.ice-candidate');
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
    
    // Poll voting
    Route::post('/poll/{poll}/vote', 'PollController@vote')->name('poll.vote');
    
    // Event RSVP
    Route::post('/event/{event}/rsvp', 'EventController@rsvp')->name('event.rsvp');
    Route::delete('/event/{event}/rsvp', 'EventController@cancelRsvp')->name('event.cancel-rsvp');
});

// Friend routes
Route::middleware('auth')->prefix('friends')->name('friends.')->group(function () {
    Route::post('/request/{user}', 'FriendController@sendRequest')->name('request');
    Route::post('/accept/{friendship}', 'FriendController@acceptRequest')->name('accept');
    Route::post('/reject/{friendship}', 'FriendController@rejectRequest')->name('reject');
    Route::delete('/remove/{friendship}', 'FriendController@removeFriend')->name('remove');
    Route::get('/list', 'FriendController@getFriends')->name('list');
    Route::get('/requests', 'FriendController@getPendingRequests')->name('requests');
});

// Activity feed routes
Route::middleware('auth')->prefix('activity')->name('activity.')->group(function () {
    Route::get('/', 'ActivityController@index')->name('index');
    Route::get('/api', 'ActivityController@api')->name('api');
});

// Chat media routes
Route::middleware('auth')->group(function () {
    Route::post('/chat/{chat}/send-media', 'ChatController@sendMedia')->name('chat.send-media');
    Route::get('/chat/{chat}/gif-search', 'ChatController@searchGifs')->name('chat.gif-search');
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