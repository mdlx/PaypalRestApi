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
Note: create your own sandbox application at https://developer.paypal.com/
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

## The MIT License (MIT)

Copyright (c) 2014 Hotel Quickly Ltd.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
