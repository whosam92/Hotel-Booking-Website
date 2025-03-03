@extends('front.layout.app')

@section('main_content')
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $single_room_data->name }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content room-detail">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-7 col-sm-12 left">

                <div class="room-detail-carousel owl-carousel">
                    <div class="item" style="background-image:url({{ asset('uploads/'.$single_room_data->featured_photo) }});">
                        <div class="bg"></div>
                    </div>
                    
                    @foreach($single_room_data->rRoomPhoto as $item)
                    <div class="item" style="background-image:url({{ asset('uploads/'.$item->photo) }});">
                        <div class="bg"></div>
                    </div>
                    @endforeach
                </div>
                
                <div class="description">
                    {!! $single_room_data->description !!}
                </div>

                <div class="amenity">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Amenities</h2>
                        </div>
                    </div>
                    <div class="row">
                        @php
                        $arr = explode(',', $single_room_data->amenities);
                        foreach ($arr as $amenity_id) {
                            $amenity = \App\Models\Amenity::find($amenity_id);
                            if ($amenity) {
                                echo '<div class="col-lg-6 col-md-12">';
                                echo '<div class="item"><i class="fa fa-check-circle"></i> ' . $amenity->name . '</div>';
                                echo '</div>';
                            }
                        }
                        @endphp
                    </div>
                </div>

                <div class="feature">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Features</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Room Size</th>
                                <td>{{ $single_room_data->size }}</td>
                            </tr>
                            <tr>
                                <th>Number of Beds</th>
                                <td>{{ $single_room_data->total_beds }}</td>
                            </tr>
                            <tr>
                                <th>Number of Bathrooms</th>
                                <td>{{ $single_room_data->total_bathrooms }}</td>
                            </tr>
                            <tr>
                                <th>Number of Balconies</th>
                                <td>{{ $single_room_data->total_balconies }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($single_room_data->video_id)
                <div class="video">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $single_room_data->video_id }}" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
                </div>
                @endif
