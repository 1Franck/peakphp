<?php
/**
 * Peak_Controller_Front
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Controller_Front
{

	/**
	 * Controller object
	 * @var object
	 */
	public $controller;

	/**
	 * Default controller name
	 * @var string
	 */
	public $default_controller = 'indexController';
	
	/**
	 * Allow/Disallow the use of Peak library internal controllers
	 * @var bool
	 */
	public $allow_internal_controller = false;
	
	
	/**
	 * class construct
	 */
	public function __construct()
	{
		$this->_registryConfig();
	}
	
	/**
     * Get array 'front' from registered object 'config' if exists
     */
    private function _registryConfig()
    {
    	if(isset(Peak_Registry::o()->config->front)) {
    		foreach(Peak_Registry::o()->config->front as $k => $v) $this->$k = $v;
    	}
    }

	/**
	 * Initialize router uri request
	 */
	public function getRoute()
	{
		Peak_Registry::o()->router->getRequestURI();
	}

	/**
	 * Initialize router uri request
	 */
	public function preDispatch()
	{
		//Peak_Registry::o()->router->getRequestURI();
	}

	/**
	 * Start dispatching to controller with the help of router
	 * 
	 * @param string $default_ctrl Controller called by default when no request
	 */
	public function dispatch()
	{
		$router = Peak_Registry::o()->router;       

        //if(isset($default_ctrl)) $this->default_controller = $default_ctrl.'Controller';

        //try to load controller from router if exists       
        if(isset($router->controller))
        {
        	$ctrl_name = $router->controller.'Controller';
     
        	//check if controller is not found in current core controllers
        	if(!$this->isController($ctrl_name))
        	{
        		//check for peak internal controller
        		if(($this->allow_internal_controller === true) && ($this->isInternalController($router->controller))) {
        			$ctrl_name = 'Peak_Controller_Internal_'.$router->controller;
        			$this->controller = new $ctrl_name();
        		}
        		else throw new Peak_Exception('ERR_APP_CTRL_NOT_FOUND', $ctrl_name);
        	}
        	else $this->controller = new $ctrl_name();
        }
        //if no router controller, try to load default controller
        elseif((isset($this->default_controller)) && ($this->isController($this->default_controller)))
        {
        	$default_ctrl = $this->default_controller;
        	$this->controller = new $default_ctrl();
        }
        else throw new Peak_Exception('ERR_APP_CTRL_NOT_FOUND',$default_ctrl);
        
        //if class if is an instance of Peak_Application_Modules, load a module app via a controller
        if($this->controller instanceof Peak_Application_Modules) 
        {
        	Peak_Registry::o()->app->module = $this->controller;
        	$this->controller->run();
        }               
        // execute a normal controller action
        elseif(method_exists($this->controller,'handleAction')) {
        	$this->controller->handleAction();
        	$this->postDispatch();  
        }       
	}
	
	/**
	 * Force dispatching to a specific controller/action
	 *
	 * @param string $ctrl
	 * @param string $action
	 */
	public function forceDispatch($controller, $action = 'index')
	{
		$router = Peak_Registry::o()->router;
		$router->controller = $controller;
		$router->action = $action;
		$this->dispatch();
	}

    /**
     * Set a new request and redispath the controller
     *
     * @param string     $ctrl
     * @param string     $action
     * @param array/null $params
     */
    public function redirect($ctrl, $action = 'index', $params = null)
    {
    	Peak_Registry::o()->router->setRequest( array($ctrl, $action, $params) );
    	$this->dispatch();
    }

    /**
	 * Called after controller action dispatching
	 */
    public function postDispatch() { }
    
    
    /**
     * Check if controller filename exists
     *
     * @param  string $name
     * @return bool
     */
    public function isController($name)
    {
    	return (file_exists(Peak_Core::getPath('controllers').'/'.$name.'.php')) ? true : false; 
    }

    /**
     * Check if internal Peak Controller filename exists
     *
     * @param  string $name
     * @return bool
     */
    public function isInternalController($name)
    {
    	return (file_exists(LIBRARY_ABSPATH.'/Peak/Controller/Internal/'.$name.'.php')) ? true : false;
    }

    /**
     * Check if modules dirname exists
     *
     * @param  string $name
     * @return bool
     */
    public function isModule($name)
    {
    	return (file_exists(Peak_Core::getPath('modules').'/'.$name)) ? true : false;
    }
}