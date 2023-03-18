@extends('layouts.main')

@section('content')

<section class="input-validation">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h2 class="">Edit @role('admin') User @else Profile @endrole</h2>
          </div>
          <div class="card-content">
            <div class="card-body">
            <form class="form-horizontal" action="{{ route('users.update', $user->id) }}" id="users_form" method="POST" enctype="multipart/form-data" novalidate autocomplete="off">
                 {{ method_field('PUT') }}
                    @include('user._userForm')
                    @include('shared._submit', [
                      'entity' => 'users',
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
