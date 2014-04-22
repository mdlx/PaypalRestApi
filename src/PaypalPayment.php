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

	/** @var \HQ\Paypal\Factory\PaymentFactory */
	private $paymentFactory;
	private $adaptivePaymentsService;
	private $paypalPaymentResponse;


	public function __construct(
		\HQ\Paypal\Factory\PaymentFactory $paymentFactory
	) {
		$this->paymentFactory = $paymentFactory;
		$this->adaptivePaymentsService = $paymentFactory->createAdaptivePaymentsService();
		$this->paypalPaymentResponse = new PaypalPaymentResponseCrate();
	}


	public function sendMoneyToPaypalAccount($amount, $currencyCode, $receiverPayPalAccount)
	{
		$payResponse = $this->triggerImplicitPayment($amount, $currencyCode, $receiverPayPalAccount);

		if ($payResponse && $payResponse->paymentExecStatus != 'COMPLETED') {
			throw new PaypalPaymentInvalidException('Paypal Payment was not completed properly, payKey:' . $payResponse->payKey . ', paymentExecStatus:' . $payResponse->paymentExecStatus);
		}

		$executePaymentResponse = $this->executeImplicitPayment($payResponse->payKey);

		if ($executePaymentResponse->responseEnvelope->ack == 'Failure') {
			$jsonError = json_encode($executePaymentResponse->error);
			throw new PaypalPaymentInvalidException('Execute Payment failed, jsonError:' . $jsonError);
		}

		$paymentDetails = $this->getPaymentDetails($payResponse->payKey);

		return $paymentDetails;
	}

	private function triggerImplicitPayment($amount, $currencyCode, $receiverPayPalAccount)
	{
		$payRequest = $this->paymentFactory->createPayRequest($amount, $currencyCode, $receiverPayPalAccount);
		$payResponse = $this->adaptivePaymentsService->Pay($payRequest);

		return $payResponse;
	}

	private function executeImplicitPayment($payKey)
	{
		$executePaymentRequest = $this->paymentFactory->createExecutePaymentRequest($payKey);
		$executePaymentResponse = $this->adaptivePaymentsService->ExecutePayment($executePaymentRequest);

		return $executePaymentResponse;
	}

	private function getPaymentDetails($payKey) {
		$paymentDetailsRequest = $this->paymentFactory->createPaymentDetailsRequest($payKey);
		$paymentDetailsResponse = $this->adaptivePaymentsService->PaymentDetails($paymentDetailsRequest);

		$this->paypalPaymentResponse->trackingId = $paymentDetailsResponse->trackingId;
		$this->paypalPaymentResponse->transactionId = $paymentDetailsResponse->paymentInfoList->paymentInfo[0]->transactionId;
		$this->paypalPaymentResponse->payKey = $paymentDetailsResponse->payKey;

		return $this->paypalPaymentResponse;
	}

}

class PaypalPaymentInvalidException extends \Exception {

}

class PaypalPaymentResponseCrate {
	public $trackingId;
	public $transactionId;
	public $payKey;

	public $error;
}