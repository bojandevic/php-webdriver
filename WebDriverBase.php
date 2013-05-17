<?php

/*
  Copyright 2011 3e software house & interactive agency

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
 */

require_once 'WebElement.php';
require_once 'WebDriverException.php';
require_once 'NoSuchElementException.php';

class WebDriverBase {

	protected $requestURL;
	protected $_curl;

	public function __construct($_seleniumUrl) {
		$this->requestURL = $_seleniumUrl;
	}

	protected function &curlInit( $url ) {
		if( $this->_curl === null ) {
			$this->_curl = curl_init( $url );
		} else {
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
	 * Function checks if there was error in last command excecution.
	 * If there was an error - new Exception is thrown.
	 * @param Curl_session $session
	 */
	protected function handleError($session, $response) {
		$last_error = curl_errno($session);
		print_r('last_error = ' . $last_error);
		if ($last_error == 500) { // selenium error
			print_r($response);
			throw new WebDriverException($message, $code, $previous);
		} else
		if ($last_error != 0) { // unknown error
			print_r($response);
			throw new WebDriverException($message, $code, $previous);
		}
	}

	/**
	 * Function analyses status attribute of the response.
	 * For some statuses it throws exception (for example NoSuchElementException).
	 * @param string $json_response
	 */
	protected function handleResponse($json_response) {
		$status = $json_response->{'status'};
		switch ($status) {
			case WebDriverException::SUCCESS:
				return;
			break;
			case WebDriverException::NO_SUCH_ELEMENT:
				throw new NoSuchElementException($json_response);
			break;
			default:
				print_r($json_response);
				throw new WebDriverException($status, 99, null);
			break;
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
		$request = $this->requestURL . "/elements";
		$session = $this->curlInit($request);
		//$postargs = "{'using':'" . $locatorStrategy . "', 'value':'" . $value . "'}";
		$args = array('using' => $locatorStrategy, 'value' => $value);
		$postargs = json_encode($args);
		$this->preparePOST($session, $postargs);
		$response = trim(curl_exec($session));
		$json_response = json_decode($response);
		$elements = $json_response->{'value'};
		$webelements = array();
		foreach ($elements as $key => $element) {
			$webelements[] = new WebElement($this, $element, null);
		}
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
		if ($json && isset($json->value)) {
			return $json->value;
		}
		return null;
	}

}

?>