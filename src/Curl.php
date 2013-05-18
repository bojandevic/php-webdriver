<?php

class Curl
{
	protected $requestURL;
   protected $_curl;

   public function __construct($requestURL)
   {
      $this->requestURL = $requestURL;
   }

   protected function &curlInit($url)
   {
      if($this->_curl === null)
         $this->_curl = curl_init($url);
      else {
         curl_setopt($this->_curl, CURLOPT_HTTPGET, true);
         curl_setopt($this->_curl, CURLOPT_URL, $url);
      }

      curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array("application/json;charset=UTF-8"));
      curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($this->_curl, CURLOPT_HEADER, false);

      return $this->_curl;
   }

   protected function curlClose()
   {
      if( $this->_curl !== null ) {
         curl_close($this->_curl);
         $this->_curl = null;
      }
   }

   protected function preparePOST($session, $postargs)
   {
      curl_setopt($session, CURLOPT_POST, true);

      if ($postargs)
         curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
   }

   protected function prepareGET($session)
   {
      curl_setopt($session, CURLOPT_GET, true); // Not needed?
   }

   protected function prepareDELETE($session)
   {
      curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'DELETE');
   }

   /**
    * Execute POST request
    * @param string $request URL REST request
    * @param string $postargs POST data
    * @return string $response Response from POST request
    */
   protected function execute_rest_request_POST($request, $postargs)
   {
      $session = $this->curlInit($request);

      return trim(curl_exec($this->preparePOST($session, $postargs)));
   }

   /**
    * Execute GET request
    * @param string $request URL REST request
    * @return string $response Response from GET request
    */
   protected function execute_rest_request_GET($request)
   {
      $session = $this->curlInit($request);

      return curl_exec($this->prepareGET($session));
   }
}

?>