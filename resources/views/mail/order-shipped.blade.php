<x-mail::message>
Olá, {{ $user->name }}

Estamos preparando o produto {{ $product }}

Total: {{ $orderPrice }}

<x-mail::button :url="''" color="success">
Ver Pedido
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
