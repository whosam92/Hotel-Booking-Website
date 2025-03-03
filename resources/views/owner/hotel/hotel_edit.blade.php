@extends('owner.layout.app')

@section('heading', 'Edit Hotel')

@section('right_top_button')
<a href="{{ route('owner.hotel_view') }}" class="btn btn-primary"><i class="fa fa-eye"></i> View All</a>
@endsection

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('owner.hotel_update', $hotel->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label class="form-label">Name *</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $hotel->name) }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control snote" cols="30" rows="10">{{ old('description', $hotel->description) }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Location *</label>
                                    <input type="text" class="form-control" name="location" value="{{ old('location', $hotel->location) }}">
                                </div>
                                
                                <div class="mb-4">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
