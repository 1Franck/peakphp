<?php
/**
 * Peak Modules Application Abstract Launcher 
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_Application_Modules
{

	/**
	 * @deprecated
	 */
	protected $_ctrl_name = '';
	
	/**
	 * Module name
	 * @var string
	 */
    protected $_name = '';
    
    /**
     * Module path
     * @var string
     */
    protected $_path = '';
    
    /**
     * Module status
     * @var bool
     */
    protected $_internal = false;
        
    /**
     * Get the name of child class and use it as the module name
     * Prepare core to run a module
     * init module bootstrap and front controller if exists
     * 
     * @uses Peak_Core_Extension_Modules
     */
    public function __construct()
    {      	
    	//prepare module
    	$this->prepare();
              
        //initialize module bootstrap
        if(file_exists(Peak_Core::getPath('application').'/bootstrap.php')) {
        	include Peak_Core::getPath('application').'/bootstrap.php';
        }
        $bootstrap_class = $this->_name.'_Bootstrap';
        if(class_exists($bootstrap_class)) {
        	Peak_Registry::o()->app->bootstrap = new $bootstrap_class();
        }
        
        //initialize module front
        if(file_exists(Peak_Core::getPath('application').'/front.php')) {
        	include Peak_Core::getPath('application').'/front.php';
        }
        $front_class = $this->_name.'_Front';
        if(class_exists($front_class)) {
        	Peak_Registry::o()->app->front = new $front_class();
        }
    }
    
    /**
     * Prepare modules app and init Modules core extension
     */
    protected function prepare()
    {
    	if(!($this->_internal))	{
    		//ctrl name
    		$this->_ctrl_name = str_ireplace('controller','',get_class($this));
    		$this->_path = Peak_Core::getPath('modules').'/'.$this->_ctrl_name;
    	}
    	else {
    		//ctrl name
    		$this->_ctrl_name = str_ireplace('Peak_Controller_Internal_','',get_class($this));
    		$this->_path = LIBRARY_ABSPATH.'/Peak/Application/'.$this->_ctrl_name;
    	}
    	//module name
    	if(empty($this->_name)) $this->_name = $this->_ctrl_name;

    	//overdrive application paths to modules folder with Peak_Core_Extension_Modules
    	Peak_Registry::o()->core->modules()->init($this->_name, $this->_path);
    }
      
    
    /**
     * Run modules requested controller.
     *
     * @param string $default_ctrl
     */
    public function run($default_ctrl = 'index')
    {      	
        //add module name to the end Peak_Router $base_uri        
        Peak_Registry::o()->router->base_uri = Peak_Registry::o()->router->base_uri.$this->_name;

        //re-call Peak_Application run() for handling the new routing
        Peak_Registry::o()->app->run($default_ctrl);
    }
    
    /**
     * Return the module name
     * 
     * @return string
     */
    public function getName()
    {
    	return $this->_name;
    }
    
    /**
     * Return module path
     *
     * @return string
     */
    public function getPath()
    {
    	return $this->_path;
    }
    
    /**
     * Return module location relative to the application
     * Will return true if the module is inside Peak library folder 
     * 
     * @return bool
     */
    public function isInternal()
    {
    	return $this->_internal;    	
    }
        
}