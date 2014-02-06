<?php

namespace HQ\PayPal;

use Nette;

/**
 * Payment credit card vault
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class CreditCardVault extends \Nette\Object {

	/** @var HQ\PayPal\Factory\CreditCardFactory */
	private $creditCardFactory;

	public function __construct()
	{
		//$this->creditCardFactory = $creditCardFactory;
	}


	/**
	 * Save a credit card with paypal
	 *
	 * @param array $params	credit card parameters
	 */
	public function saveCard($ccNumber, $ccExpirationMonth, $ccExpirationYear, $ccCVV)
	{
		$card = $this->creditCardFactory->create();

		$card->setType($this->getCreditCardType($ccNumber));
		$card->setNumber($ccNumber);
		$card->setExpireMonth($ccExpirationMonth);
		$card->setExpireYear($ccExpirationYear);
		$card->setCvv2($ccCVV);

		$card->create($this->_getApiContext());
		return $card->getId();
	}

	/**
	 *
	 * @param string $cardId credit card id obtained from
	 * a previous create API call.
	 */
	public function getCreditCard($cardId)
	{
		return CreditCard::get($cardId, $this->_getApiContext());
	}


	public function getCreditCardType($ccNumber)
	{

	}
}