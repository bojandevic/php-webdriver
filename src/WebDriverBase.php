<?php

require_once 'Curl.php';
require_once 'WebDriverBase.php';
require_once 'WebElement.php';
require_once 'WebDriverException.php';

class WebDriverBase extends Curl
{
   const SUCCESS = 0;

   protected $requestURL;
   protected $_curl;

   public function __construct($requestURL)
   {
      parent::__construct($requestURL);
   }

   /**
    * Function returns value of 'value' attribute in JSON string
    * @example extractValueFromJsonResponse("{'name':'John', 'value':'123'}")=='123'
    * @param string $json JSON string with value attrubute to extract
    * @return string value of 'value' attribute
    */
   public function extractValueFromJsonResponse($json)
   {
      $json = json_decode(trim($json));
      if ($json && isset($json->value))
         return $json->value;

      return null;
   }

   /**
    * Function analyses status attribute of the response.
    * For some statuses it throws exception (for example NoSuchElementException).
    * @param string $json_response
    */
   protected function handleResponse($json_response)
   {
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
   public function findElementBy($locatorStrategy, $value)
   {
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
   public function findActiveElement()
   {
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
   public function findElementsBy($locatorStrategy, $value)
   {
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


}

?>