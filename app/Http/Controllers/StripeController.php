<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class StripeController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            Charge::create([
                'amount' => 1200,
                'currency' => 'usd',
                'description' => 'One-time membership fee',
                'source' => $request->stripeToken,
            ]);

            // Logic to register the user goes here

            return redirect()->route('dashboard')->with('success', 'Payment successful! Welcome to the platform.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
