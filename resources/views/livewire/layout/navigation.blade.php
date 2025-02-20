<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component
{
    public string $search = '';
    public array $products = [];

    public function findProducts()
    {
        return Product::where('name', 'ilike', "%{$this->search}%")->get()->toArray();
    }

    public function updatedSearch()
    {
        $this->products = strlen($this->search) > 0 ? $this->findProducts() : [];
    }

    public function clear()
    {
        $this->search = '';
        $this->products = [];
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}
?>

<nav x-data="{ open: false }" class="bg-zinc-950 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex p-6">
            <div class="flex flex-1 items-center">
                <a class="flex-1 font-bold uppercase" href="{{ route('home') }}" wire:navigate>
                    Orange Drinks
                </a>

                <div id="search-bar" class="hidden lg:flex relative">
                    <form wire:model.live="search" class="flex items-center bg-white rounded-md" role="search">
                        <input
                            wire:model.live="search"
                            type="text"
                            name="headerSearch"
                            id="header-search"
                            class="pl-4 text-black border-none rounded-md focus:border-none focus:ring-0" aria-label="Header Search"
                            placeholder="Search..."
                            autocomplete="off">
                    </form>
                    @if(count($products) > 0)
                    <div class="absolute flex flex-col w-full p-4 text-black bg-white shadow-lg">
                        @foreach($products as $product)
                        <a href="{{ route('product.show', ['slug' => $product['slug']]) }}" wire:key="{{ $product['id'] }}">
                            {{ $product['name'] }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('cart')" :active="request()->routeIs('cart')" wire:navigate>
                        <i class="fa-solid fa-cart-shopping"></i>
                    </x-nav-link>
                    <a href="https://api.whatsapp.com/send?phone=5555999999999&text=Ol%C3%A1,posso%20te%20ajudar?">
                        <i class="fa-solid fa-headphones"></i>
                    </a>
                </div>
            </div>

            @if (Auth::check())
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <i class="fa-solid fa-user"></i>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class='text-gray-700 px-4 py-2 border-b border-solid border-gray-400' x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
            <div class="flex items-center ml-10">
                <a href="{{ route('login') }}">
                    <i class="fa-solid fa-user"></i>
                </a>
            </div>
            @endif

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        @if (Auth::check())
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{ name: '{{ auth()->user()->name }}' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-start text-white">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
        @endif
    </div>
</nav>
