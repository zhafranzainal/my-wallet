<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function topup(StoreTransactionRequest $request)
    {

        // Get Validate Request
        $validated = $request->validated();

        // Run Bill
        $billId = $this->generateBill($validated);

        $user = Auth::user();
        $user->transactions()->create(['id'=>$billId, 'amount'=>$validated['amount']]);

        return $this->return_api(true, Response::HTTP_CREATED, null, $billId, null);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CheckoutTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(CheckoutTransactionRequest $request)
    {
        // Get validate request
        $validated = $request->validated();

        // Get product model
        $product = Product::findOrFail($validated['product_id']);

        // Calculate total price
        $totalPrice = $product->price * $validated['quantity'];

        // Check whether user wallet balance is enough
        if (Auth::user()->wallet->balance < $totalPrice) {
            return $this->return_api(false, Response::HTTP_UNPROCESSABLE_ENTITY, "Wallet balance is not enough", null, null);
        }

        return DB::transaction(function () use ($totalPrice) {
            // Update user wallet balance
            Auth::user()->wallet()->decrement('balance', $totalPrice);

            return $this->return_api(true, Response::HTTP_CREATED, null, null, null);
        });
    }

    public function generateBill($validatedData)
    {
        $user = Auth::user();

        $some_data = array(
            'userSecretKey' => config('payment-gateway.key'),
            'categoryCode' => config('payment-gateway.category'),
            'billName' => 'Topup',
            'billDescription' => 'Topup',
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $validatedData['amount'] * 100,
            'billReturnUrl' => route('paymentConfirmation'),
            'billTo' => $user->name,
            'billEmail' => $user->email,
            'billPhone' => "0000000000",
            'billSplitPayment' => 0,
            'billSplitPaymentArgs' => '',
            'billPaymentChannel' => '2',
            'billDisplayMerchant' => 1,
            'billContentEmail' => 'Thank you for topping up your wallet. Your wallet balance will be updated shortly.',
            'billChargeToCustomer' => ''
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, config('payment-gateway.api') . 'createBill');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);
        $obj = json_decode($result);

        try {
            return $obj[0]->BillCode;
        } catch (Exception $e) {
            return null;
        }
    }
}
