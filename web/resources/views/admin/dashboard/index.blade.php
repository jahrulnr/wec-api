@extends('layouts.app')

@section('menu_card')
<div class="row">
@include('components.dashboard.system-information')
@include('components.dashboard.users')
</div>
@endsection

@section('content')
<div class="row">
</div>
@endsection