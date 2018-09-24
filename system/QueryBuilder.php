<?php

namespace System;

/**
 * Build and execute a query with some parameters.
 *
 * @author Ceddy
 */
class QueryBuilder
{
    /**
     * Database instance.
     * 
     * @var \System\Database
     */
    protected $oDatabase = null;
    
    /**
     * The current table.
     * 
     * @var string
     */
    protected $sTable = '';
    
    /**
     * List of columns we want to get.
     * 
     * @var array
     */
    protected $aColumns = [];
    
    /**
     * List of where clauses.
     * 
     * @var array
     */
    protected $aWhere = [];
    
    /**
     * List of values to bind.
     * 
     * @var array
     */
    protected $aBind = [];

    public function __construct(Database $oDatabase)
    {
        $this->oDatabase = $oDatabase;
    }
    
    /**
     * Set the table.
     * 
     * @param string $sTable
     * 
     * @return $this
     */
    public function table($sTable)
    {
        $this->sTable = $sTable;
        
        return $this;
    }
    
    /**
     * Set the columns we want to get.
     * 
     * @param array $aColumns
     * 
     * @return $this
     */
    public function select($aColumns = ['*'])
    {
        $this->aColumns = is_array($aColumns) ? $aColumns : func_get_args();

        return $this;
    }
    
    /**
     * Build the where clauses.
     * 
     * @param array $aWhere
     * @param string $sBoolean ('and' | 'or')
     * 
     * @return $this
     */
    public function where(array $aWhere, $sBoolean = 'and')
    {
        foreach($aWhere as $mKey => $mCondition)
        {
            if(!is_array($mCondition))
            {
                $this->aWhere[$sBoolean][] = $mKey.' = ?';
                $this->aBind[] = $mCondition;
            }
            else
            {
                $this->aWhere[$sBoolean][] = $aWhere[0].' '.$aWhere[1].' ?';
                $this->aBind[] = $aWhere[2];
            }
        }
        
        return $this;
    }
    
    /**
     * Build the 'or' where clauses.
     * 
     * @param array $aWhere
     * 
     * @return $this
     */
    public function orWhere(array $aWhere)
    {
        return $this->where($aWhere, 'or');
    }
    
    /**
     * Execute the query and return the result.
     * 
     * @return array
     */
    public function get()
    {
        return $this->oDatabase->exec(
            'select', $this->buildSelect(), $this->aBind
        );
    }
    
    /**
     * Build the select query.
     * 
     * @return string $sQuery
     */
    private function buildSelect()
    {
        $sQuery = 'SELECT '. implode(', ', $this->aColumns).' FROM '.$this->sTable;
                
        if (!empty($this->aWhere['and']))
        {
            $sAndWhere = implode(' AND ', $this->aWhere['and']);
            $sQuery .= ' WHERE '.$sAndWhere;
        }

        if (!empty($this->aWhere['or']))
        {
            if ($sAndWhere = '')
            {
                $sQuery .= ' WHERE ';
            }
            else
            {
                $sQuery .= ' OR ';
            }

            $sQuery .= implode(' OR ', $this->aWhere['or']);
        }
        
        return $sQuery;
    }
}
