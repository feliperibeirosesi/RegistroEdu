<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<title>{{ $title ?? 'RegistroEdu' }}</title>

@if(isset($css))
    <link rel="stylesheet" href="{{ asset("css/page/{$page}/{$css}.css") }}">
@endif

@if(isset($additionalCss))
    @foreach($additionalCss as $cssFile)
        <link rel="stylesheet" href="{{ asset("css/{$cssFile}.css") }}">
    @endforeach
@endif
