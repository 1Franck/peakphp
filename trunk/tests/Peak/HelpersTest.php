<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Helpers, Peak_Exception
 */
require_once 'Peak/Helpers.php';
require_once 'Peak/Exception.php';

/**
 * @category   Peak
 * @package    Peak_Helpers
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_HelpersTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{
		$this->peakhelpers = new myHelpers();
	}

	function testCreateHelpersClass()
	{		
		$helper = new myHelpers();		
		$this->assertType('Peak_Helpers', $helper);		
	}
	
	function testLoadHelpers()
	{		
		$this->peakhelpers->test;		
		$txt = $this->peakhelpers->test->getHello();	
		$this->assertTrue($txt === 'Hello');
		
		$this->peakhelpers->misc;		
		$txt = $this->peakhelpers->misc->getHello();	
		$this->assertTrue($txt === 'Hello');
	}
	
	/**
	 * @expectedException Peak_Exception
	 */
	function testRenderException()
	{
		//try to load an unknow helpers
		try {
			$this->peakhelpers->unknowhelper;
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
	}
		
	
  
}


/**
 * Class Helpers Examples
 */
class myhelpers extends Peak_Helpers
{
	
	public function __construct()
	{
		
		$this->_prefix    = array('MyHelper_', 'Helper_');
    	
    	$this->_paths     = array(TESTS_PATH.'/tmp//helpers');
    			                  
    	$this->_exception = 'ERR_CUSTOM';

	}
	
	
}