<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ route('homepage') }}" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- User Account Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-user"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">Account</span>
        <div class="dropdown-divider"></div>
        <a href="{{ route('profile.show') }}" class="dropdown-item">
          <i class="fas fa-user-cog mr-2"></i> Profile
        </a>
        <div class="dropdown-divider"></div>
        <form action="{{ route('logout') }}" method="POST" class="dropdown-item">
          @csrf
          <button type="submit" style="background: none; border: none; padding: 0; color: #212529; cursor: pointer;">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        </form>
      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