<!-- Reviews Section --> 
<div class="reviews mt-4">
    <h2 class="text-white">Customer Reviews</h2>

    @if($single_room_data->reviews->count() > 0)
        @foreach($single_room_data->reviews as $review)
            <div class="review-box p-3 mb-3 shadow-sm rounded position-relative" style="background: #F3F3F3; color:#E75542;">
                <div class="d-flex align-items-center">
                    <div class="ms-3">
                        <strong>{{ $review->customer->name }}</strong> 
                        <small class="d-block text-dark">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="rating">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i>
                        @endfor
                        ({{ $review->rating }}/5)
                    </span>
                    <p class="mt-2">{{ $review->review }}</p>

                    <!-- Display Multiple Admin Replies -->
                    @if($review->replies->count() > 0)
                        <div class="admin-replies mt-2 p-2 rounded" style="background: #E9ECEF; color:#333; border-left: 4px solid #007BFF;">
                            <strong>Admin Replies:</strong>
                            @foreach($review->replies as $reply)
                            @if($reply->admin)
                                <div class="mt-2 p-2 border-bottom">
                                    <strong>{{ $reply->admin->name }}:</strong> {{ $reply->reply }}
                                    <small class="text-muted d-block">{{ $reply->created_at->diffForHumans() }}</small>
                                </div>
                            @else
                                <div class="mt-2 p-2 border-bottom">
                                    <strong>Admin:</strong> {{ $reply->reply }}
                                    <small class="text-danger">Admin not found!</small>
                                </div>
                            @endif
                        @endforeach
                        
                        </div>
                    @endif

                    <!-- Admin Reply Form (Only for Logged-in Admins) -->
                    @if(Auth::guard('admin')->check())
                        <form action="{{ url('/reviews/'.$review->id.'/reply') }}" method="POST" class="mt-2">
                            @csrf
                            <div class="form-group">
                                <textarea name="reply" class="form-control" rows="2" required placeholder="Write your reply...">{{ old('reply') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-sm mt-2" style="background: #007BFF; color: white;">Reply</button>
                        </form>
                    @else
                        <!-- Show Disabled Reply Box & Login Message -->
                        <div class="form-group mt-2">
                            <textarea class="form-control" rows="2" placeholder="You must be logged in as an admin to reply." disabled></textarea>
                        </div>
                        <p class="text-danger mt-1"><strong>Note:</strong> Only admins can reply. <a href="{{ route('admin_login') }}">Login as Admin</a></p>
                    @endif

                    <!-- Edit & Delete Buttons (Only for Logged-in Customer) -->
                    @if(Auth::guard('customer')->id() == $review->customer_id)
                        <div class="position-absolute" style="top: 10px; right: 10px;">
                            <form action="{{ route('review.destroy', $review->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this review?')">❌ Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="text-dark">No reviews yet. Be the first to leave a review!</p>
    @endif
</div>

<!-- Add Review Section -->
@if((Auth::guard('customer')->check() && $hasBooked) || Auth::guard('admin')->check())
    <div class="add-review mt-4 p-4 rounded" style="background:#F3F3F3 ; color: #E75542;">
        <h2>Leave a Review</h2>
        <form action="{{ route('review.store', $single_room_data->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="rating">Rating</label>
                <div class="star-rating">
                    @for($i = 5; $i >= 1; $i--) {{-- Reverse order for correct click selection --}}
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                        <label for="star{{ $i }}" class="star">&#9733;</label>
                    @endfor
                </div>
            </div>
            <div class="form-group mt-3">
                <label for="review">Your Review</label>
                <textarea name="review" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn mt-2" style="background: #E75542; color: white;">Submit Review</button>
        </form>
    </div>
@else
    <p class="text-danger mt-3">You must be logged in and have booked this room to leave a review.</p>
@endif
</div>
<!-- End of Reviews Section -->

<!-- Star Rating CSS -->
<style>
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-start;
        font-size: 26px;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        cursor: pointer;
        color: #908787;
        transition: color 0.2s ease-in-out;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: gold;
    }

    .review-box {
        border-left: 4px solid #fff;
        padding: 15px;
        position: relative;
    }

    .admin-reply {
        font-size: 14px;
        margin-top: 5px;
    }
</style>


            <div class="col-lg-4 col-md-5 col-sm-12 right">
                <div class="sidebar-container" id="sticky_sidebar">
                    <div class="widget">
                        <h2>Room Price per Night</h2>
                        <div class="price">
                            ${{ $single_room_data->price }}
                        </div>
                    </div>
                    <div class="widget">
                        <h2>Reserve This Room</h2>
                        <form action="{{ route('cart_submit') }}" method="post">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $single_room_data->id }}">
                            <div class="form-group mb_20">
                                <label for="">Check in & Check out</label>
                                <input type="text" name="checkin_checkout" class="form-control daterange1" placeholder="Checkin & Checkout">
                            </div>
                            <div class="form-group mb_20">
                                <label for="">Adult</label>
                                <input type="number" name="adult" class="form-control" min="1" max="30" placeholder="Adults">
                            </div>
                            <div class="form-group mb_20">
                                <label for="">Children</label>
                                <input type="number" name="children" class="form-control" min="0" max="30" placeholder="Children">
                            </div>
                            <button type="submit" class="book-now">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    @foreach($errors->all() as $error)
        <script>
            iziToast.error({
                title: '',
                position: 'topRight',
                message: '{{ $error }}',
            });
        </script>
    @endforeach
@endif

@endsection


{{-- js for stars rating to fix issue   --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let stars = document.querySelectorAll('.star-rating label');
        stars.forEach(function (star) {
            star.addEventListener('click', function () {
                let selectedValue = this.previousElementSibling.value;
                document.querySelectorAll('.star-rating label').forEach(label => {
                    label.style.color = (label.previousElementSibling.value <= selectedValue) ? 'gold' : '#908787';
                });
            });
        });
    });
    </script>
    