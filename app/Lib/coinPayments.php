<?php namespace App\Lib;

use App\Lib\CPHelper;

class coinPayments extends CPHelper 
{
	private $secretKey;
	private $merchantId;
	private $isHttpAuth;
	public $paymentErrors;


	public function setMerchantId($merchant)
	{
		$this->merchantId = $merchant;
	}

	public function setSecretKey($secretKey)
	{
		$this->secretKey = $secretKey;
	}


	function createPayment($productName, $currency, $price, $custom, $callbackUrl, $successUrl, $cancelUrl)
	{
		$fields = [
			'merchant' => $this->merchantId,
			'item_name' => $productName,
			'currency' => $currency,
			'amountf' => $price, 
			'ipn_url' => $callbackUrl,
			'success_url' => $successUrl,
			'cancel_url' => $cancelUrl,
			'custom'  => $custom
		];

		//return 'https://www.coinpayments.net/index.php?merchant='.$this->merchantId.'&item_name='.$productName.'&currency='.$currency.'&amountf='.$price.'&ipn_url='.$callbackUrl.'&success_url='.$successUrl.'&cancel_url='.$successUrl.'&cancel_url='.$cancelUrl.'&custom='.$custom.'&cmd=_pay_simple&want_shipping=0';

		return $this->createForm($fields);
	}



	function ValidatePayment($cost, $currency) {
		if(!isset($_POST['ipn_mode']))
		{
			$this->paymentError[] = 'ipn mode not set.';

			return false;

		}

		if($this->isHttpAuth || $_POST['ipn_mode'] != 'hmac') {
			
			//Verify that the http authentication checks out with the users supplied information 
			// 
			if($_SERVER['PHP_AUTH_USER']==$this->merchantId && $_SERVER['PHP_AUTH_PW']==$this->secretKey)
			{
				// Failsafe to prevent malformed requests to throw an error
				if(empty($_POST['merchant']))
				{

					$this->paymentError[] = 'POST data does not contain a merchant ID.';

					return false;

					
				}

				if($this->checkFields()) {
					echo 'IPN OK';
					return true;
				}

			}

			$this->paymentError[] = 'Request does not autheticate (wrong merchant ID + secret Key combo)';

			return false;

		}

		return $this->validatePaymentHMAC();

	}


	private function validatePaymentHMAC() {
		if(!empty($_SERVER['HTTP_HMAC'])) {

			$hmac = hash_hmac("sha512", file_get_contents('php://input'), $this->secretKey);

			if($hmac == $_SERVER['HTTP_HMAC']) {

				if($this->checkFields()) {

					echo 'IPN OK';
					return true;

				}
			}

			$this->paymentError[] = 'HMAC hashes do not match';

			return false;
		}

		$this->paymentError[] = 'Does not contain a HMAC request';

		return false;
	}


	private function checkFields($currency, $cost) {
		// Ensure the paid out merchant is the same as the application
		if($_POST['merchant'] == $this->merchantId) {

			//ensure that the same currency was used (form tampering)
			if(strtoupper($_POST['currency1']) == strtoupper($currency)) {

				// ensure the price was paid
				if(floatval($_POST['amount1']) >= floatval($cost)) {

					// check and make sure coinpayments confirmed the payment
					if(intval($_POST['status']) >= 100 || intval($_POST['status']) == 2) {

						return true;

					}

					if(intval($_POST['status']) == -2) {

						$this->paymentError[] = 'The payment has been chargedback through paypal.';

						return false;

					}

					$this->paymentError[] = 'The payment most likely has not been completed yet.';

					return false;

				}

				$this->paymentError[] = 'The amount paid does not match the original payment.';

			}

			$this->paymentError[] = 'The currency requested and currency paid differ, suspected form tampering.';

			return false;
		}

		$this->paymentError[] = 'Merchant ID does not match.';

		return false;
	}

	public function getErrors() {
		return (empty($this->paymentErrors)) ? $this->paymentErrors : array('None');
	}
}