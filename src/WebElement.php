<?php

require_once 'WebDriverBase.php';

class WebElement extends WebDriverBase
{

   public function __construct($parent, $element, $options)
   {
      if (get_class($parent) == 'WebDriver')
         $root = $parent->requestURL;
      else
         $root = preg_replace("(/element/.*)", "", $parent->requestURL);

      parent::__construct($root . "/element/" . $element->ELEMENT);
   }

   public function sendKeys($value)
   {
      if (!is_array($value))
         throw new Exception("$value must be an array");

      $session = $this->curlInit($this->requestURL . "/value");

      $this->preparePOST($session, json_encode(array('value' => $value)));
      $response = trim(curl_exec($session));
   }

   public function getValue()
   {
      $response = $this->executeGET($this->requestURL . "/value");

      return $this->extractValueFromJsonResponse($response);
   }

   public function clear()
   {
      $session = $this->curlInit($this->requestURL . "/clear");

      $this->preparePOST($session, null);
      $response = trim(curl_exec($session));
   }

   public function click()
   {
      $session = $this->curlInit($this->requestURL . "/click");

      $this->preparePOST($session, null);
      $response = trim(curl_exec($session));
   }

   public function submit()
   {
      $session = $this->curlInit($this->requestURL . "/submit");

      $this->preparePOST($session, "");
      $response = trim(curl_exec($session));
   }

   public function getText()
   {
      $response = $this->executeGET($this->requestURL . "/text");

      return $this->extractValueFromJsonResponse($response);
   }

   public function getName()
   {
      $response = $this->executeGET($this->requestURL . "/name");

      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Get the value of a the given attribute of the element.
    */
   public function getAttribute($attribute)
   {
      $response = $this->executeGET($this->requestURL . '/attribute/' . $attribute);
      return $this->extractValueFromJsonResponse($response);
   }

   /**
    * Determine if an OPTION element, or an INPUT element of type checkbox or radiobutton is currently selected.
    * @return boolean Whether the element is selected.
    */
   public function isSelected()
   {
      $request = $this->requestURL . "/selected";
      $response = $this->executeGET($request);
      $isSelected = $this->extractValueFromJsonResponse($response);
      return ($isSelected == 'true');
   }

   /**
    * Select an OPTION element, or an INPUT element of type checkbox or radiobutton.
    *
    */
   public function setSelected()
   {
      $this->click(); //setSelected is now deprecated
   }


   /**
    * find OPTION by text in combobox
    *
    */
   public function findOptionElementByText($text)
   {
      return $this->findElementBy(WebDriver::LOCATOR_XPATH, 'option[normalize-space(text())="'.$text.'"]');
   }

   /**
    * find OPTION by value in combobox
    *
    */
   public function findOptionElementByValue($val)
   {
      return $this->findElementBy(WebDriver::LOCATOR_XPATH, 'option[@value="'.$val.'"]');
   }


   /**
    * Determine if an element is currently enabled
    * @return boolean Whether the element is enabled.
    */
   public function isEnabled()
   {
      $response = $this->executeGET($this->requestURL . "/enabled");

      return ($this->extractValueFromJsonResponse($response) == 'true');
   }


   /**
    * Determine if an element is currently displayed.
    * @return boolean Whether the element is displayed.
    */
   public function isDisplayed()
   {
      $response = $this->executeGET($this->requestURL . "/displayed");

      return ($this->extractValueFromJsonResponse($response) == 'true');
   }


   /**
    * Determine an element's size in pixels. The size will be returned as a JSON object with width and height properties.
    * @return width:number,height:number The width and height of the element, in pixels.
    */
   public function getSize()
   {
      $response = $this->executeGET($this->requestURL . "/size");

      return $this->extractValueFromJsonResponse($response);
   }


   /**
    * Query the value of an element's computed CSS property. The CSS property to query should be specified using
    * the CSS property name, not the JavaScript property name (e.g. background-color instead of backgroundColor).
    * @return string The value of the specified CSS property.
    */
   public function getCssProperty($propertyName)
   {
      $response = $this->executeGET($this->requestURL . "/css/". $propertyName);
      return $this->extractValueFromJsonResponse($response);
   }


   /**
    * Test if two element IDs refer to the same DOM element.
    * @return boolean Whether the two IDs refer to the same element.
    */
   public function isOtherId($otherId)
   {
      $response = $this->executeGET($this->requestURL . "/equals/".$otherId);

      return ($this->extractValueFromJsonResponse($response) == 'true');
   }


   /**
    * Determine an element's location on the page. The point (0, 0) refers to the upper-left corner of the page.
    * The element's coordinates are returned as a JSON object with x and y properties.
    * @return x:number, y:number The X and Y coordinates for the element on the page.
    */
   public function getLocation()
   {
      $response = $this->executeGET($this->requestURL . "/location");

      return $this->extractValueFromJsonResponse($response);
   }


   /**
    * Determine an element's location on the screen once it has been scrolled into view.
    * The element's coordinates are returned as a JSON object with x and y properties.
    * @return x:number, y:number The X and Y coordinates for the element.
    */
   public function getLocationInView()
   {
      $response = $this->executeGET($this->requestURL . "/location_in_view");

      return $this->extractValueFromJsonResponse($response);
   }
}

?>