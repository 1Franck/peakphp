<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Helpers, Peak_Exception
 */
require_once 'Peak/Zreflection.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/ZreflectionTest/class1.php';
require_once dirname(__FILE__).'/ZreflectionTest/class2.php';
/**
 * Include path
 */
set_include_path(implode(PATH_SEPARATOR,array(realpath(dirname(__FILE__).'/../../library/Peak/Vendors'),
						                      get_include_path())));

/**
 * Autoload for Zend
 */
spl_autoload_register('_autoloadZendVendor');

function _autoloadZendVendor($cn) {
    $file = realpath(dirname(__FILE__).'/../../library/Peak/Vendors').'/'.str_replace('_','/',$cn).'.php';
    if(!file_exists($file)) return false;
    include $file;
}

/**
 * @category   Peak
 * @package    Peak_Zreflection
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ZreflectionTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{
		$this->zref = new Peak_Zreflection();
	}

	function testLoadClass()
	{
		$this->zref->loadClass('class1', false);
		$this->assertTrue($this->zref->class instanceof Zend_Reflection_Class);
		$this->assertTrue($this->zref->class->getName() === 'class1');
	}
	
	function testgetClassDoc()
	{
		$this->zref->loadClass('class1', false);
		$this->assertTrue($this->zref->getClassDoc() === 'Example Class1');
		$this->assertTrue($this->zref->getClassDoc('short') === 'Example Class1');
		$long_desc = trim($this->zref->getClassDoc('long'));
		//file_put_contents('test.txt',(string)$long_desc);
		
		//$this->assertTrue(trim($this->zref->getClassDoc('long')) === 'Long description
	}

    function testgetClassDocTags()
	{
		$this->zref->loadClass('class1', false);
		$doctags = $this->zref->getClassDocTags();
		
		$this->assertTrue(is_array($doctags));
	    $this->assertTrue(count($doctags) == 2);

		$this->assertTrue($doctags[0]['name'] === 'author');
		$this->assertTrue($doctags[0]['description'] === 'FooBar');
	}
	
	function testgetClassDeclaration()
	{
		$this->zref->loadClass('class1', false);
		$declaration = $this->zref->getClassDeclaration();

		$this->assertTrue(is_array($declaration));
		$this->assertTrue(count($declaration) == 2);
		$this->assertArrayHasKey('properties', $declaration);
		$this->assertTrue(count($declaration['properties']) == 3);
		$this->assertArrayHasKey('interfaces', $declaration);
		$this->assertTrue(count($declaration['interfaces']) == 1);
		
		$this->zref->loadClass('class2', false);
		$declaration = $this->zref->getClassDeclaration();
		
		$this->assertTrue(count($declaration['properties']) == 2);
		$this->assertTrue(count($declaration['interfaces']) == 1);
	}
	
	function testgetConstants()
	{
		$this->zref->loadClass('class1', false);
		
		$constants = $this->zref->getConstants();
		
		$this->assertNotEmpty($constants);
		$this->assertTrue(count($constants) == 1);
	}
	
	function testgetMethods()
	{
		$this->zref->loadClass('class1', false);
		
		$methods =  $this->zref->getMethods();
		$this->assertNotEmpty($methods);
		$this->assertTrue(count($methods) == 1);
	}

    function testgetMethodsByInheritance()
	{
		$this->zref->loadClass('class1', false);
		
		$methods =  $this->zref->getMethodsByInheritance();
		$this->assertNotEmpty($methods);
		$this->assertTrue(count($methods) == 2);
		$this->assertArrayHasKey('self', $methods);
		$this->assertArrayHasKey('parent', $methods);
		$this->assertTrue(empty($methods['parent']));
		$this->assertTrue(count($methods['self']) == 1);
	}
	
	function testgetSelfMethods()
	{
		$this->zref->loadClass('class1', false);
		
		$methods =  $this->zref->getSelfMethods();
		
		$this->assertNotEmpty($methods);
		$this->assertTrue(count($methods) == 1);
	}
	
	function testgetParentMethods()
	{
		$this->zref->loadClass('class1', false);
		
		$methods =  $this->zref->getParentMethods();
		
		$this->assertEmpty($methods);
	}
	
	function testgetMethodClassname()
	{
		$this->zref->loadClass('class2', false);
		
		$classname = $this->zref->getMethodClassname('count');
		$this->assertTrue($classname === 'class1');
		
		$classname = $this->zref->getMethodClassname('setName');
		$this->assertTrue($classname === 'class2');
	}
	
	function testgetMethodVisibility()
	{
		$this->zref->loadClass('class2', false);
		
		$visiblity = $this->zref->getMethodVisibility('count');
		$this->assertTrue($visiblity === 'public');
		
		$visiblity = $this->zref->getMethodVisibility('_sanitizeName');
		$this->assertTrue($visiblity === 'protected');
	}
	
	function testgetMethodDoc()
	{
		$this->zref->loadClass('class1', false);
		
		$doc = $this->zref->getMethodDoc('count');
		$this->assertTrue($doc === 'Return the count of $_misc_data');
		
		$this->zref->loadClass('class2', false);
		
		$doc = $this->zref->getMethodDoc('setName', 'long');
		$this->assertTrue($doc === 'Long description text...');
	}
}