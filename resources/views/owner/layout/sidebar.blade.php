<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('owner_home') }}">Owner Panel</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('owner_home') }}"></a>
        </div>

        <ul class="sidebar-menu">

            <li class="{{ Request::is('owner/home') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_home') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Dashboard"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>

            <li class="{{ Request::is('owner/setting') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_hotel') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Setting"><i class="fa fa-hotel"></i> <span>Hotels</span></a></li>

            <li class="nav-item dropdown {{ Request::is('owner/amenity/view')||Request::is('owner/room/view') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fa fa-superpowers"></i><span>Room Section</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('owner/amenity/view') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_amenity_view') }}"><i class="fa fa-angle-right"></i> Amenities</a></li>

                    <li class="{{ Request::is('owner/room/view') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_room_view') }}"><i class="fa fa-angle-right"></i> Rooms</a></li>
                </ul>
            </li>


            <li class="{{ Request::is('owner/datewise-rooms') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_datewise_rooms') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Datewise Rooms"><i class="fa fa-calendar"></i> <span>Datewise Rooms</span></a></li>

            <li class="{{ Request::is('owner/customers') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_customer') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Customers"><i class="fa fa-user-plus"></i> <span>Customers</span></a></li>

            <li class="{{ Request::is('owner/order/*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_orders') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Orders"><i class="fa fa-cart-plus"></i> <span>Orders</span></a></li>


            <li class="{{ Request::is('owner/feature/*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_feature_view') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Feature"><i class="fa fa-gavel"></i> <span>Feature</span></a></li>


            <li class="{{ Request::is('owner/photo/*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_photo_view') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Photo Gallery"><i class="fa fa-picture-o"></i> <span>Photo Gallery</span></a></li>

            <li class="{{ Request::is('owner/video/*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('owner_video_view') }}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Video Gallery"><i class="fa fa-camera"></i> <span>Video Gallery</span></a></li>



            

        </ul>
    </aside>
</div>