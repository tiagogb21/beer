<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Computed};
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    #[Computed]
    public function showCartItems()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {

            $cartItems = CartItem::where('cart_id', $cart->id)
                ->with(['product' => function ($query) {
                    $query->with('media');
                }])
                ->get();

            foreach ($cartItems as $item) {
                $item->product->image_url = $item->product->getImageUrlAttribute();
            }

            return $cartItems;
        }
    }

    public function addToCart($productId, $quantity = 1)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $product = Product::find($productId);
        if (!$product) {
            session()->flash('error', 'Produto não encontrado.');
            return;
        }

        if ($quantity > $product->quantity) {
            session()->flash('error', 'Quantidade excede o estoque disponível.');
            return;
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        $this->showCartItems();
    }

    public function incrementQuantity($itemId)
    {
        $cartItem = CartItem::with('product')->find($itemId);

        if ($cartItem && $cartItem->product->quantity > $cartItem->quantity) {
            $cartItem->increment('quantity');
            $cartItem->product->decrement('quantity');
            $this->showCartItems(); // Atualiza a lista de itens do carrinho
        } else {
            session()->flash('error', 'Quantidade máxima atingida ou produto fora de estoque.');
        }
    }

    public function decrementQuantity($itemId)
    {
        $cartItem = CartItem::with('product')->find($itemId);

        if ($cartItem && $cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
            $cartItem->product->increment('quantity');
            $this->showCartItems();
        }
    }

    public function removeFromCart($itemId)
    {
        $cartItem = CartItem::with('product')->find($itemId);

        if ($cartItem) {
            $cartItem->product->increment('quantity', $cartItem->quantity);
            $cartItem->delete();
            $this->showCartItems();
        }
    }
}
?>

<div class="">
    <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8 text-white flex flex-col gap-10">
        <h2 class="text-black font-bold text-2xl">Seu Carrinho</h2>
        <div class="flex flex-col lg:flex-row justify-between">
            <div class="flex flex-col">
                @forelse ($this->showCartItems() as $item)
                <div class="flex flex-col gap-6 border border-solid border-white rounded-lg p-4">
                    <div class="flex items-center justify-between gap-4 lg:gap-10">
                        <div class="flex items-center">
                            <img src="{{ $item->product->media[0]->original_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover mr-4">
                            <div>
                                <h3 class="text-lg font-bold">{{ $item->product->name }}</h3>
                                <p>R$ {{ number_format($item->product->price, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center border border-solid border-white rounded-2xl">
                            <!-- Botões de adicionar e remover -->
                            <button wire:click="decrementQuantity({{ $item->id }})" class="text-white p-2 rounded-full">-</button>
                            <span class="mx-2">{{ $item->quantity }}</span>
                            <button wire:click="incrementQuantity({{ $item->id }})" class="text-white p-2">+</button>
                        </div>
                        <button wire:click="removeFromCart({{ $item->id }})" class="bg-red-500 hover:bg-red-600 rounded-md text-white w-10 h-10"><i class="fa-solid fa-trash"></i></button>
                    </div>
                    <p class="text-center text-blue-900">Estoque disponível: {{ $item->product->quantity }}</p>
                </div>
                @empty
                <p>Seu carrinho está vazio.</p>
                @endforelse
            </div>
            <div class="h-36 flex flex-col justify-between border border-solid border-white w-96 p-4 rounded-lg">
                <h2 class="text-center text-2xl font-bold">Total</h2>
                <p class="flex justify-between"><span>Total</span> <span>R$ 0</span></p>
                <div class="flex items-center justify-center">
                    <a href="route('checkout')" class="px-10 py-2 rounded-md font-bold uppercase bg-black text-white">finalizar</a>
                </div>
            </div>
        </div>

        @if (session()->has('error'))
        <div class="bg-red-500 text-white p-4 mt-4">
            {{ session('error') }}
        </div>
        @endif
    </div>
</div>
