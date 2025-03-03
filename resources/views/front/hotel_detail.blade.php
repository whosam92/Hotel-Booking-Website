@extends('front.layout.app')

@section('main_content')
<div class="container mt-4">
    <h2>{{ $hotel->name }}</h2>
    <p class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $hotel->location }}</p>

    <img src="{{ asset('uploads/hotels/'.$hotel->image) }}" class="img-fluid mb-3" alt="{{ $hotel->name }}">

    <h4>About this Hotel</h4>
    <p>{{ $hotel->description }}</p>

    <h4 class="mt-4">Available Rooms</h4>
    <div class="row">
        @foreach($hotel->rooms as $room)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ asset('uploads/'.$room->featured_photo) }}" class="card-img-top" alt="{{ $room->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $room->name }}</h5>
                        <p class="card-text">{{ $room->description }}</p>
                        <p class="price"><strong>${{ $room->price }}</strong> / night</p>
                        <a href="#" class="btn btn-primary w-100">Book Now</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
