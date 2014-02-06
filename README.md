## Installation
## Installation
Easiest way is to use [Composer](http://getcomposer.org/):

```sh
$ composer require hotel-quickly/paypal-rest-api:@dev
```

Register extension in your bootstrap.php
```php
$configurator->onCompile[] = function ($configurator, $compiler) {
	$compiler->addExtension('paypal', new \HQ\Paypal\PaypalExtension());
};
```

## Possible configuration

```yml
paypal: # sandbox account access
	clientId: "EBWKjlELKMYqRNQ6sYvFo64FtaRLRR5BdHEESmha49TM"
	secret: "EO422dn3gQLgDbuwqTjzrFgFtaRLRR5BdHEESmha49TM"
	senderPaypalAccount: platfo_1255077030_biz@gmail.com

	adaptivePaymentsAccount:
		UserName: jb-us-seller_api1.paypal.com
		Password: WX4WTU3S8MY44S7F
		Signature: AFcWxV21C7fd0v3bYYYRCpSSRl31A7yDhhsPUU2XhtMoZXsWHFxu-RWy
		AppId: APP-80W284485P519543T

	mode: sandbox # sandbox, production
	http:
		ConnectionTimeOut: 30
		Retry: 1
	log:
		LogEnabled: true
		FileName: %logDir%/paypal.log
		LogLevel: FINE # Logging level can be one of FINE, INFO, WARN or ERROR
```