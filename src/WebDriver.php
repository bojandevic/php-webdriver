<?php

require_once 'WebDriverBase.php';

class WebDriver extends WebDriverBase
{
   const LOCATOR_CLASS_NAME        = "class name";
   const LOCATOR_CSS               = "css selector";
   const LOCATOR_ID                = "id";
   const LOCATOR_NAME              = "name";
   const LOCATOR_LINK_TEXT         = "link text";
   const LOCATOR_PARTIAL_LINK_TEXT = "partial link text";
   const LOCATOR_TAG_NAME          = "tag name";
   const LOCATOR_XPATH             = "xpath";

   public function __construct($host, $port) {
      parent::__construct("http://{$host}:{$port}/wd/hub");
   }

   /**
    * Connects to Selenium server.
    * @param $browserName The name of the browser being used; should be one of {chrome|firefox|htmlunit|internet explorer|iphone}.
    * @param $version   The browser version, or the empty string if unknown.
    * @param $caps  array with capabilities see: http://code.google.com/p/selenium/wiki/JsonWireProtocol#/session
    */
   public function connect($browserName = "firefox", $version = "", $caps = array()) {
      $session = $this->curlInit($this->requestURL . "/session");

      $allCaps = array_merge(array('javascriptEnabled' => true,
                                   'nativeEvents'      => false,
                                   'browserName'       => $browserName
                                   'version'           => $version),
                             $caps);

      $this->preparePOST($session, json_encode(array('desiredCapabilities' => $allCaps)));
      curl_setopt($session, CURLOPT_HEADER, true);
      curl_exec($session);

      $header   = curl_getinfo($session);

      $this->requestURL = $header['url'];
   }

   /**
    * Delete the session.
    */
   public function close()
   {
      $session = $this->curlInit($this->requestURL);

      $this->prepareDELETE($session);
      $response = curl_exec($session);
      $this->curlClose();
   }

    /**
    * Refresh the current page.
    */
   public function refresh()
   {
      $session = $this->curlInit($this->requestURL . "/refresh");

      $this->preparePOST($session, null);
      curl_exec($session);
   }

   /**
    * Navigate forwards in the browser history, if possible.
    */
   public function forward()
   {
      $session = $this->curlInit($this->requestURL . "/forward");

      $this->preparePOST($session, null);
      curl_exec($session);
   }

   /**
    * Navigate backwards in the browser history, if possible.
    */
   public function back()
   {
      $session = $this->curlInit($this->requestURL . "/back");

      $this->preparePOST($session, null);
      curl_exec($session);
   }

   /**
    * Get the element on the page that currently has focus.
    * @return JSON object WebElement.
    */
   public function getActiveElement()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/element/active");
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Change focus to another frame on the page. If the frame ID is null, the server should switch to the page's default content.
    */
   public function focusFrame($frameId)
   {
      $session = $this->curlInit($this->requestURL . "/frame");
      $args    = array('id' => $frameId);

      $this->preparePOST($session, json_encode($args));
      curl_exec($session);
   }

   /**
    * Navigate to a new URL
    * @param string $url The URL to navigate to.
    */
   public function get($url)
   {
      $session = $this->curlInit($this->requestURL . "/url");
      $args    = array('url' => $url);

      $this->preparePOST($session, json_encode($args));
      $response = curl_exec($session);
   }

   /**
    * Get the current page url.
    * @return string The current URL.
    */
   public function getCurrentUrl()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/url");
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Get the current page title.
    * @return string current page title
    */
   public function getTitle()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/title");
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Get the current page source.
    * @return string page source
    */
   public function getPageSource()
   {
      $request  = $this->requestURL . "/source";
      $response = $this->execute_rest_request_GET($request);
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Get the current user input speed. The server should return one of {SLOW|MEDIUM|FAST}.
    * How these constants map to actual input speed is still browser specific and not covered by the wire protocol.
    * @return string {SLOW|MEDIUM|FAST}
    */
   public function getSpeed()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/speed");
      return $this->extractValueFromJsonResponse($response);
   }

   public function setSpeed($speed)
   {
      $session  = $this->curlInit($this->requestURL . "/speed");
      $jsonData = json_encode(array('speed' => $speed));

      $response = curl_exec($this->preparePOST($session, $jsonData));
      return $this->extractValueFromJsonResponse($response);
   }


