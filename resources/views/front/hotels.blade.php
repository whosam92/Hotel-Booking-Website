@extends('front.layout.app')

@section('main_content')
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>All Hotels</h2>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="container mt-4">
    <form action="{{ route('hotels.index') }}" method="GET" id="filterForm">
        <div class="row">
            <!-- Search by Name -->
            <div class="col-md-4">
                <input type="text" name="search" class="form-control search-input" placeholder="Search by Hotel Name or Location" value="{{ request('search') }}">
            </div>
            <!-- Filter by Price -->
            <div class="col-md-3">
                <select name="price_filter" class="form-control filter-select">
                    <option value="">Filter by Price</option>
                    <option value="low" {{ request('price_filter') == 'low' ? 'selected' : '' }}>Low to High</option>
                    <option value="high" {{ request('price_filter') == 'high' ? 'selected' : '' }}>High to Low</option>
                </select>
            </div>
            <!-- Search Button -->
            <div class="col-md-2">
                <button type="submit" class="btn search-btn w-100">Search</button>
            </div>
            <!-- Reset Button -->
            <div class="col-md-2">
                <button type="button" class="btn search-btn w-100" onclick="resetFilters()">Reset</button>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript for Reset Button -->
<script>
    function resetFilters() {
        window.location.href = "{{ route('hotels.index') }}"; // Redirects back to default page
    }
</script>
<!-- Hotel Listing Section -->

<div class="container mt-5">
    <div class="row">
        @foreach($hotels as $hotel)
            <div class="col-md-12">
                <div class="hotel-box">
                    <h2 class="hotel-title">{{ $hotel->name }}</h2>
                    <p class="hotel-description">{{ $hotel->description }}</p>
                    <p class="hotel-location"><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> {{ $hotel->location }}</p>

                    <h4 class="room-heading">Available Rooms:</h4>
                    <div class="row">
                        @foreach($hotel->rooms as $room)
                            <div class="col-md-3">
                                <div class="room-card">
                                    <img src="{{ asset('uploads/'.$room->featured_photo) }}" alt="Room Photo" class="img-fluid room-img">
                                    <h5 class="room-title">{{ $room->name }}</h5>
                                    <p class="room-price"><strong>Price:</strong> ${{ $room->price }} per night</p>
                                    <a href="{{ route('room_detail', $room->id) }}" class="btn btn-details">See Details</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Custom Styles with #E75542 Theme -->
<style>
    body {
        background-color: #f8f9fa;
    }

    /* Search and Filter */
    .search-input, .filter-select {
        border: 2px solid #E75542;
        border-radius: 5px;
        padding: 8px;
    }

    .search-btn {
        background-color: #E75542;
        color: #fff;
        border-radius: 5px;
    }

    .search-btn:hover {
        background-color: #d14535;
    }

    /* Hotel Box */
    .hotel-box {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        border-left: 5px solid #E75542;
    }

    .hotel-title {
        font-size: 24px;
        font-weight: bold;
        color: #E75542;
    }

    .hotel-description {
        font-size: 16px;
        color: #555;
    }

    .hotel-location {
        font-size: 15px;
        color: #777;
    }

    /* Room Card */
    .room-card {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 8px;
        background: #fff;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.3s ease-in-out;
    }

    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
    }

    .room-img {
        max-height: 180px;
        object-fit: cover;
        width: 100%;
        border-radius: 8px;
    }

    .room-title {
        font-size: 18px;
        margin-top: 10px;
        font-weight: bold;
        color: #333;
    }

    .room-price {
        font-size: 16px;
        color: #666;
    }

    .btn-details {
        background-color: #E75542;
        color: #fff;
        border-radius: 5px;
        padding: 8px 15px;
        text-decoration: none;
    }

    .btn-details:hover {
        background-color: #d14535;
    }
</style>

@endsection
