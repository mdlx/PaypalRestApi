<?php

namespace HQ\Paypal;

use HQ\Paypal\Factory\ContextFactory;
use Nette;
use HQ\PayPal\Factory\CreditCardFactory;

/**
 * Payment credit card vault
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class Vault extends \Nette\Object {

	/** @var CreditCardFactory */
	private $creditCardFactory;

	/** @var  ContextFactory */
	private $contextFactory;

	public function __construct(
        Factory\CreditCardFactory $creditCardFactory,
		ContextFactory $contextFactory
    ) {
		$this->creditCardFactory = $creditCardFactory;
		$this->contextFactory = $contextFactory;
	}


	/**
	 * Save a credit card with paypal
	 *
	 * @param array $params	credit card parameters
	 */
	public function saveCard($payerId, $firstName, $lastName, $ccNumber, $ccExpirationMonth, $ccExpirationYear, $ccCVV, $ccBrandType)
	{
		$card = $this->creditCardFactory->create();

		$card->setPayerId($payerId);
		$card->setFirstName($firstName);
		$card->setLastName($lastName);
		$card->setType($ccBrandType);
		$card->setNumber($ccNumber);
		$card->setExpireMonth($ccExpirationMonth);
		$card->setExpireYear($ccExpirationYear);
		$card->setCvv2($ccCVV);

		$card->create($this->contextFactory->createContext());
		return $card->getId();
	}

	/**
	 * @param string $cardId credit card id obtained from a previous create API call.
     * @return string
	 */
	public function getCreditCard($cardId)
	{
		return CreditCard::get($cardId, $this->contextFactory->createContext());
	}
}