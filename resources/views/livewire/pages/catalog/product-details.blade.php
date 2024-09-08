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

                $this->product->image_url = $this->product->getFirstMediaUrl('images');
            }

            return $this->product;
        }
    }
?>

<div class="container flex-1 mx-auto px-4 py-8">
    @if($this->getProduct)
    <div class="flex flex-col lg:flex-row">
        <div class="lg:w-1/2">
            <img src="{{ $this->product->image_url }}" alt="{{ $this->product->name }}" class="w-full h-auto rounded-lg shadow-lg">
        </div>
        <div class="lg:w-1/2 lg:pl-8 mt-8 lg:mt-0">
            <h1 class="text-3xl font-bold mb-4">{{ $this->product->name }}</h1>
            <p class="text-lg text-gray-700 mb-4">{{ $this->product->description }}</p>
            <p class="text-xl font-semibold mb-4">Price: ${{ $this->product->price }}</p>
        </div>
    </div>
    @endif
</div>

