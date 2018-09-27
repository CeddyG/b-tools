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
     * @param string $sType
     * @param string $sKey
     * @param \System\callable $cResolver
     * 
     * @return void
     */
    public function set($sType, $sKey, Callable $cResolver)
    {
        $this->aRegistry[$sKey] = [
            'resolver'  => $cResolver,
            'type'      => $sType
        ];
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
                if ($this->aRegistry[$sKey]['type'] == 'singleton')
                {
                    $this->aInstance[$sKey] = $this->aRegistry[$sKey]['resolver']($this);
                    return $this->aInstance[$sKey];
                }
                elseif ($this->aRegistry[$sKey]['type'] == 'factory')
                {
                    return $this->aRegistry[$sKey]['resolver']($this);
                }                   
            } 
            else 
            {
                return $this;
            }
        }
        
        return $this->aInstance[$sKey];
    }
}
