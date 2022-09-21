<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index($billId)
    {
        // Run Bill
        $this->runBill($billId);
    }

    function runBill($billId)
    {

        $transaction = Transaction::findOrFail($billId);
        $user = $transaction->user;

        $some_data = array(
            'userSecretKey' => config('payment-gateway.key'),
            'billCode' => $billId,
            'billpaymentPayorName' => $user->name,
            'billpaymentPayorPhone' => $user->phone_number ?? '',
            'billpaymentPayorEmail' => $user->email,
        );


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, config('payment-gateway.api') . 'runBill');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $obj = json_decode($result);
        echo $result;
    }

    public function paymentConfirmation(Request $request)
    {

        // Get transaction model
        $transaction = Transaction::findOrFail($request->billcode);

        if ($request->status_id == 1) {
            $transaction->status = "Success";
        }

        // If status completed/successfull
        if ($transaction->status == "Success") {
            $title = __('Success');
            $icon = __('✔');
            $desc = __('Thank you for topping up your wallet.<br/>Your wallet balance will be updated shortly.<br/>You may close this page now.');
            // Update user wallet ballance
            $user = User::findOrFail($transaction->user_id);

            $user->wallet()->increment('balance', $transaction->amount);
        } else {
            $title = __('Failed');
            $desc = __('Failed to make the purchase, please try again later.<br/>You may close this page now.');
            $icon = __('✘');
        }

        return view('paymentGatewayStatusPage', compact('title', 'desc', 'icon'));
    }
}
