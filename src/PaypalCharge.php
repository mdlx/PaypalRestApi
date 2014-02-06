<?php

namespace HQ\Paypal;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;


/**
 *  Class for charging customers by paypal
 *  @author Michal Juhas <michal.juhas@hotelquickly.com>
 *  @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 *
 */
class PaypalCharge extends \Nette\Object {

   private $apiContext;

    /** @var array Braintree library initalization params */
    private $payPalParams;

    /** @var Models\AccountTransaction */
    private $accountTransactionModel;

    public function __construct($payPalParams)
    {
		$this->payPalParams = $payPalParams;
    }

	private function _getApiContext()
	{
		if ($this->apiContext) {
			return $this->apiContext;
		}

		$this->apiContext = new ApiContext(new OAuthTokenCredential(
			$this->payPalParams['clientId'],
			$this->payPalParams['secret']
		));

		return $this->apiContext;
	}

	/**
	 * Create a payment using a previously obtained
	 * credit card id. The corresponding credit
	 * card is used as the funding instrument.
	 *
	 * @param string $creditCardId credit card id
	 * @param string $total Payment amount with 2 decimal points
	 * @param string $currency 3 letter ISO code for currency
	 * @param string $paymentDesc
	 */
	public function makePaymentUsingCC($creditCardId, $total, $currency, $paymentDesc)
	{
		$ccToken = new CreditCardToken();
		$ccToken->setCreditCardId($creditCardId);

		$fi = new FundingInstrument();
		$fi->setCreditCardToken($ccToken);

		$payer = new Payer();
		$payer->setPaymentMethod("credit_card");
		$payer->setFundingInstruments(array($fi));

		// Specify the payment amount.
		$amount = new Amount();
		$amount->setCurrency($currency);
		$amount->setTotal($total);
		// ###Transaction
		// A transaction defines the contract of a
		// payment - what is the payment for and who
		// is fulfilling it. Transaction is created with
		// a `Payee` and `Amount` types
		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setDescription($paymentDesc);

		$payment = new Payment();
		$payment->setIntent("sale");
		$payment->setPayer($payer);
		$payment->setTransactions(array($transaction));

		$payment->create($this->_getApiContext());
		return $payment;
	}

}