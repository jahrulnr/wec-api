<!-- Small Box components -->
<div class="col-lg-3 col-6">
  <!-- small box -->
  <div class="small-box bg-info">
    <div class="inner">
      <h3>@php
        echo \App\Models\ApiCriteria::count();
      @endphp</h3>
      <p>Api List</p>
    </div>
    <div class="icon">
      <i class="ion ion-bag"></i>
    </div>
    <a href="{{route('api-switcher.dashboard')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
  </div>
</div>
<!-- ./col -->
