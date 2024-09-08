<?php

use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};

new
    #[Layout('layouts.app')]
    #[Title('Checkout')]
    class extends Component
{
}
?>

<div>
    <h1>Seu pedido foi confirmado com sucesso</h1>
</div>
