<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class Payment extends Component
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

    public function render()
    {
        return view('livewire.payment');
    }

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

            session()->flash('success', 'Nós adicionamos ' . $this->amount . "$ para sua conta!");
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
}
