<?php

require_once "../src/WebDriver.php";

class Google extends WebDriver
{
	public function __construct()
	{
		parent::__construct("localhost", "4444");

		$this->googleSelenium();
	}

	public function googleSelenium()
	{
		$this->connect("firefox");
		$this->get("http://google.com");

		$element = $this->findElementBy(WebDriver::LOCATOR_NAME, "q");
		$element->sendKeys(array("webdriver" ) );
		$element->submit();
	}
}

?>