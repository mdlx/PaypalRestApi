<?php

namespace HQ\Paypal\Factory;

use Nette;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\CreditCard;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Capture;
use PayPal\Api\Authorization;

/**
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class ChargeFactory extends \Nette\Object {

	private $contextFactory;
	private $apiContext;

	private $payer;
	private $amount;
	private $transaction;
	private $payment;
	private $authorization;
	private $capture;

	public function __construct(
		ContextFactory $contextFactory
	) {
		$this->contextFactory = $contextFactory;

		$this->apiContext = $this->contextFactory->createContext();
	}


    public function createPayer($creditCardId, $userId)
	{
		$ccToken = new CreditCardToken();
		$ccToken->setCreditCardId($creditCardId);

		$fi = new FundingInstrument();
		$fi->setCreditCardToken($ccToken);

		$payerInfo = new PayerInfo();
		$payerInfo->setPayerId($userId);

		$payer = new Payer();
		$payer->setPaymentMethod("credit_card")
			->setFundingInstruments(array($fi))
			->setPayerInfo($payerInfo);

		return $payer;
	}


	public function createItem($name, $currency, $quantity, $price)
	{
		$item = new Item();
		$item->setName($name)
			->setCurrency($currency)
			->setQuantity($quantity)
			->setPrice($price);

		return $item;
	}


	public function createItemList(array $itemArray)
	{
		$itemList = new ItemList();
		$itemList->setItems($itemArray);

		return $itemList;
	}


	public function createDetails($shipping, $tax, $subtotal)
	{
		$details = new Details();
		$details->setShipping($shipping)
			->setTax($tax)
			->setSubtotal($subtotal);

		return $details;
	}


	public function createAmount($total, $currency, Details $details = null)
	{
		// Specify the payment amount.
		$amount = new Amount();
		$amount->setCurrency($currency)
			->setTotal($total);

		if (isset($details)) {
			$amount->setDetails($details);
		}

		return $amount;
	}


	public function createTransaction(Amount $amount, $paymentDesc, ItemList $itemList = null)
	{
		// ###Transaction
		// A transaction defines the contract of a
		// payment - what is the payment for and who
		// is fulfilling it. Transaction is created with
		// a `Payee` and `Amount` types
		$transaction = new Transaction();
		$transaction->setAmount($amount)
			->setDescription($paymentDesc);

		if (isset($itemList)) {
			$transaction->setItemList($itemList);
		}


		return $transaction;
	}


	public function createPayment(Payer $payer, Transaction $transaction)
	{
		$payment = new Payment();
		$payment->setIntent("sale")
			->setPayer($payer)
			->setTransactions(array($transaction));

		$payment->create($this->apiContext);

		return $payment;
	}


	public function createAuthorizationId($apiContext)
	{
		$authId = createAuthorization($apiContext);

		return $authId;
	}


	public function getAuthorization($authId)
	{
		$authorization = Authorization::get($authId, $this->apiContext);

		return $authorization;
	}


	public function createCapture($authId, Amount $amount)
	{
		$capture = new Capture();
		$capture->setId($authId)
			->setAmount($amount);

		return $capture;
	}


	public function captureAuthorizedTransactionRequest(Authorization $authorization, Capture $capture)
	{
		return $authorization->capture($capture, $this->apiContext);
	}


	public function voidAuthorizedTransactionRequest(Authorization $authorization)
	{
		return $authorization->void($this->apiContext);
	}
}