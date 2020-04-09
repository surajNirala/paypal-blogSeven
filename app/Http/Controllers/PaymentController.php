<?php

namespace App\Http\Controllers;

use Exception;
use PayPal\Api\Item;
use PayPal\Api\Sale;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transactions;
use PayPal\Api\PaymentExecution;
use PHPUnit\TextUI\ResultPrinter;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;

class PaymentController extends Controller
{

    protected $apiContext;
    protected $baseUrl = "http://localhost:8000";
    protected $clientId = "AUrbMVLikWi9g_yzC9E5TDOEUl-sEii57--7YXNm8ahQJtxqUp8l6fgHosPTHOpRo5UmnIFIWpsEtu_7"; // ClientID
    protected $clientSecret = "EGDYMflQJfVvt1HqN3OAWIDr0Z_x1R1pHMoekp-bi1xt-bCw4JAr_LLUpeXzh-qXPhHse_RJTw8qTh1w";  // ClientSecret
    protected $enableSandbox = true;

    
    public function __construct() {
        // set credentials 
        $this->apiContext = $this->getApiContext($this->clientId,$this->clientSecret,$this->enableSandbox);
    }
    // return $this->apiContext;
    function getApiContext($clientId,$clientSecret,$enableSandbox)
    {
        $credentials = new OAuthTokenCredential($clientId, $clientSecret);
        $setApiContext  = new ApiContext($credentials,$enableSandbox);
        $setApiContext->setConfig([
            'mode' => $enableSandbox ? 'sandbox' : 'live'
        ]);
        return $setApiContext;
    }

    

    public function create(Request $request)
    {
        /* $apiContext = new ApiContext(
            new OAuthTokenCredential("AUrbMVLikWi9g_yzC9E5TDOEUl-sEii57--7YXNm8ahQJtxqUp8l6fgHosPTHOpRo5UmnIFIWpsEtu_7",
                                    "EGDYMflQJfVvt1HqN3OAWIDr0Z_x1R1pHMoekp-bi1xt-bCw4JAr_LLUpeXzh-qXPhHse_RJTw8qTh1w",
        ),$enableSandbox = true
        );
        $apiContext->setConfig([
            'mode' => $enableSandbox ? 'sandbox' : 'live'
        ]); */
        // return $apiContext;
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
            $details->setShipping(2.2)
                    ->setTax(1.3)
                    ->setSubtotal(17.50);
            
            $amount = new Amount();
            $amount->setCurrency("USD")
                   ->setDetails($details)
                   ->setTotal(21);
            
            $transaction = new Transaction();
            $transaction->setItemList($itemList)
                        ->setAmount($amount)
                        ->setDescription("Payment description")
                        ->setInvoiceNumber(uniqid());
            
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl("http://localhost:8000/execute-payment")
                        ->setCancelUrl("http://localhost:8000/cancel");

            $payment = new Payment();
            $payment->setIntent("sale")
                    ->setPayer($payer)
                    ->setRedirectUrls($redirectUrls)
                    ->setTransactions(array($transaction));

            try {
                $payment->create($this->apiContext);
            }catch(PayPalConnectionException $e){
                echo $e->getCode(); // Prints the Error Code
                echo $e->getData();
                die($e);
            } catch (Exception $ex) {
                die($ex);
            }
            // $payment->create($this->apiContext);

            $approvalUrl = $payment->getApprovalLink();

            // echo $approvalUrl;

            // echo "-------------------------";
            
            // return $payment;
            return redirect($approvalUrl);
    }

    // get payment Execution
    public function execute()
    {
        $paymentId = request()->paymentId; 
        $payerID = request()->PayerID;
        // return $payerID;
        $payment = Payment::get($paymentId,$this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerID);

        $amount = new Amount();
        $transaction = new Transaction();
        $details = new Details();

        $details->setShipping(2.2)
        ->setTax(1.3)
        ->setSubtotal(17.50);

        $amount->setCurrency('USD');
        $amount->setTotal(21);
        $amount->setDetails($details);
        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);

        $result = $payment->execute($execution,$this->apiContext);

        return $result;
    }


    public function getSales($saleId)
    {
        $saleId = $saleId;
        try {
            $sale = Sale::get($saleId, $this->apiContext);
        }catch(PayPalConnectionException $e){
            echo $e->getCode(); // Prints the Error Code
            echo $e->getData();
            die($e);
        } catch (Exception $ex) {
            die($ex);
        }
        return $sale;
    }
    
    public function getPaymentList()
    {
        try {
            $params = array('count' => 10, 'start_index' => 5);
            $payments = Payment::all($params, $this->apiContext);
        }catch(Exception  $e){
            echo $e->getCode(); // Prints the Error Code
            die($e);
        } catch (Exception $ex) {
            die($ex);
        }
        return $payments;
    }

    public function getpaymentDetails($payID)
    {
        // $payID = "PAYID-L2G6SDQ4UX98259UE105001B";
        try {
            $payment = Payment::get($payID, $this->apiContext);
        }catch(PayPalConnectionException $e){
            echo $e->getCode(); // Prints the Error Code
            echo $e->getData();
            die($e);
        }catch (Exception $ex) {
            die($ex);
        }
        return $payment;
    }
}
