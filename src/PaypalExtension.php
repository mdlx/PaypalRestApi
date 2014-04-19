<?php

namespace HQ\Paypal;

use Nette;

// compatibility for nette 2.0.x and 2.1.x
if (!class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
}

/**
 *
 * @author Josef Nevoral <josef.nevoral@hotelquickly.com>
 */
class PaypalExtension extends Nette\DI\CompilerExtension {

	private $defaults = array(
		'http' => array(
			'ConnectionTimeOut' => 30,
			'Retry' => 1
		),
		'mode' => 'sandbox',
		'log' => array(
			'LogEnabled' => true,
			'FileName' => '%appDir%/../log/paypal.log',
			'LogLevel' => 'FINE'
		)
	);

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$builder->addDefinition($this->prefix('paypalFactory'))
			->setClass('HQ\Paypal\Factory\PaypalFactory', array(
				'mode' => $config['mode'],
				'senderPaypalAccount' => $config['senderPaypalAccount'],
				'adaptivePaymentsAccount' => $config['adaptivePaymentsAccount']
			));

		$builder->addDefinition($this->prefix('contextFactory'))
			->setClass('HQ\Paypal\Factory\ContextFactory', array(
				'clientId' => $config['clientId'],
				'secret' => $config['secret'],
				'httpConfig' => $config['http'],
				'mode' => $config['mode'],
				'logConfig' => $config['log']
			));

		// load additional config file for this extension
		$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/paypal.neon'));
	}
}