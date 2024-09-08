<?php

use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

new
    #[Layout('layouts.app')]
    #[Title('Checkout')]
    class extends Component
{
    #[Computed]
    public function cartTotal()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart) return 0;

        return CartItem::where('cart_id', $cart->id)
            ->with('product')
            ->get()
            ->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $paymentMethodId = $request->input('paymentMethodId');

        // Cria o pedido com status pendente
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Carrinho vazio.']);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $this->cartTotal(),
            'status' => OrderStatusEnum::PENDING, // Status inicial
        ]);

        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        return response()->json(['success' => true, 'orderId' => $order->id]);
    }

    public function confirmPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        $order->status = OrderStatusEnum::PAID;

        $order->save();

        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->delete();
        }

        return redirect()->route('order.confirmation', ['order' => $order->id]);
    }
}
?>

<form class="my-10 lg:w-96 mx-auto bg-white p-6 rounded shadow-md" id="payment-form">
    <h1 class="text-xl mb-4">Formulário de Pagamento</h1>

    <div class="flex justify-between items-center mb-4">
        <p>Total a pagar</p> <p id="total-amount">R$ {{ $this->cartTotal() }}</p>
    </div>

    <!-- Nome no cartão -->
    <div class="mb-4">
        <label for="card-name" class="block text-sm font-medium text-gray-700">Nome do titular</label>
        <input type="text" id="card-name" name="cardName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="{{ Auth::user()->name }}" required>
    </div>

    <!-- Endereço de Entrega -->
    <div class="mb-4">
        <div class="flex justify-between">
            <label for="shipping-address" class="block text-sm font-medium text-gray-700">Endereço de entrega</label>
            <a href="{{ route('profile') }}">Alterar</a>
        </div>
        <input type="text" id="shipping-address" name="shippingAddress" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
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
            const billingAddress = document.getElementById('billing-address') ? document.getElementById('billing-address').value : '';

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
                // Enviar paymentMethod.id e dados adicionais para o servidor
                fetch('{{ route('checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        paymentMethodId: paymentMethod.id
                    })
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        // Confirmar pagamento
                        fetch(`/confirm-payment/${data.orderId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                        }).then(response => response.json()).then(data => {
                            if (data.success) {
                                alert('Pagamento realizado com sucesso!');
                                window.location.href = "{{ route('order.confirmation', ['order' => 'orderId']) }}".replace('orderId', data.orderId);
                            } else {
                                cardErrors.textContent = 'Ocorreu um erro ao processar o pagamento.';
                            }
                        });
                    } else {
                        cardErrors.textContent = 'Ocorreu um erro ao processar o pagamento.';
                    }
                });
            }
        });
    });
</script>
