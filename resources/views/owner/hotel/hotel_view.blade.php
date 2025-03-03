@extends('owner.layout.app')

@section('heading', 'Hotels')

@section('right_top_button')
<a href="{{ route('owner.hotel_create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Hotel</a>
@endsection

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hotel Name</th>
                                <th>Description</th>
                                <th>location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hotels as $hotel)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $hotel->name }}</td>
                                <td>{{ $hotel->description }}</td>
                                <td>{{ $hotel->location }}</td>
                                <td>
                                    <a href="{{ route('owner.hotel_edit', $hotel->id) }}" class="btn btn-warning">Edit</a>
                                    <form action="{{ route('owner.hotel_destroy', $hotel->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
