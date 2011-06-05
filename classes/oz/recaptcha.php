<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Simple wrapper for Googles reCAPTCHA library
 *
 * @package openzula/kohana-recaptcha
 * @author Alex Cartwright <alex@openzula.org>
 * @copyright Copyright (c) 2011, OpenZula
 * @license http://openzula.org/license-bsd-3c BSD 3-Clause License
 */

class Oz_Recaptcha {

	/**
	 * Public key
	 * @var string
	 */
	protected $_public_key;

	/**
	 * Private key
	 * @var string
	 */
	protected $_private_key;

	/**
	 * Error code returned when checking the answer
	 * @var string
	 */
	protected $_error;

	/**
	 * Load the reCAPTCHA PHP library and configure the keys from the config
	 * file or the provided array argument.
	 *
	 * @param array $config
	 * @return object
	 */
	public function __construct(array $config=NULL)
	{
		require_once Kohana::find_file('vendor', 'recaptcha/recaptchalib');

		if (empty($config))
		{
			$config = Kohana::config('recaptcha');
		}
		$this->_public_key = $config['public_key'];
		$this->_private_key = $config['private_key'];
	}

	/**
	 * Generate the HTML to display to the client
	 *
	 * @return string
	 */
	public function get_html()
	{
		return recaptcha_get_html(
			$this->_public_key,
			$this->_error,
			(Request::$initial->protocol() === 'https')
		);
	}

	/**
	 * Returns bool true if successful, bool false if not.
	 *
	 * @param string $challenge
	 * @param string $response
	 * @return bool
	 */
	public function check($challenge=NULL, $response=NULL)
	{
		if (NULL === $challenge)
		{
			$challenge = Arr::get($_POST, 'recaptcha_challenge_field', FALSE);
		}
		if (NULL === $response)
		{
			$response = Arr::get($_POST, 'recaptcha_response_field', FALSE);
		}
		$result = recaptcha_check_answer($this->_private_key, Request::$client_ip, $challenge, $response);
		$this->_error = $result->error;
		return (bool) $result->is_valid;
	}

}
