@extends('layouts.main')

@section('content')

<section class="input-validation">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h2 class="">Edit Product</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
            <form class="form-horizontal" action="{{ route('coupons.update', $coupon->id) }}" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                 {{ method_field('PUT') }}
                    @include('coupon._couponForm')
                    @include('shared._submit', [
                      'entity' => 'coupons',
                      'button' => 'Update'
                    ])
                </form>
            </div>
         </div>
        </div>
    </div>
</div>
</section>

@endsection
