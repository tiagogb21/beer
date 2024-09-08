<?php

use Illuminate\Http\Request;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};

new
    #[Layout('layouts.app')]
    #[Title('Checkout')]
    class extends Component {
    public function checkout(Request $request)
    {
        $user = $request->user();
        $user->newSubscription('default', 'price_id')->create($request->paymentMethodId);

        return response()->json(['success' => true]);
    }
}; ?>

<form class="bg-white p-6 rounded shadow-md" id="payment-form">
    <h2 class="text-xl mb-4">Formulário de Pagamento</h2>

    <!-- Nome no cartão -->
    <div class="mb-4">
        <label for="card-name" class="block text-sm font-medium text-gray-700">Nome no cartão</label>
        <input type="text" id="card-name" name="cardName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
    </div>

    <!-- Endereço de cobrança -->
    <div class="mb-4">
        <label for="billing-address" class="block text-sm font-medium text-gray-700">Endereço de cobrança</label>
        <input type="text" id="billing-address" name="billingAddress" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
    </div>

    <!-- Stripe Element -->
    <div id="card-element" class="border border-gray-300 rounded-md p-2 mb-4">
        <!-- Stripe Element will be inserted here. -->
    </div>

    <!-- Button -->
    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md shadow-sm hover:bg-blue-600">Pagar</button>

    <!-- Error Message -->
    <div id="card-errors" role="alert" class="text-red-500 mt-2"></div>
</form>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        const cardErrors = document.getElementById('card-errors');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const cardName = document.getElementById('card-name').value;
            const billingAddress = document.getElementById('billing-address').value;

            const {paymentMethod, error} = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: cardName,
                    address: {
                        line1: billingAddress
                    }
                }
            });

            if (error) {
                cardErrors.textContent = error.message;
            } else {
                // Send paymentMethod.id and additional data to your server
                fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        paymentMethodId: paymentMethod.id,
                        cardName: cardName,
                        billingAddress: billingAddress
                    })
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        alert('Pagamento realizado com sucesso!');
                    } else {
                        cardErrors.textContent = 'Ocorreu um erro ao processar o pagamento.';
                    }
                });
            }
        });
    });
</script>
