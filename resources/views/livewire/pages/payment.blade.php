<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Stripe\Stripe;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentMethod;

new #[Layout('layouts.guest')] class extends Component
{
    public $amount;
    public $cardNumber;
    public $cardExpiryMonth;
    public $cardExpiryYear;
    public $cardCVC;

    protected $rules = [
        'amount' => 'required|numeric|between:5,500',
        'cardNumber' => 'required|regex:/^[45]\d{15}$/',
        'cardExpiryMonth' => 'required|numeric|between:1,12',
        'cardExpiryYear' => 'required|numeric|digits:4',
        'cardCVC' => 'required|numeric|digits:3',
    ];

    private function MakeStripePayment()
    {
        Stripe::setApiKey(env('stripe_secret'));

        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number' => $this->cardNumber,
                'exp_month' => $this->cardExpiryMonth,
                'exp_year' => $this->cardExpiryYear,
                'cvc' => $this->cardCVC,
            ],
        ]);

        return $paymentMethod->id;
    }

    public function makePayment()
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            Auth::user()->charge($this->amount * 100, $this->MakeStripePayment(), [
                'currency' => 'BRL',
                'description' => "Depósito para Orange Drinks por " . Auth::user()->name,
                'receipt_email' => Auth::user()->email,
            ]);

            Auth::user()->increment('balance', $this->amount);

            session()->flash('success', 'Nós adicionamos R$' . $this->amount . " a sua conta!");
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e);
        }
    }

    private function resetForm()
    {
        $this->amount = null;
        $this->cardNumber = null;
        $this->cardExpiryMonth = null;
        $this->cardExpiryYear = null;
        $this->cardCVC = null;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
};
?>

<div class="container mt-3 mb-3">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <h2 class="text-center mb-4">Add Funds</h2>

                <form wire:submit.prevent="makePayment">
                    <div class="form-group">
                        <label for="amount">Amount:</label>
                        <input type="text" wire:model="amount" class="form-control" id="amount" placeholder="Enter Amount">
                        <input type="text" wire:model="amount" class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" id="amount" placeholder="Enter Amount">
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="text" wire:model="amount" class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" id="amount" placeholder="Enter Amount">
                            {!!$errors->has('amount') ? "<span class='text-danger'>{$errors->first('amount')}</span>" : '' !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cardNumber">Card Number:</label>
                        <input type="text" class="form-control" id="cardNumber" placeholder="Enter Card Number">
                    </div>


                    <div class="form-group">
                        <label for="cardExpiry">Expiration Date:</label>
                        <div class="input-group">
                            <div class="col-md-6 mb-2">
                                <select class="form-control" id="cardExpiryMonth">
                                    <option value="" selected disabled>Month</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="cardExpiryYear">
                                    <option value="" selected disabled>Year</option>
                                    @php
                                    $currentYear = date('Y');
                                    $futureYears = 10;
                                    @endphp
                                    @for ($i = 0; $i <= $futureYears; $i++)
                                        <option value="{{ $currentYear + $i }}">{{ $currentYear + $i }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cardCVC">CVC:</label>
                        <input type="text" class="form-control" id="cardCVC" placeholder="CVC">
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg px-5">Pay Now</button>
                    </div>
                </form>

                @if (session()->has('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                    <a href="../"><button class="btn-sm btn-outline-success mt-2">Back Home</button></a>
                </div>
                @elseif(session()->has('error'))
                <div class="alert alert-danger text-center">
                    {{ session('error') }}
                    <a href="../"><button class="btn-sm btn-danger mt-2">Cancel Payment</button></a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
