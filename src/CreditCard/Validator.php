<?php

namespace HQ\Paypal\CreditCard;

use Nette;

/**
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class Validator extends \Nette\Object {

	const VISA_BRAND = 'visa';
	const MASTERCARD_BRAND = 'mastercard';
	const AMERICAN_EXPRESS_BRAND = 'amex';
	const DISCOVER_BRAND = 'discover';

	/** @see http://www.regular-expressions.info/creditcard.html */
	private $validationRules = array(
		self::VISA_BRAND => '^4[0-9]{12}(?:[0-9]{3})?$',
		self::MASTERCARD_BRAND => '^5[1-5][0-9]{14}$',
		self::AMERICAN_EXPRESS_BRAND => '^3[47][0-9]{13}$',
		self::DISCOVER_BRAND => '^6(?:011|5[0-9]{2})[0-9]{12}$',
	);


	public function getCreditCardBrand($ccNumber)
	{
		foreach ($this->validationRules as $brand => $rule) {
			$match = preg_match("/$rule/", $ccNumber);
			if ($match) return $brand;
		}

		throw new UnsupportedCreditCardException();
	}

}


class UnsupportedCreditCardException extends \Exception {

}