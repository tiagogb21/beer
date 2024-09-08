<?php

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $street = '';
    public string $number = '';
    public string $complement = '';
    public string $district = '';
    public string $city = '';
    public string $state = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $address = Auth::user()->addresses()->first();

        if (!empty($address)) {
            $this->street = $address->street;
            $this->number = $address->number;
            $this->complement = $address->complement;
            $this->district = $address->district;
            $this->city = $address->city;
            $this->state = $address->state;
        }
    }

    /**
     * Update the address information for the currently authenticated user.
     */
    public function updateAddressInformation(): void
    {
        $validated = $this->validate([
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'integer'],
            'complement' => ['nullable', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        $address = $user->addresses()->first();

        if (!$address) {
            $address = Address::create($validated);
            $user->addresses()->attach($address->id);
        } else {
            $address->update($validated);
        }

        session()->flash('message', 'Endereço atualizado com sucesso.');

        $this->dispatch('address-information', ['name' => $address->street]);
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Address Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-950">
            {{ __("Update your address's information.") }}
        </p>
    </header>

    <form wire:submit.prevent="updateAddressInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="street" :value="__('Street')" />
            <x-text-input wire:model="street" id="street" street="street" type="text" class="mt-1 block w-full" required autofocus autocomplete="street" />
            <x-input-error class="mt-2" :messages="$errors->get('street')" />
        </div>

        <div>
            <x-input-label for="number" :value="__('Number')" />
            <x-text-input wire:model="number" id="number" name="number" type="number" class="mt-1 block w-full" required autocomplete="number" />
            <x-input-error class="mt-2" :messages="$errors->get('number')" />
        </div>

        <div>
            <x-input-label for="complement" :value="__('Complement')" />
            <x-text-input wire:model="complement" id="complement" name="complement" type="text" class="mt-1 block w-full" autocomplete="complement" />
            <x-input-error class="mt-2" :messages="$errors->get('complement')" />
        </div>

        <div>
            <x-input-label for="district" :value="__('District')" />
            <x-text-input wire:model="district" id="district" name="district" type="text" class="mt-1 block w-full" required autocomplete="district" />
            <x-input-error class="mt-2" :messages="$errors->get('district')" />
        </div>

        <div>
            <x-input-label for="city" :value="__('City')" />
            <x-text-input wire:model="city" id="city" name="city" type="text" class="mt-1 block w-full" required autocomplete="city" />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div>
            <x-input-label for="state" :value="__('State')" />
            <x-text-input wire:model="state" id="state" name="state" type="text" class="mt-1 block w-full" required autocomplete="state" />
            <x-input-error class="mt-2" :messages="$errors->get('state')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>

    @if (session()->has('message'))
    <div class="text-red-500 mt-4">
        {{ session('message') }}
    </div>
    @endif
</section>
