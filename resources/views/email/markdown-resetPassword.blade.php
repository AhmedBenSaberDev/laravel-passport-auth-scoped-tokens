@component('mail::message')
Hello {{ $userName }},

A request has been received to change the password 

@component('mail::button', ['url' => "http://localhost:8081/reset/" . $token])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
