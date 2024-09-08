<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {}
?>

<div>
    <img src="/images/tropical-drink.jpeg" alt="bebida tropical refrescante" />
    <div>
        <section class="bg-orange-700 container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h2 class="flex flex-col font-bold text-2xl gap-10">Sobre</h2>
            <p class="">Se você gosta de vinhos, espumantes, cervejas e muito mais, e quer ter acesso a uma grande variedade de rótulos, a ORANGE`DRINKS é o lugar perfeito para você! Com a nossa recém-lançado adega virtual, você pode explorar uma seleção cuidadosamente curada de todo o mundo, fazer pedidos com facilidade e receber suas garrafas favoritas diretamente na sua porta. Além disso, oferecemos recomendações personalizadas, informações detalhadas sobre cada produto. Experimente a ORANGE`DRINKS e descubra uma nova forma conveniente e prazerosa de apreciar bons produtos.</p>
        </section>
        <section class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h2 class="flex flex-col font-bold text-2xl gap-10">Sobre</h2>
            <div></div>
        </section>
        <section class="bg-orange-700 container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <form @submit.prevent="submitForm" method="POST" class="flex flex-col gap-4">

                @csrf
                <div class="flex flex-col">
                    <label for="name">Nome</label>
                    <input id="name" name="name" type="text" x-model="form.name" required>
                </div>
                <div class="flex flex-col">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="text" x-model="form.email" required>
                </div>
                <div class="flex flex-col">
                    <label for="motivo">Motivo</label>
                    <select id="motivo" name="motivo" x-model="form.motivo" required>
                        <option value="duvida">Dúvida</option>
                        <option value="elogio">Elogio</option>
                        <option value="reclamacao">Reclamação</option>
                        <option value="sugestao">Sugestão</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="mensagem">Mensagem</label>
                    <textarea id="mensagem" name="mensagem" x-model="form.mensagem" required></textarea>
                </div>
                <div class="w-full flex items-center justify-center">
                    <button type="submit" class="bg-black text-white px-10 py-4 rounded-md uppercase font-bold">Enviar</button>
                </div>
            </form>
        </section>
    </div>
</div>
