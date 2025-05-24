<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="WEC API Dashboard">
  <title>WEC API | @yield('title', 'Dashboard')</title>

  @include('partials.assets.css')
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
@include('partials.layout.loading')
<div class="wrapper">
  @include('partials.layout.header')

  @include('partials.layout.sidebar-container')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{ isset($menu_name) ? $menu_name : "Dashboard" }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">{{ isset($menu) ? $menu : "Home" }}</a></li>
              <li class="breadcrumb-item active">{{ isset($menu_name) ? $menu_name : "Dashboard" }}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        @yield('menu_card')
        
        <!-- Main content area -->
        @yield('content')
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  @include('partials.layout.footer')
  
</div>
<!-- ./wrapper -->
@include('partials.assets.js')
<script src="{{ asset('') }}js/dashboard.js"></script>
@stack('scripts')
</body>
</html>
