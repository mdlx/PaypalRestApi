<?php

namespace HQ\Paypal;

/**
 *  Class for charging customers by paypal
 *  @author Michal Juhas <michal.juhas@hotelquickly.com>
 *  @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 *
 */
class PaypalCharge extends \Nette\Object {

	private $apiContext;

	/** @var Models\AccountTransaction */
	private $accountTransactionModel;

	/** @var \HQ\Paypal\Factory\ChargeFactory */
	private $chargeFactory;

	public function __construct(
		\HQ\Paypal\Factory\ChargeFactory $chargeFactory
	) {
		$this->chargeFactory = $chargeFactory;
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
	public function makePaymentUsingCC($creditCardId, $userId, $total, $currency, $paymentDesc)
	{
		$payer = $this->chargeFactory->createPayer($creditCardId, $userId);

		//$item = $this->chargeFactory->createItem($paymentDesc, $currency, 1, $total);
		//$itemList = $this->chargeFactory->createItemList(array($item));
		//$details = $this->chargeFactory->createDetails(0, 0, $total);
		$amount = $this->chargeFactory->createAmount($total, $currency);

		$transaction = $this->chargeFactory->createTransaction($amount, $paymentDesc);

		$paymentResult = $this->chargeFactory->createPayment('authorize', $payer, $transaction);

		return $paymentResult;
	}

	public function makeRefundOfCC($captureId, $total, $currency)
	{
		$amount = $this->chargeFactory->createAmount($total, $currency);

		$refundResult = $this->chargeFactory->createRefund($captureId, $amount);

		return $refundResult;
	}

	public function captureAuthorizedPayment($authId, $total, $currency)
	{
		$authorization = $this->chargeFactory->getAuthorization($authId);
		$amount = $this->chargeFactory->createAmount($total, $currency);
		$capture = $this->chargeFactory->createCapture($authId, $amount);
		$captureResult = $this->chargeFactory->captureAuthorizedTransactionRequest($authorization, $capture);

		return $captureResult;
	}

	public function voidAuthorizedPayment($authId)
	{
		$authorization = $this->chargeFactory->getAuthorization($authId);
		$voidResult = $this->chargeFactory->voidAuthorizedTransactionRequest($authorization);

		return $voidResult;
	}

	public function getCapturedTransactionDetail($transactionId)
	{
		$result = $this->chargeFactory->getCapture($transactionId);

		return $result;
	}

	public function getTransactionStatus($transactionId)
	{
		$result = $this->chargeFactory->getAuthorization($transactionId);

		return $result;
	}

}
