<?php

/**
 * Test: Credit card validator
 * @author Josef Nevoral <josef.nevoral@gmail.com>
 */

namespace Tests;

use Nette,
	Tester,
	Tester\Assert,
	\HQ\CreditCard\Validator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../src/CreditCard/Validator.php';

class CreditCardValidatorTestCase extends Tester\TestCase {

	public function testBrandRecognition()
	{
		$ccValidator = new Validator;

		// VISA
		$ccNumber = '4005519200000004';
		Assert::same(Validator::VISA_BRAND, $ccValidator->getCreditCardBrand($ccNumber));

		// MASTERCARD
		$ccNumber = '5555555555554444';
		Assert::same(Validator::MASTERCARD_BRAND, $ccValidator->getCreditCardBrand($ccNumber));

		// AMERICAN_EXPRESS
		$ccNumber = '378282246310005';
		Assert::same(Validator::AMERICAN_EXPRESS_BRAND, $ccValidator->getCreditCardBrand($ccNumber));

		// DISCOVER
		$ccNumber = '6011111111111117';
		Assert::same(Validator::DISCOVER_BRAND, $ccValidator->getCreditCardBrand($ccNumber));

		// unsupported number
		$ccNumber = '111111111111111';
		Assert::exception(function() use ($ccNumber, $ccValidator) {
			$ccValidator->getCreditCardBrand($ccNumber);
		}, '\HQ\CreditCard\UnsupportedCreditCardException');
	}
}

\run(new CreditCardValidatorTestCase());