   /**
    * Change focus to another window. The window to change focus to may be specified
    * by its server assigned window handle, or by the value of its name attribute.
    */
   public function selectWindow($windowName)
   {
      $session  = $this->curlInit($this->requestURL . "/window");
      $jsonData = json_encode(array('name' => $windowName));

      $response = curl_exec($this->preparePOST($session, $jsonData));
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Close the current window.
    */
   public function closeWindow()
   {
      $request = $this->requestURL . "/window";
      $session = $this->curlInit($request);

      $response = curl_exec($this->prepareDELETE($session));
      $this->curlClose();
   }

   /**
    * Retrieve all cookies visible to the current page.
    * @return array array with all cookies
    */
   public function getAllCookies()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/cookie");
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Set a cookie.
    */
   public function setCookie($name, $value, $cookie_path = '/', $domain = '', $secure = false, $expiry = '')
   {
      $session = $this->curlInit($this->requestURL . "/cookie");
      $cookie  = array('name'   => $name,
                       'value'  => $value,
                       'secure' => $secure);

      if (!empty($cookie_path))
         $cookie['path']=$cookie_path;

      if (!empty($domain))
         $cookie['domain']=$domain;

      if (!empty($expiry))
         $cookie['expiry']=$expiry;

      $jsonData = json_encode(array('cookie' => $cookie));
      $response = curl_exec($this->preparePOST($session, $jsonData));

      return $this->extractValueFromJsonResponse($response);
   }


   /**
    *Delete the cookie with the given name. This command should be a no-op if there is no such cookie visible to the current page.
    */
   public function deleteCookie($name)
   {
      $session  = $this->curlInit($this->requestURL . "/cookie/" . $name);
      $response = curl_exec($this->prepareDELETE($session));

      $this->curlClose();
   }

   /**
    * Delete all cookies visible to the current page.
    */
   public function deleteAllCookies()
   {
      $session  = $this->curlInit($this->requestURL . "/cookie");
      $response = curl_exec($this->prepareDELETE($session));

      $this->curlClose();
   }


   /**
    * Gets the text of the currently displayed JavaScript alert(), confirm(), or prompt() dialog.
    * @return string The text of the currently displayed alert.
    */
   public function getAlertText()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/alert_text");
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Sends keystrokes to a JavaScript prompt() dialog.
    */
   public function sendAlertText($text)
   {
      $session  = $this->curlInit($this->requestURL . "/alert_text");
      $jsonData = json_encode(array('keysToSend' => $text));

      $response = curl_exec($this->preparePOST($session, $jsonData));
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Get the current browser orientation. The server should return a valid orientation value as defined in ScreenOrientation: LANDSCAPE|PORTRAIT.
    * @return string The current browser orientation corresponding to a value defined in ScreenOrientation: LANDSCAPE|PORTRAIT.
    */
   public function getOrientation()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/orientation");
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Set the browser orientation. The orientation should be specified as defined in ScreenOrientation: LANDSCAPE|PORTRAIT.
    */
   public function setOrientation($orientation)
   {
      $session  = $this->curlInit($this->requestURL . "/orientation");
      $jsonData = json_encode(array('orientation' => $orientation));

      $this->preparePOST($session, $jsonData);
      curl_exec($session);
   }

   /**
    * Accepts the currently displayed alert dialog. Usually, this is equivalent to clicking on the 'OK' button in the dialog.
    */
   public function acceptAlert()
   {
      $session = $this->curlInit($this->requestURL . "/accept_alert");

      $response = curl_exec($this->preparePOST($session, ''));
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Dismisses the currently displayed alert dialog. For confirm() and prompt() dialogs,
    * this is equivalent to clicking the 'Cancel' button. For alert() dialogs, this is equivalent to clicking the 'OK' button.
   */
   public function dismissAlert()
   {
      $session = $this->curlInit($this->requestURL . "/dismiss_alert");

      $response = curl_exec($this->preparePOST($session, ''));
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Inject a snippet of JavaScript into the page for execution in the context of the currently selected frame.
    * The executed script is assumed to be synchronous and the result of evaluating the script
    * is returned to the client.
    * @return Object result of evaluating the script is returned to the client.
    */
   public function execute($script, $scripArgs)
   {
      $session = $this->curlInit($this->requestURL . "/execute");
      $jsonData = json_encode(array('script' => $script, 'args' => $scripArgs));

      $response = curl_exec($this->preparePOST($session, $jsonData));
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Inject a snippet of JavaScript into the page for execution in the context of the currently selected frame.
    * The executed script is assumed to be synchronous and the result of evaluating the script
    * is returned to the client.
    * @return Object result of evaluating the script is returned to the client.
    */
   public function executeScript($script, $scripArgs)
   {
      $session  = $this->curlInit($this->requestURL . "/execute");
      $jsonData = json_encode(array('script' => $script, 'args' => $scripArgs));

      $response = curl_exec($this->preparePOST($session, $jsonData));
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Inject a snippet of JavaScript into the page for execution
    * in the context of the currently selected frame. The executed script
    * is assumed to be asynchronous and must signal that is done by invoking
    * the provided callback, which is always provided as the final argument
    * to the function. The value to this callback will be returned to the client.
    * @return Object result of evaluating the script is returned to the client.
    */
   public function executeAsyncScript($script, $scripArgs)
   {
      $session  = $this->curlInit($this->requestURL . "/execute_async");
      $jsonData = json_encode(array('script' => $script, 'args' => $scripArgs));

      $response = curl_exec($this->preparePOST($session, $jsonData));

      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Take a screenshot of the current page.
    * @return string The screenshot as a base64 encoded PNG.
    */
   public function getScreenshot()
   {
      $response = $this->execute_rest_request_GET($this->requestURL . "/screenshot";);
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Take a screenshot of the current page and saves it to png file.
    * @param $pngFileName filename (with path) where file has to be saved
    * @return bool result of operation (false if failure)
    */
   public function getScreenshotAndSaveToFile($pngFileName)
   {
      $data = base64_decode($this->getScreenshot());

      return file_put_contents($pngFileName, $data);
   }
}

?>