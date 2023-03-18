@component('mail::message')
    # Dear {{ $data['vendor_name'] }}

    {{ $data['vendor_content'] }}

    # Vendor Details

    @foreach ($data['table'] as $k => $val)
        {{ $k }} : {{ $val }}
    @endforeach

    Thanks,
    {{ config('app.name') }}
@endcomponent
