<?php

namespace System;

use PDO;

/**
 * Connection of the database using PDO.
 *
 * @author Ceddy
 */
class Database
{
    private $oPdo;
    
    public function __construct(array $aConfig)
    {
        try
		{
			$this->oPdo = new PDO(
                'mysql:host='.$aConfig['host'].';dbname='.$aConfig['database'].'', 
                $aConfig['user'], 
                $aConfig['password']
            );
			$this->oPdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
		}
		catch (Exception $e)
		{
				die('Erreur : ' . $e->getMessage());
		}
    }
    
    /**
     * Execute a query.
     * 
     * @param string $sType
     * @param string $sQuery
     * @param array $aValue
     * 
     * @return mixed
     */
    public function exec($sType, $sQuery, $aValue = [])
    {
        $oRequest = $this->oPdo->prepare($sQuery);
        
        if ($sType == 'select')
        {
            $oRequest->execute($aValue);
            $oResponse = $oRequest->fetchAll(PDO::FETCH_OBJ);
            $oRequest->closeCursor();

            return $oResponse;
        }
        else
        {
            return $oRequest->execute($aValue);
        }
    }
}
