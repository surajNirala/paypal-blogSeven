<?php

namespace App\Http\Controllers;

use PayPal\Api\Item;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\Transactions;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;

class PaymentController extends Controller
{
    protected  $apiContext;
    public function __construct() {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                'AUrbMVLikWi9g_yzC9E5TDOEUl-sEii57--7YXNm8ahQJtxqUp8l6fgHosPTHOpRo5UmnIFIWpsEtu_7',     // ClientID
                'EGDYMflQJfVvt1HqN3OAWIDr0Z_x1R1pHMoekp-bi1xt-bCw4JAr_LLUpeXzh-qXPhHse_RJTw8qTh1w'      // ClientSecret
            )
        );

        $this->apiContext = $apiContext;
    }

    public function create(Request $request)
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // first product
        $item1 = new Item();
        $item1->setName('Ground Coffee 40 oz')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice(7.5);

        // Second product
        $item2 = new Item();
        $item2->setName('Granola bars')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setSku("321321") // Similar to `item_number` in Classic API
            ->setPrice(2);

            $itemList = new ItemList();
            $itemList->setItems(array($item1, $item2));

            $details = new Details();
            $details->setShipping(1.2)
                ->setTax(1.3)
                ->setSubtotal(17.50);
            
            $amount = new Amount();
            $amount->setCurrency("USD")
                ->setTotal(20)
                ->setDetails($details);
            
            $transaction = new Transaction();
            $transaction->setAmount($amount)
                        ->setItemList($itemList)
                        ->setDescription("Payment description")
                        ->setInvoiceNumber(uniqid());
    }

    public function execute($getAmount = null)
    {
        $paymentId = request()->paymentId; 
        $payerID = request()->PayerID;

        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerID);

        $execution = new PaymentExecution();
        $amount = new Amount();
        $transaction = new Transactions();
        $details = new Details();

        /* $details->setShipping(2.2)
                ->setTax(1.3)
                ->setSubtotal(17.50); */
                
        $amount->setCurrency('USD');
        $amount->setTotal($getAmount);
        $amount->setDetails($getAmount);
        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);

        $result = $payment->execute($execution, $this->apiContext);

        return $result;
    }
}
