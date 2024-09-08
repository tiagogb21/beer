<?php

use App\Models\Category;
use Livewire\Attributes\{Layout, Title, Computed};
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
    #[Layout('layouts.app')]
    #[Title('Login')]
    class extends Component {
        use WithPagination;

        public string $name = '';
        public string $email = '';
        public string $reason = '';
        public string $message = '';

        #[Computed]
        public function showCategories()
        {
            return Category::paginate(10);
        }

        public function submitForm()
        {
            $this->reason = !empty($this->reason) ? $this->reason : 'Duvida';

            $validated = $this->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'reason' => 'required|string',
                'message' => 'required|string',
            ]);

            $data = "Nome: {$validated['name']}\nEmail: {$validated['email']}\nMotivo: {$validated['reason']}\nMensagem: {$validated['message']}\n\n";

            $filePath = storage_path('app/formulario.txt');

            file_put_contents($filePath, $data, FILE_APPEND);

            session()->flash('message', 'Formulário enviado com sucesso!');
        }
    }
?>

<div>
    <img src="/images/tropical-drink.jpeg" alt="bebida tropical refrescante" />
    <div>
        <section class="bg-orange-700 container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h2 class="flex flex-col font-bold text-2xl gap-10">Sobre</h2>
            <p class="">Se você gosta de vinhos, espumantes, cervejas e muito mais, e quer ter acesso a uma grande variedade de rótulos, a ORANGE`DRINKS é o lugar perfeito para você! Com a nossa recém-lançado adega virtual, você pode explorar uma seleção cuidadosamente curada de todo o mundo, fazer pedidos com facilidade e receber suas garrafas favoritas diretamente na sua porta. Além disso, oferecemos recomendações personalizadas, informações detalhadas sobre cada produto. Experimente a ORANGE`DRINKS e descubra uma nova forma conveniente e prazerosa de apreciar bons produtos.</p>
        </section>
        <section class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-4">
            <h2 class="flex flex-col font-bold text-2xl gap-10">Categorias</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 justify-items-start">
                @foreach ($this->showCategories as $category)
                <div wire:key="{{ $category->id }}">
                    <a href="{{ route('products', ['category' => $category->id]) }}">
                        <img src="{{ $category->image_url }}" class="w-32 h-32 object-cover mt-2" />
                    </a>
                </div>
                @endforeach
            </div>
        </section>
        <section class="bg-orange-700 container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <form wire:submit.prevent="submitForm" class="flex flex-col gap-4">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="flex flex-col">
                    <x-input-label for="reason" :value="__('Menssagem')" />
                    <select id="reason" name="reason" wire:model.defer="reason" required>
                        <option value="duvida">Dúvida</option>
                        <option value="elogio">Elogio</option>
                        <option value="reclamacao">Reclamação</option>
                        <option value="sugestao">Sugestão</option>
                    </select>
                </div>
                <div class="mt-4">
                    <x-input-label for="message" :value="__('Menssagem')" />
                    <textarea wire:model="message" id="message" class="block mt-1 w-full rounded-md" type="message" name="message" required autocomplete="message"></textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>
                <div class="flex items-center justify-center">
                    <x-primary-button class="ms-4">
                        {{ __('Enviar') }}
                    </x-primary-button>
                </div>
            </form>
        </section>
    </div>
</div>
