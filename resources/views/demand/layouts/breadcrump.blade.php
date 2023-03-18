
<?php

$route_name = null;
$topic = null;
$home = null;

switch ($route[1]) {
  case 'categories':
    $topic = 'Services';
    $home = 'demand.'.$route[1].'.index';
    break;

  default:
    $topic = $route[1];
    $home = 'demand.'.$route[1].'.index';
    break;
}
?>


<div class="row breadcrumbs-top">
<div class="col-12">
  <h5 class="content-header-title float-left pr-1 mb-0">{{ucfirst(str_replace('-', ' ', $topic))}}</h5>
  <div class="breadcrumb-wrapper col-12">
    <ol class="breadcrumb p-0 mb-0">
      <li class="breadcrumb-item"><a href="{{ route('demand.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
      </li>
    <li class="breadcrumb-item"><a href="{{ route($home) }}">{{ucfirst($topic)}}</a>
      </li>
      <li class="breadcrumb-item active">{{$route_name}}
      </li>
    </ol>
  </div>
</div>
</div>
