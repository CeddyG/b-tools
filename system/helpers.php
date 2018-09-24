<?php

use System\Application;

if (!function_exists('app')) 
{
    function app($sClass)
    {
        return Application::getInstance()->getContainer()->get($sClass);
    }
}

if (!function_exists('config')) 
{
    function config($sKey, $mDefault = null)
    {
        return Application::getInstance()->getConfig($sKey, $mDefault);
    }
}

if (!function_exists('dump')) 
{
    function dump($mVariable, $bDie = false)
    {
        echo '<pre>';
        var_dump($mVariable);
        echo '</pre>';
        
        if ($bDie)
        {
            die();
        }
    }
}

if (!function_exists('dd')) 
{
    function dd($mVariable)
    {
        dump($mVariable, true);
    }
}