<!-- jQuery -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Sparkline -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/jqvmap/jquery.vmap.min.js"></script>
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/moment/moment.min.js"></script>
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script referrerpolicy="same-origin" src="{{ asset('') }}plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script referrerpolicy="same-origin" src="{{ asset('') }}js/adminlte.min.js"></script>

@stack('js')