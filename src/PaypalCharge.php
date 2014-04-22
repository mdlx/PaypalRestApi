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

    /** @var array Braintree library initalization params */
    private $payPalParams;

    /** @var Models\AccountTransaction */
    private $accountTransactionModel;

    private $chargeFactory;

    public function __construct(
    	$payPalParams,
    	\HQ\Paypal\Factory\ChargeFactory $chargeFactory
    ) {
		$this->payPalParams = $payPalParams;
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
	public function makePaymentUsingCC($creditCardId, $total, $currency, $paymentDesc)
	{
		$this->chargeFactory->createPayer($creditCardId)
			->createAmount($total, $currency)
			->createTransaction($paymentDesc)
			->createPayment();


		return $payment;
	}

}