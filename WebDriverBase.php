<?php

require_once 'WebElement.php';
require_once 'WebDriverException.php';

class WebDriverBase
{
	const SUCCESS = 0;

	protected $requestURL;
	protected $_curl;

	public function __construct($requestURL) {
		$this->requestURL = $requestURL;
	}

	protected function &curlInit($url) {
		if( $this->_curl === null )
			$this->_curl = curl_init( $url );
		else {
			curl_setopt( $this->_curl, CURLOPT_HTTPGET, true );
			curl_setopt( $this->_curl, CURLOPT_URL, $url );
		}

		curl_setopt( $this->_curl, CURLOPT_HTTPHEADER, array("application/json;charset=UTF-8"));
		curl_setopt( $this->_curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $this->_curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $this->_curl, CURLOPT_HEADER, false );

		return $this->_curl;
	}

	protected function curlClose() {
		if( $this->_curl !== null ) {
			curl_close( $this->_curl );
			$this->_curl = null;
		}
	}

	protected function preparePOST($session, $postargs) {
		curl_setopt($session, CURLOPT_POST, true);
		if ($postargs) {
			curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
		}
	}

	/**
	 * Execute POST request
	 * @param string $request URL REST request
	 * @param string $postargs POST data
	 * @return string $response Response from POST request
	 */
	protected function execute_rest_request_POST($request, $postargs) {
		$session = $this->curlInit($request);
		$this->preparePOST($session, $postargs);
		$response = trim(curl_exec($session));
		return $response;
	}

	protected function prepareGET( $session ) {

		//curl_setopt($session, CURLOPT_GET, true);
	}

	protected function prepareDELETE($session) {
		curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'DELETE');
	}

	/**
	 * Execute GET request
	 * @param string $request URL REST request
	 * @return string $response Response from GET request
	 */
	protected function execute_rest_request_GET($request) {
		$session = $this->curlInit($request);
		$this->prepareGET($session);
		$response = curl_exec($session);
		return $response;
	}

	/**
	 * Function analyses status attribute of the response.
	 * For some statuses it throws exception (for example NoSuchElementException).
	 * @param string $json_response
	 */
	protected function handleResponse($json_response) {
		if (($status = $json_response->{'status'}) != self::SUCCESS) {
			if (array_key_exists($status, WebDriverException::$messages))
				throw new WebDriverException(WebDriverException::$messages[$status], $status, null);
			else
				throw new WebDriverException('Unknown error occured', $status, null);
		}
	}

	/**
	 * Search for an element on the page, starting from the document root.
	 * @param string $locatorStrategy
	 * @param string $value
	 * @return WebElement found element
	 */
	public function findElementBy($locatorStrategy, $value) {
		$request = $this->requestURL . "/element";
		$session = $this->curlInit($request);

		$args = array('using' => $locatorStrategy, 'value' => $value);
		$postargs = json_encode($args);
		$this->preparePOST($session, $postargs);
		$response = curl_exec($session);
		$json_response = json_decode(trim($response));
		if (!$json_response) {
			return null;
		}
		$this->handleResponse($json_response);
		$element = $json_response->{'value'};

		return new WebElement($this, $element, null);
	}

	/**
	 * Search for an element on the page, starting from the document root.
	 * @return WebElement found element
	 */
	public function findActiveElement() {
		$request = $this->requestURL . "/element/active";
		$session = $this->curlInit($request);
		$this->preparePOST($session, null);
		$response = curl_exec($session);
		$json_response = json_decode(trim($response));
		if (!$json_response) {
			return null;
		}
		$this->handleResponse($json_response);
		$element = $json_response->{'value'};

		return new WebElement($this, $element, null);
	}



	/**
	 * Search for multiple elements on the page, starting from the document root.
	 * @param string $locatorStrategy
	 * @param string $value
	 * @return array of WebElement
	 */
	public function findElementsBy($locatorStrategy, $value) {
		$session  = $this->curlInit($this->requestURL . "/elements");
		$postargs = json_encode(array('using' => $locatorStrategy, 'value' => $value));

		$this->preparePOST($session, $postargs);

		$json_response = json_decode(trim(curl_exec($session)));
		$elements      = $json_response->{'value'};

		$webelements   = array();
		foreach ($elements as $key => $element)
			$webelements[] = new WebElement($this, $element, null);

		return $webelements;
	}


	/**
	 * Function returns value of 'value' attribute in JSON string
	 * @example extractValueFromJsonResponse("{'name':'John', 'value':'123'}")=='123'
	 * @param string $json JSON string with value attrubute to extract
	 * @return string value of 'value' attribute
	 */
	public function extractValueFromJsonResponse($json) {
		$json = json_decode(trim($json));
		if ($json && isset($json->value))
			return $json->value;

		return null;
	}

}

?>