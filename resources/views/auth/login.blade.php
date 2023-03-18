<!DOCTYPE html>

<html class="loading" lang="en" data-textdirection="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Vdeliverz Adminpanel">
    <meta name="keywords" content="Developed using Laravel">
    <meta name="author" content="MINDNOTIX">
    <title>{{ config('app.name') }} login</title>
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/ico/fav.ico')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/ico/fav.ico')}}">
    {{-- <link rel="shortcut icon" type="image/x-icon" href="https://www.pixinvent.com/demo/frest-clean-bootstrap-admin-dashboard-template/app-assets/images/ico/favico.ico"> --}}
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.min.css') }}">

    <!-- END: Custom CSS-->



    <style>
    .small, small {
    font-size: 80%;
    color: red;
    }
    </style>

  </head>
  <!-- END: Head-->

  <!-- BEGIN: Body-->
  <body class="vertical-layout vertical-menu-modern semi-dark-layout 1-column blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" data-layout="semi-dark-layout">
    <!-- BEGIN: Content-->
    <div class="app-content content">
      <div class="content-overlay"></div>
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><!-- login page start -->
<section id="auth-login" class="row flexbox-container">
                <!-- left section-login -->
                <div class="col-md-6 col-12 px-0">
                    <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                        <div class="card-header pb-1">
                            <div class="card-title">
                                <h4 class="text-center mb-2">Welcome</h4>
                            </div>
                            <div class="text-center">
                                {{-- <img src="{{asset('app-assets/images/logo/login.png')}}" width="30%" alt="" srcset=""> --}}
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="divider">
                                    <div class="divider-text text-uppercase text-muted"><small style="color: Blue">login
                                           </small>
                                    </div>
                                </div>
                                <form class="login-form" method="POST" action="{{ route('login') }}" name="login_form" id="login_form">
                                @csrf
                                 <div id="flash-msg">
                                    @include('flash::message')
                                 </div>
                                    <div class="form-group mb-50">
                                        <label class="text-bold-600" for="exampleInputEmail1">Email</label>
                                        <input type="email"  autocomplete="new_password" value="{{old('email')}}"class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                            placeholder="Email address">
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        </div>
                                    <div class="form-group">
                                        <label class="text-bold-600"  for="exampleInputPassword1">Password</label>
                                        <input type="password" autocomplete="new_password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                            placeholder="Password">
                                            @error('password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                    </div>
                                    <div
                                        class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">

                                    </div>
                                    <button type="submit" class="btn btn-warning glow w-100 position-relative">LOGIN
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

</section>
<!-- login page ends -->

        </div>
      </div>
    </div>
    <!-- END: Content-->


  </body>
 </html>
