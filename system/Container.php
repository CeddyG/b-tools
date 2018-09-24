<?php

namespace System;

/**
 * Contains how to instanciate classes and already instanciated classes.
 *
 * @author Ceddy
 */
class Container
{
    private $aRegistry = [];
    private $aInstance = [];
    
    /**
     * Register how instanciate ($cResolver) a class ($sKey)
     * 
     * @param string $sKey
     * @param \System\callable $cResolver
     */
    public function set($sKey, Callable $cResolver)
    {
        $this->aRegistry[$sKey] = $cResolver;
    }
    
    /**
     * Return an instanciated class. Instanciate it if not yet.
     * 
     * @param string $sKey
     * 
     * @return object|$this
     */
    public function get($sKey)
    {
        if(!isset($this->aInstance[$sKey]))
        {
            if(isset($this->aRegistry[$sKey]))
            {
                $this->aInstance[$sKey] = $this->aRegistry[$sKey]($this);
            } 
            else 
            {
                return $this;
            }
        }
        return $this->aInstance[$sKey];
    }
}
