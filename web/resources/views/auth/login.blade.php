@extends('layouts.auth')

@section('title', 'Login')

@section('headline', 'Welcome back')
@section('highlight', 'to WEC API')
@section('subtext', 'Enter your email and password to access admin panel. Manage your data and settings with ease using our dashboard interface.')

@section('content')
<div class="form-container">
  @include('components.auth-alert')
  <form action="{{ route('login') }}" method="POST">
    @csrf
    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email address" value="{{ old('email') }}" required>
    <x-validation-error name="email" />
    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
    <x-validation-error name="password" />
    
    <div class="form-checkbox">
      <input type="checkbox" id="remember" name="remember">
      <label for="remember">Remember me</label>
    </div>
    
    <button type="submit" class="btn-sign-up">LOGIN</button>
  </form>
  
  <div class="social-divider">or login with:</div>
  
  <div class="social-icons">
    <a href="#" class="social-icon">
      <i class="fab fa-facebook-f"></i>
    </a>
    <a href="#" class="social-icon">
      <i class="fab fa-google"></i>
    </a>
    <a href="#" class="social-icon">
      <i class="fab fa-twitter"></i>
    </a>
    <a href="#" class="social-icon">
      <i class="fab fa-github"></i>
    </a>
  </div>
</div>
@endsection
