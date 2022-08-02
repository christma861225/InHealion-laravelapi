@component('mail::layout')
@slot('header')
@component('mail::header',  ['url' => config('app.url')])
@endcomponent
@endslot
# Passwor reset

Hello {{$user->profile->name}}!

You have requested to reset your password.


To reset and change your password, click the following [link]({{config('app.url').'reset-password\/'. base64_encode($resetPassword->hash)}}) and follow the instructions.
This link will be expired at {{$resetPassword->expired_at->format('Y-m-d')}}


If you did not make this request, just ignore this email.


Greetings<br>
{{ config('mail.from.name') }}

@slot('footer')
@component('mail::footer')
    <!-- footer here -->
@endcomponent
@endslot
@endcomponent
