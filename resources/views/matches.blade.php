@extends('layouts.app')

@section('content')
    <div class="container">
        @if(count($users) == 0)
            <div class="text-center empty">
                <h2>Nothing to show here yet...</h2>
            </div>
        @else
            <div class="row">
                @foreach($users as $otherUser)
                    <div class="card">
                        <div class="row justify-content-center">
                            <div class="col-6 text-right">
                                <img src="{{ $otherUser->info->getPicture() }}"
                                     alt="Image of the person"
                                     id="profile_picture"
                                >
                            </div>
                            <div class="col-6">
                                <h2>{{ $otherUser->info->name . ' ' . $otherUser->info->surname . ', ' . $otherUser->info->age }}</h2>
                                <br>
                                <div class="row">
                                    <div class="col-4">
                                        <h5>Country: {{ $otherUser->info->country }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <h5>Languages: {{ $otherUser->info->languages }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <h5>Relationship status: {{ $otherUser->info->relationship }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <h5>Interest on: {{ $otherUser->info->tag1 }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <h5>Interest on: {{ $otherUser->info->tag2 }}</h5>
                                    </div>
                                    <div class="col-4">
                                        <h5>Interest on: {{ $otherUser->info->tag3 }}</h5>
                                    </div>
                                </div>
                                <br>
                                <h3>Bio:</h3>
                                <p id="description">{{ $otherUser->info->description}}</p>
                                <p>e-mail: {{ $otherUser->email }}</p>
                                <p>phone number: {{ $otherUser->info->phone }}</p>
                                
                                <!-- Chat Button -->
                                <div class="mt-3">
                                    <form action="{{ route('chat.create', $otherUser->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-chat">
                                            <i class="fas fa-comments"></i> Start Chat
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
        @endif
    </div>


<style>
    .row.justify-content-center {
        width: 100%;
    }

    .empty {
        margin-top: 30%;
        color: black;
        text-shadow: 3px 4px 3px rgba(0, 0, 0, 0.3);
    }

    .card {
        padding: 20px;
        margin: 20px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    #profile_picture {
        width: 100%;
        max-height: 100%;
        border-radius: 10px;
    }

    #description {
        font-size: 18px;
    }

    .btn-chat {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-chat:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }

    .btn-chat i {
        margin-right: 8px;
    }
</style>
@endsection