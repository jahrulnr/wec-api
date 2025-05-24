<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('homepage') }}" class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
        @if(auth()->user()->hasAnyRole(['Administrator']) || auth()->user()->hasPermission('users-view') || auth()->user()->hasPermission('roles-view') || auth()->user()->hasPermission('permissions-view'))
        <li class="nav-item has-treeview {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>
                    User Management
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @if(auth()->user()->hasPermission('users-view'))
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="far fa-user nav-icon"></i>
                        <p>Users</p>
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasPermission('roles-view'))
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tag nav-icon"></i>
                        <p>Roles</p>
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasPermission('permissions-view'))
                <li class="nav-item">
                    <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <i class="fas fa-key nav-icon"></i>
                        <p>Permissions</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif
        <li class="nav-item">
            <a href="{{ route('api-switcher.dashboard') }}" class="nav-link {{ request()->routeIs('api-switcher.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-random"></i>
                <p>API Switcher</p>
            </a>
        </li>
        @if(auth()->user()->hasPermission('postman'))
        <li class="nav-item">
            <a href="{{ route('postman') }}" class="nav-link {{ request()->routeIs('postman*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-globe"></i>
                <p>Postman</p>
            </a>
        </li>
        @endif
    </ul>
</nav>
<!-- /.sidebar-menu -->