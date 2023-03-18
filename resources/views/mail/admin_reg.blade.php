@component('mail::message')
    # Dear Admin

    {{ $data['admin_content'] }}

    # Vendor Details

    @foreach ($data['table'] as $k => $val)
        {{ $k }} : {{ $val }}
    @endforeach

    Thanks,
    {{ config('app.name') }}
@endcomponent
