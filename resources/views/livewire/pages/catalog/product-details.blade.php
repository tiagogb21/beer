<?php

use App\Models\Product;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Computed, Url};

new
    #[Layout('layouts.app')]
    #[Title('Detalhes do Produto')]
    class extends Component {
        #[Url]
        public $slug = '';

        public ?Product $product = null;

        #[Computed]
        public function getProduct(): Product
        {
            if (!$this->product) {
                $this->product = Product::where('slug', $this->slug)->firstOrFail();
            }

            return $this->product;
        }
    }
?>

<div class="container flex-1 mx-auto px-4 py-8">
    @if($this->getProduct)
    <div class="flex flex-col lg:flex-row">
        <div class="lg:w-1/2">
            <img src="{{ $this->product->media[0]->original_url }}" alt="{{ $this->product->name }}" class="w-full h-auto rounded-lg shadow-lg">
        </div>
        <div class="flex flex-col items-center justify-center lg:w-1/2 lg:pl-8 mt-8 lg:mt-0">
            <h1 class="text-3xl font-bold mb-4">{{ $this->product->name }}</h1>
            <p class="text-lg text-gray-700 mb-4">{{ $this->product->description }}</p>
            <p class="text-xl font-semibold mb-4">Price: R${{ $this->product->price }}</p>
            <a href="{{ route('cart') }}" type="button" class="bg-black text-white rounded-md px-10 py-2 uppercase">comprar</a>
        </div>
    </div>
    @endif
</div>

