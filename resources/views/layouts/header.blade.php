<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="{{ config('app.name') }} Adminpanel">
    <meta name="keywords" content="Developed using Laravel">
    <meta name="author" content="MINDNOTIX">
    <title>{{ config('app.name') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/fav.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/fav.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
        rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css"> --}}

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/chartist.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <!-- END: Vendor CSS-->
    @stack('css')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <!--Icons -->
    {{-- <link href='https://unpkg.com/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'> --}}

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.min.css') }}">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/chart-chartist.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/toastr.min.css') }}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
    <!-- END: Custom CSS-->

    <link rel="stylesheet" href="{{ asset('formvalidation/css/formValidation.min.css') }}">

    <style>
        .small,
        small {
            font-size: 80%;
            color: red;
        }

        ._jw-tpk-container {
            height: auto;
        }

    </style>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        // Pusher.logToConsole = true;

        var pusher = new Pusher('839c0784eaee275f407b', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            $.ajax({
                url: "{{ route('notifications') }}",
                type: 'get',
                data: {
                    "_token": '{{ csrf_token() }}',
                    id: data.post.id
                },
                success: function(response) {
                    if (response.status == true) {
                        // var url = "{{ asset('app-assets/notify/notify.wav') }}";
                        // const audio = new Audio(url);
                        // audio.play();
                        Push.create("New Order Arrived", {
                            body: 'Order referel - ' + response.prefix,
                            onClick: function() {
                                window.location.href = '/orders/';
                            }
                        });
                        $('#loadNotifications').html(response.notify_view);
                        $('#notifyCount').text(response.count);
                    }
                },
                error: function(response) {
                    toastr.error("Bad Network!", "Please Refresh page");
                }
            })
            // $('#notifyCount').text(2);
            // $('#dynamicNotify').append(`<a class="d-flex justify-content-between" href="javascript:void(0)"> <div class="media d-flex align-items-center"> <div class="media-left pr-0"> <div class="avatar mr-1 m-0"> <img src="../../../app-assets/images/portrait/small/avatar-s-11.jpg"alt="avatar"height="39"width="39"/> </div> </div> <div class="media-body"> <h6 class="media-heading"> <span class="text-bold-500">Congratulate Socrates Itumay</span>for work anniversaries </h6> <small class="notification-text">Mar 15 12:32pm</small> </div> </div></a>`);
        });
    </script>
</head>
<!-- END: Head-->
