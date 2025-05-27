<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="WEC API Authentication">
  <meta name="theme-color" content="#343a40">
  <title>WEC API | @yield('title', 'Authentication')</title>

  @include('partials.assets.css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('') }}cms-api/css/auth.css">
  <link rel="stylesheet" href="{{ asset('') }}cms-api/css/auth-animation.css">
  <link rel="stylesheet" href="{{ asset('') }}cms-api/css/auth-alerts.css">
  <link rel="stylesheet" href="{{ asset('') }}cms-api/css/auth-ui-enhancements.css">
  @stack('styles')
</head>
<body class="auth-page">
  @include('partials.layout.loading')
  <div class="auth-container">
    <div class="left-section">
      <div class="fluid-bg"></div>
      <h1 class="headline">@yield('headline', 'The best offer')<br><span class="highlight">@yield('highlight', 'for your business')</span></h1>
      <p class="subtext">@yield('subtext', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Eveniet, itaque accusantium odio, soluta, corrupti aliquam quibusdam tempora at cupiditate quis eum maiores libero veritatis? Dicta facilis sint aliquid ipsum atque?')</p>
    </div>
    <div class="right-section">
      <div class="auth-animation">
        @yield('content')
      </div>
    </div>
  </div>

  @include('partials.assets.js')
  <script src="{{ asset('') }}cms-api/js/auth.js"></script>
  @stack('scripts')
</body>
</html>
