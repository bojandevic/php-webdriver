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
require_once "phpwebdriver/WebDriver.php";
require("phpwebdriver/LocatorStrategy.php");

$webdriver = new WebDriver("localhost", "4444");
$webdriver->connect("firefox");                            
$webdriver->get("http://google.com");
$element = $webdriver->findElementBy(LocatorStrategy::name, "q");
$element->sendKeys(array("selenium google code" ) );
$element->submit();

$webdriver->close();
</code>
</pre>
###combobox handling###
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
###Use your existing Selenium1 tests (also generated with Selenium IDE)###
Use CWebDriverTestCase.php class which provides interface like classic selenium test class:
<pre>
<code>
class WebTestCase extends CWebDriverTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setBrowserUrl('http://yourapp123.com');
    }

        /** generate screenshot if any test has failed */
    protected function tearDown()
    {
        if( $this->hasFailed() ) {
            $date = "screenshot_" . date('Y-m-d-H-i-s') . ".png" ;
            $this->webdriver->getScreenshotAndSaveToFile( $date );
        }
        $this->close();
    }

    protected function testSomething( )
    {
        $this->open( "/index-test.php/user/login", "expect-div-with-this-id-after-load-page" );
        $this->type( "LoginForm_username", "your_login" );
        $this->type( "LoginForm_password", "your_pass" );
        $this->click( "login-button" );
                
        // getElement will try few times to find element 
        $this->getElement( LocatorStrategy::id, 'top-user-data' );
                
        $this->assertTrue( $this->isTextPresent( "Logged as: your_pass" ) );
    }
        //...
</code>
</pre>
