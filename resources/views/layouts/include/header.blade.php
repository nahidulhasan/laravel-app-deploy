<header class="header header-sticky ">
    <div class="container-fluid">
        <button class="header-toggler px-md-0 me-md-3" type="button"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button>
        <a class="header-brand d-md-none" href="#">
            <svg width="118" height="46">
                <use xlink:href="assets/brand/coreui.svg#full"></use>
            </svg>
        </a>
{{--        <ul class="header-nav d-none d-md-flex">--}}
{{--            <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>--}}
{{--        </ul>--}}
        <ul class="header-nav ms-3">
            <li class="nav-item dropdown">
                <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    {{ Auth::user()->name ?? '' }}&nbsp;&nbsp;
                    <div class="avatar avatar-md">
                        <img class="avatar-img" src="{{asset('/assets/img/avatars/profile.jpg')}}" alt="RCMS"></div>
                </a>
                <div class="dropdown-menu dropdown-menu-end mt-2"
                     style=" min-width: 200px; text-align: center; padding-top: 10px;">
                    <a class="dropdown-item mt-1" href="{{route('dashboard')}}"
                       style="border-bottom: 1px #EEE solid; padding: 15px;">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                    <a class="dropdown-item mt-1" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       style=" padding: 15px;">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                        </svg> {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf

                    </form>
                </div>
            </li>
        </ul>
    </div>

</header>


{{--<div class="header-divider"></div>--}}