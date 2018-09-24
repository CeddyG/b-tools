<?php

namespace System;

/**
 * Main class to register how classes have to be instanciate.
 *
 * @author Ceddy
 */
abstract class Provider
{
    protected $oApp = null;
    
    protected $oContainer = null;
    
    public function __construct(Application $oApp)
    {
        $this->oApp         = $oApp;
        $this->oContainer   = $oApp->getContainer();
    }
    
    abstract public function register();
}
