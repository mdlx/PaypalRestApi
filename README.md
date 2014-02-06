Possible configuration

```yml
paypal:
	mode: sandbox # sandbox, production
	http:
		ConnectionTimeOut: 30
		Retry: 1
	log:
		LogEnabled: true
		FileName: %logDir%/paypal.log
		LogLevel: FINE # Logging level can be one of FINE, INFO, WARN or ERROR
```