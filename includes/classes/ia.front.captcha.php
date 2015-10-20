<?php
//##copyright##

class iaCaptcha extends abstractUtil
{
	// Get a key from https://www.google.com/recaptcha
	protected $_publicKey = '';
	protected $_privateKey = '';

	protected $_theme;

	protected $_error;


	public function __construct()
	{
		parent::init();

		require_once dirname(__FILE__) . IA_DS . 'recaptchalib.php';

		$this->_publicKey = $this->iaCore->get('recaptcha_publickey');
		$this->_privateKey = $this->iaCore->get('recaptcha_privatekey');
		$this->_theme = $this->iaCore->get('recaptcha_theme');

		if ($this->_privateKey)
		{
			$this->reCaptcha = new ReCaptcha($this->_privateKey);
		}
	}

	public function getImage()
	{
		if (!$this->_publicKey || !$this->_privateKey)
		{
			return iaLanguage::get('recaptcha_set_configuration');
		}

		$theme = '';
		if ('light' != $this->iaCore->get('recaptcha_theme'))
		{
			$theme = ' data-theme="' . $this->iaCore->get('recaptcha_theme') . '"';
		}

		$code = <<<CODE
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="g-recaptcha" data-sitekey="{$this->_publicKey}"{$theme}></div>
CODE;

		return $code;
	}

	public function validate()
	{
		if (!empty($_POST["g-recaptcha-response"]))
		{
			$response = $this->reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);

			if ($response != null && $response->success)
			{
				return true;
			}
		}

		return false;
	}

	public function getPreview()
	{
		return $this->getImage();
	}
}