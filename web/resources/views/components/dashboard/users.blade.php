<!-- Small Box components -->
<div class="col-lg-3 col-6">
  <!-- small box -->
  <div class="small-box bg-success">
    <div class="inner">
      <h3>@php
        echo \App\Models\User::count();
      @endphp</h3>
      <p>Users</p>
    </div>
    <div class="icon">
      <i class="ion ion-person"></i>
    </div>
    <a href="{{route('users.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
  </div>
</div>
<!-- ./col -->
