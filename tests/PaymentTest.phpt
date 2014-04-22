<?php

/**
 * Test: Payment test
 * @author Josef Nevoral <josef.nevoral@gmail.com>
 */

namespace Tests;

use Nette,
	Tester,
	Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/BaseTestCase.php';
require __DIR__ . '/../src/Factory/PaymentFactory.php';
require __DIR__ . '/../src/PaypalPayment.php';

class PaymentTestCase extends BaseTestCase {

	public function testSuccessSendMoneyToPaypalAccount()
	{
		$payResponse = (object) array('paymentExecStatus' => 'CREATED', 'payKey' => 'xxx');
		$paymentDetails = new \stdClass;

		$paymentFactory = $this->mockista->create('\HQ\Paypal\Factory\PaymentFactory', array(
			'createPayRequest' => new \stdClass(),
			'createAdaptivePaymentsService' => $this->mockista->create('\PayPal\Service\AdaptivePaymentsService', array(
				'Pay' => $payResponse,
				'PaymentDetails' => $paymentDetails
			)),
			'createPaymentDetailsRequest' => null
		));

		$paypalPayment = new \HQ\Paypal\PaypalPayment($paymentFactory);

		Assert::same($paymentDetails, $paypalPayment->sendMoneyToPaypalAccount(20.0, 'USD', 'josef.nevoral@gmail.com'));
	}


	public function testFailedSendMoneyToPaypalAccount()
	{
		$payResponse = (object) array('paymentExecStatus' => 'FAILED');

		$paymentFactory = $this->mockista->create('\HQ\Paypal\Factory\PaymentFactory', array(
			'createPayRequest' => new \stdClass(),
			'createAdaptivePaymentsService' => $this->mockista->create('\PayPal\Service\AdaptivePaymentsService', array(
				'Pay' => $payResponse
			))
		));

		$paypalPayment = new \HQ\Paypal\PaypalPayment($paymentFactory);

		Assert::null($paypalPayment->sendMoneyToPaypalAccount(20.0, 'USD', 'josef.nevoral@gmail.com'));
	}
}

\run(new PaymentTestCase());
