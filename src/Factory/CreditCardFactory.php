<?php

namespace HQ\Paypal\Factory;

use Nette;

/**
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class CreditCardFactory extends \Nette\Object {

	public function create()
	{
		return new \PayPal\Api\CreditCard();
	}
}