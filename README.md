Introduction
============
<i>This is a fork of [PHP WebDriver bindings](https://code.google.com/p/php-webdriver-bindings/)</i><br />
This is site for developers of PHP bindings for Selenium WebDriver. This PHP library allows creating functional webdriver tests with PHP.

Details
-------
Library comunicates with Selenium Server using [JsonWireProtocol](https://code.google.com/p/selenium/wiki/JsonWireProtocol). Requires curl in PHP. List of implemented methods: implemented_methods.

Example
-------
<pre>
<code>
   require_once "WebDriver.php";
   require("LocatorStrategy.php");

   $webdriver = new WebDriver("localhost", "4444");
   $webdriver->connect("firefox");                            
   $webdriver->get("http://google.com");
   $element = $webdriver->findElementBy(LocatorStrategy::name, "q");
   $element->sendKeys(array("selenium google code" ) );
   $element->submit();

   $webdriver->close();
</code>
</pre>
###Combobox handling###
<pre>
<code>
   $this->webdriver->get($this->test_url);
   $element = $this->webdriver->findElementBy(LocatorStrategy::name, "sel1");
   $option3 = $element->findOptionElementByText("option 3");
   $option3->click();
   $this->assertTrue($option3->isSelected());

   $option2 = $element->findOptionElementByValue("2");
   $option2->click();
   $this->assertFalse($option3->isSelected());
   $this->assertTrue($option2->isSelected());
</code>
</pre>