<?php

namespace HQ\Paypal\Factory;

use Nette;
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
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class ChargeFactory extends \Nette\Object {

	private $payer;
	private $amount;
	private $transaction;
	private $payment;


    public function createPayer($creditCardId)
	{
		$ccToken = new CreditCardToken();
		$ccToken->setCreditCardId($creditCardId);

		$fi = new FundingInstrument();
		$fi->setCreditCardToken($ccToken);

		$payer = new Payer();
		$payer->setPaymentMethod("credit_card");
		$payer->setFundingInstruments(array($fi));

		$this->payer = $payer;

		return $this;
	}


	public function createAmount($total, $currency)
	{
		// Specify the payment amount.
		$amount = new Amount();
		$amount->setCurrency($currency);
		$amount->setTotal($total);

		$this->amount = $amount;

		return $this;
	}


	public function createTransaction($paymentDesc)
	{
		// ###Transaction
		// A transaction defines the contract of a
		// payment - what is the payment for and who
		// is fulfilling it. Transaction is created with
		// a `Payee` and `Amount` types
		$transaction = new Transaction();
		$transaction->setAmount($this->amount);
		$transaction->setDescription($paymentDesc);

		$this->transaction = $transaction;

		return $this;
	}


	public function createPayment()
	{
		$payment = new Payment();
		$payment->setIntent("sale");
		$payment->setPayer($this->payer);
		$payment->setTransactions(array($this->transaction));

		$payment->create($this->_getApiContext());

		return $this;
	}
}