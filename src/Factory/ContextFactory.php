<?php


namespace HQ\Paypal\Factory;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

/**
 * Class Context
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class ContextFactory extends \Nette\Object
{
	private $clientId;
	private $secret;
	private $httpConfig;
	private $mode;
	private $logConfig;

	function __construct($clientId, $secret, $httpConfig, $mode, $logConfig)
	{
		$this->clientId = $clientId;
		$this->secret = $secret;
		$this->httpConfig = $httpConfig;
		$this->mode = $mode;
		$this->logConfig = $logConfig;
	}

	/**
	 * It is a purpose to not cache apiContext
	 * Paypal needs unique request
	 * @return ApiContext
	 */
	public function createContext()
	{
		$apiContext = new ApiContext(new OAuthTokenCredential(
			$this->clientId,
			$this->secret
		));

		$apiContext->setConfig(array(
			'mode' => $this->mode,
			'http.ConnectionTimeOut' => $this->httpConfig['ConnectionTimeOut'],
			'log.LogEnabled' => $this->logConfig['LogEnabled'],
			'log.FileName' => $this->logConfig['FileName'],
			'log.LogLevel' => $this->logConfig['LogLevel']
		));

		return $apiContext;
	}
} 