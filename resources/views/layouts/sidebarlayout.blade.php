<!-- resources/views/layouts/sidebarlayout.blade.php -->

<nav id="sidebarMenu" class="bg-dark text-white vh-100 p-3 position-fixed">
    <div class="text-center mb-4">
        <h4 class="fw-bold">{{ config('app.name') }}</h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}" 
               href="{{ route('manager.dashboard') }}">
                <i class="bi bi-house-fill"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('manager.reports') ? 'active' : '' }}" 
               href="{{ route('manager.reports') }}">
                <i class="bi bi-bar-chart-fill"></i> <span>Reports</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('manager.tenants') ? 'active' : '' }}" 
               href="{{ route('manager.tenants') }}">
                <i class="bi bi-people-fill"></i> <span>Tenants</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('manager.units.*') ? 'active' : '' }}" 
               href="{{ route('manager.units.index') }}">
                <i class="bi bi-building-fill-add"></i> <span>Units</span>
            </a>
        </li>
        <li class="nav-item mt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                </button>
            </form>
        </li>
    </ul>
</nav>
