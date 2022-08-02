@component('mail::layout')
@slot('header')
@component('mail::header',  ['url' => config('app.url')])
@endcomponent
@endslot
# Account created

Hello {{$user->profile->name}}!


You can go to [here]({{config('app.url').'reset-password\/'. base64_encode($resetPassword->hash)}}) to login into your account.
This link will be expired at {{$resetPassword->expired_at->format('Y-m-d')}}


Greetings<br>
{{ config('mail.from.name') }}

@slot('footer')
@component('mail::footer')
    <!-- footer here -->
@endcomponent
@endslot
@endcomponent
