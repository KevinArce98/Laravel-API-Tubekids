@component('mail::message')
# Hola {{ $user->name }}

Has cambiado tu correo electronico. Por favor verfica tu nuevo correo utilizando el siguiente boton:
@component('mail::button', ['url' =>  env('LINK_VERIFICATION_CLIENT', 'http://localhost:8000/verify/'). $user->verification_token ])
Confirmar mi cuenta
@endcomponent


Gracias, <br>
{{ config('app.name')}}
@endcomponent