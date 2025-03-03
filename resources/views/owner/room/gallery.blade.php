@extends('owner.layout.app')

@section('heading', 'Room Gallery of ' . ($room_data->name ?? 'Unknown Room'))

@section('right_top_button')
<a href="{{ route('owner_room_view') }}" class="btn btn-primary"><i class="fa fa-eye"></i> Back to previous</a>
@endsection

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- Display validation errors if any --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Upload Form --}}
                    <form action="{{ route('owner_room_gallery_store', $room_data->id ?? 0) }}" 
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label class="form-label">Photo *</label>
                                    <div>
                                        <input type="file" name="photos[]" multiple required class="form-control">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Display Uploaded Photos --}}
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="example1">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($room_photos) && count($room_photos) > 0)
                                    @foreach($room_photos as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <img src="{{ asset('uploads/' . ltrim($row->photo, '/')) }}" 
                                            alt="Room Image" class="w_200" 
                                            onerror="this.onerror=null;this.src='{{ asset('default-placeholder.png') }}';">
                                                                               </td>
                                        <td class="pt_10 pb_10">
                                            <a href="{{ route('owner_room_gallery_delete', $row->id) }}" 
                                               class="btn btn-danger" 
                                               onClick="return confirm('Are you sure?');">Delete</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">No photos uploaded yet.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
