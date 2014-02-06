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


	public function __construct(
		\HQ\Paypal\Factory\PaypalFactory $paypalFactory
	) {
		$this->paypalFactory = $paypalFactory;
	}


	public function sendMoneyToPaypalAccount($amount, $currencyCode, $receiverPayPalAccount)
	{
		$payResponse = $this->triggerImplicitPayment($amount, $currencyCode, $receiverPayPalAccount);

		if ($payResponse && $payResponse->paymentExecStatus == 'CREATED') {
			return $this->executeImplicitPayment($payResponse->payKey);
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

		return $adaptivePaymentsService->PaymentDetails($paymentDetailsRequest);
	}

}