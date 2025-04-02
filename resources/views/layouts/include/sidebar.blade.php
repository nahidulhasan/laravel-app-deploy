<div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
    <div class="sidebar-brand d-none d-md-flex">
        <div class="avatar avatar-md" style="padding-right: 15px">
            <img class="avatar-img" src="{{asset('/assets/favicon/favicon.ico')}}" alt="user@email.com">
        </div>
        Learnathon
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item"><a class="nav-link" href="{{route('dashboard')}}">
                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
                </svg>
                Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('compliance-entry')}}">

                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                </svg>
                Compliance entry
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('periodic-tickets')}}">

                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-list-rich"></use>
                </svg>
                Periodic Tickets
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route('users')}}">

                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                </svg>
                User List
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route('user-role')}}">

                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                </svg>
                User Roles
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('ceo-cxo-report') }}">
                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-chart-pie"></use>
                </svg>
                Ceo Cxo Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('activity-logs')}}">

                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-av-timer"></use>
                </svg>
                Activity Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('index')}}">
                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-lock-unlocked"></use>
                </svg>
                Credential
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('notification-configs') }}">

                <svg class="nav-icon">
                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                </svg>
                Notification config
            </a>
        </li>

    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable" style="background: #67bbf7"></button>
</div>
<style>
    #sidebar {
        background: #67bbf7 !important;
        color: #FFF !important;
    }

    .nav-icon {
        color: #FFF !important;
    }

    #sidebar ul li a {
        color: #fff !important;
        text-decoration: none;
        padding: 15px 0 15px 10px;
        font-size: 16px;
        transition: all 0.3s ease;

    }

    ul li a.active {
        color: #fff !important;
        background: #4b92c5 !important;

    }
</style>