@component('mail::message')
# Hola {{ $user->name }}

Gracias por crear una cuenta por favor verfica tu correo utilizando el siguiente boton:
@component('mail::button', ['url' => env('LINK_VERIFICATION_CLIENT', 'http://localhost:8000/verify/'). $user->verification_token])
Confirmar mi cuenta
@endcomponent


Gracias, <br>
{{ config('app.name')}}
@endcomponent