@component('mail::message')
    # Hi {{ $name }}

    {{ $content }}

    Thanks,
    {{ config('app.name') }}
@endcomponent
