@component('mail::message')
    # Hi {{ $name }}

    Your VDeliverz verification code is

    # {{ $otp }}

    Thanks,
    {{ config('app.name') }}
@endcomponent
