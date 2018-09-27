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
    /**
     * PDO Instance.
     * 
     * @var PDO
     */
    private $oPdo;
    
    /**
     * We'll cache query to not instanciate another PDOStatement.
     * 
     * @var type 
     */
    private $aCache = [];
    
    public function __construct(array $aConfig)
    {
        try
		{
			$this->oPdo = new PDO(
                'mysql:host='.$aConfig['host'].';dbname='.$aConfig['database'].'', 
                $aConfig['user'], 
                $aConfig['password'],
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
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
        $oRequest = $this->buildRequest($sQuery);
        
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
    
    /**
     * Prepare a query or take one from the cache.
     * 
     * @param string $sQuery
     * 
     * @return PDOStatement $oRequest
     */
    private function buildRequest($sQuery)
    {
        $iKeyCache = array_search($sQuery, array_column($this->aCache, 'query'));
        
        if ($iKeyCache === false)
        {
            $oRequest = $this->oPdo->prepare($sQuery);
            
            $this->aCache[] = [
                'object'    => $oRequest,
                'query'     => $sQuery
            ];
        }
        else
        {
            $oRequest = $this->aCache[$iKeyCache]['object'];
        }
        
        return $oRequest;
    }
}
