<?php

namespace HQ\Paypal\Factory;

use Nette;

use \PayPal\Types\AP\PayRequest;
use \PayPal\Types\AP\ExecutePaymentRequest;
use \PayPal\Types\AP\Receiver;
use \PayPal\Types\AP\ReceiverList;
use \PayPal\Types\AP\PaymentDetailsRequest;
use \PayPal\Types\Common\RequestEnvelope;
use \PayPal\Service\AdaptivePaymentsService;

/**
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class PaymentFactory extends \Nette\Object {

	private $cancelPaymentUrl = 'https://devtools-paypal.com/guide/ap_implicit_payment/php?cancel=true';
	private $returnUrl = 'https://devtools-paypal.com/guide/ap_implicit_payment/php?success=true';
	private $ipnNotificationUrl = 'http://replaceIpnUrl.com';

	/* @var string */
	private $senderPaypalAccount;

	/** @var array */
	private $adaptivePaymentsConfig;

	public function __construct($mode, $senderPaypalAccount, array $adaptivePaymentsAccount)
	{
		$this->senderPaypalAccount = $senderPaypalAccount;

		$this->adaptivePaymentsConfig = array(
			"mode" => $mode,
			"acct1.UserName" => $adaptivePaymentsAccount['UserName'],
			"acct1.Password" => $adaptivePaymentsAccount['Password'],
			"acct1.Signature" => $adaptivePaymentsAccount['Signature'],
			"acct1.AppId" => $adaptivePaymentsAccount['AppId']
		);
	}


	/**
	 * @return \PayPal\Service\AdaptivePaymentsService
	 */
	public function createAdaptivePaymentsService()
	{
		return new AdaptivePaymentsService($this->adaptivePaymentsConfig);
	}


	/**
	 * @param  int $amount
	 * @param  string $currencyCode
	 * @param  string $receiverPayPalAccount
	 * @return \PayPal\Types\AP\PayRequest
	 */
	public function createPayRequest($amount, $currencyCode, $receiverPayPalAccount, $memo = '')
	{
		$payRequest = new PayRequest();

		$receivers = array($this->createReciever($amount, $receiverPayPalAccount));

		$payRequest->receiverList = $this->createRecieverList($receivers);
		$payRequest->senderEmail = $this->senderPaypalAccount;

		$payRequest->requestEnvelope = $this->createRequestEnvelope();
		$payRequest->actionType = "PAY";
		$payRequest->feesPayer = "SENDER";
		$payRequest->memo = $memo;
		$payRequest->cancelUrl = $this->cancelPaymentUrl;
		$payRequest->returnUrl = $this->returnUrl;
		$payRequest->currencyCode = $currencyCode;
		$payRequest->ipnNotificationUrl = $this->ipnNotificationUrl;
		$payRequest->payKeyDuration = 'P15D';	// reserve payKey for 15 days

		return $payRequest;
	}


	/**
	 * @param  int $amount
	 * @param  string $currencyCode
	 * @param  string $receiverPayPalAccount
	 * @return \PayPal\Types\AP\PayRequest
	 */
	public function createExecutePaymentRequest($payKey)
	{
		$executePaymentRequest = new ExecutePaymentRequest($this->createRequestEnvelope(), $payKey);

		return $executePaymentRequest;
	}


	/**
	 * @param  string $payKey
	 * @return \PayPal\Types\Ap\PaymentDetailsRequest
	 */
	public function createPaymentDetailsRequest($payKey)
	{
		$paymentDetailsRequest = new PaymentDetailsRequest($this->createRequestEnvelope());
		$paymentDetailsRequest->payKey = $payKey;

		return $paymentDetailsRequest;
	}


	/**
	 * @param  int $amount
	 * @param  string $receiverPayPalAccount
	 * @return \PayPal\Types\AP\Receiver
	 */
	public function createReciever($amount, $receiverPayPalAccount)
	{
		$receiver = new Receiver();
		$receiver->amount = $amount;
		$receiver->email = $receiverPayPalAccount;

		return $receiver;
	}


	/**
	 * @param  array  $receiver of receiver objects
	 * @return \PayPal\Types\AP\ReceiverList
	 */
	public function createRecieverList(array $recievers)
	{
		return new ReceiverList($recievers);
	}


	public function createRequestEnvelope()
	{
		return new RequestEnvelope("en_US");
	}
}
