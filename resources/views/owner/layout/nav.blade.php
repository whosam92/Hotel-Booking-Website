<div class="navbar-bg"></div>
        <nav class="navbar navbar-expand-lg main-navbar">
            <form class="form-inline mr-auto">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fa fa-bars"></i></a></li>
                    <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
                </ul>
            </form>
            <ul class="navbar-nav navbar-right btn-block justify-content-end">
                <li class="nav-link">
                    <a href="" target="_blank" class="btn btn-danger">hello</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{-- <img alt="image" src="{{ asset('uploads/'.Auth::guard('owner')->user()->photo) }}" class="rounded-circle"> --}}
                        {{-- <div class="d-sm-none d-lg-inline-block">{{ Auth::guard('owner')->user()->name }}</div> --}}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('owner_profile') }}"><i class="fa fa-user"></i> Edit Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('owner_logout') }}"><i class="fa fa-sign-out"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>