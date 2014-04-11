<?php

namespace HQ\Paypal;

/**
 *  Class for sending payments to another paypal account
 *
 *  @author Michal Juhas <michal.juhas@hotelquickly.com>
 *  @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 *
 */
class PaypalPayment extends \Nette\Object {

	/** @var \HQ\Paypal\Factory\PaypalFactory */
	private $paypalFactory;

	private $paypalPaymentResponse;


	public function __construct(
		\HQ\Paypal\Factory\PaypalFactory $paypalFactory
	) {
		$this->paypalFactory = $paypalFactory;
		$this->paypalPaymentResponse = new PaypalPaymentResponseCrate();
	}


	public function sendMoneyToPaypalAccount($amount, $currencyCode, $receiverPayPalAccount)
	{
		$payResponse = $this->triggerImplicitPayment($amount, $currencyCode, $receiverPayPalAccount);

		if ($payResponse && $payResponse->paymentExecStatus == 'CREATED') {
			return $this->executeImplicitPayment($payResponse->payKey);
		} else {
			throw new PaypalPaymentInvalidException('Paypal Payment was not created properly, payKey:' . $payResponse->payKey . ', paymentExecStatus:' . $payResponse->paymentExecStatus);
		}

		return null;
	}

	private function triggerImplicitPayment($amount, $currencyCode, $receiverPayPalAccount)
	{
		$payRequest = $this->paypalFactory->createPayRequest($amount, $currencyCode, $receiverPayPalAccount);
		$adaptivePaymentsService = $this->paypalFactory->createAdaptivePaymentsService();

		return $adaptivePaymentsService->Pay($payRequest);
	}

	private function executeImplicitPayment($payKey)
	{
		$paymentDetailsRequest = $this->paypalFactory->createPaymentDetailsRequest($payKey);
		$adaptivePaymentsService = $this->paypalFactory->createAdaptivePaymentsService();

		$paymentDetails = $adaptivePaymentsService->PaymentDetails($paymentDetailsRequest);

		$this->paypalPaymentResponse->trackingId = $paymentDetails->trackingId;
		$this->paypalPaymentResponse->transactionId = $paymentDetails->paymentInfoList->paymentInfo[0]->transactionId;
		$this->paypalPaymentResponse->payKey = $paymentDetails->payKey;

		return $this->paypalPaymentResponse;
	}

}

class PaypalPaymentInvalidException extends \Exception {

}

class PaypalPaymentResponseCrate {
	public $trackingId;
	public $transactionId;
	public $payKey;
}