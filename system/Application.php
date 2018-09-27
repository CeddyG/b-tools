<?php

namespace System;

/**
 * Main class containing the kernel of the application.
 *
 * @author Ceddy
 */
class Application
{
    /**
     * Self instance.
     * 
     * @var \System\Application
     */
    private static $instance = null;
    
    /**
     * The root dir of the application.
     * 
     * @var string 
     */
    private $sRootDir = '';
    
    /**
     * Instance of the container.
     * 
     * @var \System\Container
     */
    private $oContainer = null;
    
    /**
     * Contains the config file (config.php).
     * 
     * @var array
     */
    private $aConfig = [];

    private function __construct()
    {
        
    }
    
    /*
     * Setter and Getter 
     */
    
    private function setConfig()
    {
        $this->aConfig = include_once $this->sRootDir.'/config.php';
    }
    
    public function getContainer()
    {
        return $this->oContainer;
    }
    
    public function getConfig($sKey, $mDefault = null)
    {
        if (isset($this->aConfig[$sKey]))
        {
            return $this->aConfig[$sKey];
        }
        else
        {
            return $mDefault;
        }
    }
    
    public static function getInstance() 
    {
        if (is_null(self::$instance)) 
        {
            self::$instance = new Application();
        }
        
        return self::$instance;
    }
    
    /**
     * Set up the application.
     * 
     * @return $this
     */
    public function init()
    {
        session_start();
        
        $this->sRootDir     = realpath(__DIR__.'/../').'/';
        $this->oContainer   = new Container();
        
        $this->setConfig();
        $this->registerProviders();
        
        return $this;
    }
    
    /**
     * Instanciate all providers.
     */
    private function registerProviders()
    {
        //Get all providers files
        $aFiles = scandir($this->sRootDir.'system/Providers');
        
        //We include all of them
        foreach ($aFiles as $sFile)
        {
            if (strpos($sFile, '.php') !== false)
            {
                include_once $this->sRootDir.'system/Providers/'.$sFile;
            }
        }
        
        $aClasses = get_declared_classes();
        
        //We set all providers in the container and fire their register function
        foreach ($aClasses as $sClass)
        {
            if (strpos($sClass, 'System\Providers') !== false)
            {
                $this->oContainer->set('singleton', $sClass, function ($oContainer) use ($sClass){
                    return new $sClass($this);
                });
                
                $this->oContainer->get($sClass)->register();
            }
        }
    }
}
