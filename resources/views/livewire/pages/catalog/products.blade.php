<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Livewire\WithPagination;

new
    #[Layout('layouts.app')]
    #[Title('Products')]
    class extends Component {
        use WithPagination;

        public $selectedCategory = null;

        public function mount()
        {
            $this->selectedCategory = request()->query('category');
        }

        #[Computed]
        public function showProducts()
        {
            $query = Product::query();

            if ($this->selectedCategory) {
                $query->whereHas('categories', function ($query) {
                    $query->where('category_id', $this->selectedCategory);
                });
            }

            $products = $query->paginate(20);

            foreach ($products as $product) {
                $product->image_url = $product->getFirstMediaUrl('images');
            }

            return $products;
        }

        #[Computed]
        public function showCategories()
        {
            return Category::all(); // Utiliza all() para carregar todas as categorias
        }

        public function selectCategory($categoryId)
        {
            $this->selectedCategory = $categoryId;
            $this->showProducts();
            $this->resetPage(); // Reseta a páginação ao mudar a categoria
        }

        public function addToCart($productId)
        {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $product = Product::find($productId);

            // Verificar se o carrinho do usuário já existe
            $cart = Cart::where('user_id', Auth::id())->first();

            if ($product['quantity'] <= 0) {
                session()->flash('error', 'Produto fora de estoque.');
                return;
            }

            if (!$cart) {
                // Criar um novo carrinho se não existir
                $cart = Cart::create(['user_id' => Auth::id()]);
            }

            // Verificar se o item já está no carrinho
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product['id'])
                ->first();

            if ($cartItem) {
                // Atualizar a quantidade se o item já estiver no carrinho
                $cartItem->increment('quantity');
            } else {
                // Adicionar um novo item ao carrinho
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product['id'],
                    'quantity' => 1,
                ]);
            }

            $product->decrement('quantity');
        }
    };
?>

<div class="min-h-screen">
    <div class="bg-zinc-950">
        <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8 text-white flex justify-between">
            @foreach ($this->showCategories as $category)
            <a href="#" wire:click.prevent="selectCategory({{ $category->id }})" class="">{{ $category->name }}</a>
            @endforeach
        </div>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
        @if(count($this->showProducts) === 0)
            <p class="w-screen">Ainda não temos produtos nessa categoria.</p>
        @endif
        @foreach ($this->showProducts as $product)
        <div wire:key="{{ $product->id }}" class="border border-solid border-black p-4 items-center justify-between flex flex-col gap-4">
            <a href="{{ route('product.show', ['slug' => $product->slug]) }}" class="flex flex-col items-center">
                <img src="{{ $product->media[0]->original_url }}" class="w-32 h-32 object-cover mt-2">
                <h3 class="font-bold text-lg">{{ $product->name }}</h3>
                <p class="text-white font-bold text-2xl">R$ {{ $product->price }}</p>
            </a>
            <button wire:click="addToCart({{ $product->id }})" class="bg-white px-4 font-bold">Adicionar ao carrinho</button>
        </div>
        @endforeach
    </div>

    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-4 mt-4">
            {{ session('error') }}
        </div>
    @endif
</div>
