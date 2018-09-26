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
     * Execute the query and return the result.
     * 
     * @param array $aValue
     * 
     * @return mixed
     */
    public function insert(array $aValue)
    {
        return $this->oDatabase->exec(
            'insert', $this->buildInsert($aValue), $this->aBind
        );
    }
    
    /**
     * Execute the query and return the result.
     * 
     * @param array $aValue
     * 
     * @return mixed
     */
    public function update(array $aValue)
    {
        return $this->oDatabase->exec(
            'update', $this->buildUpdate($aValue), $this->aBind
        );
    }
    
    /**
     * Execute the query and return the result.
     * 
     * @param array $aValue
     * 
     * @return mixed
     */
    public function delete()
    {
        return $this->oDatabase->exec(
            'delete', $this->buildDelete(), $this->aBind
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
             
        $sQuery .= $this->buildWhere();
        
        return $sQuery;
    }
    
    /**
     * Build the insert query.
     * 
     * @param array $aValue
     * 
     * @return string
     */
    private function buildInsert(array $aValue)
    {
        //We'll list all the columns where there are a value to insert.
        $aColumn    = [];
        //We'll add all '?'
        $aBindMark  = [];
        foreach ($aValue as $sKey => $mValues)
        {
            //If $mValue is an array, then we are inserting multiple row.
            //Else we insert a unique row.
            if(!is_array($mValues))
            {
                $bUnique        = true;
                $aColumn[]      = $sKey;
                $this->aBind[]  = $mValues;
                $aBindMark[]    = '?';
            }
            else
            {
                $bUnique = false;
                if (empty($aColumn))
                {
                    $aColumn    = array_keys($mValues);
                    $iNbValue   = count($mValues);
                    
                    $aBindNbMark = [];
                    for ($i = 0 ; $i < $iNbValue ;  $i++)
                    {
                        $aBindNbMark[] = '?';
                    }
                    
                    $sBindMark = '('. implode(', ', $aBindNbMark).')';
                }
                
                foreach ($mValues as $mValue)
                {
                    $this->aBind[] = $mValue;
                }
                
                $aBindMark[] = $sBindMark;
            }
        }
            
        if ($bUnique)
        {
            $sBind = '('. implode(', ', $aBindMark).')';
        }
        else
        {
            $sBind = implode(', ', $aBindMark);
        }
        
        return 'INSERT INTO '.$this->sTable.' ('. implode(', ', $aColumn).') VALUES '.$sBind;
    }
    
    /**
     * Build the update query.
     * 
     * @param array $aValue
     * 
     * @return string $sQuery
     */
    private function buildUpdate(array $aValue)
    {
        $sQuery = 'UPDATE '.$this->sTable.' SET ';
        
        $aSet   = [];
        $aBind  = [];
        foreach ($aValue as $sKey => $mValue)
        {
            $aSet[]     = $sKey.' = ?';
            $aBind[]    = $mValue;
        }
        
        $this->aBind = array_merge($aBind, $this->aBind);
        
        $sQuery .= implode(', ', $aSet);
        $sQuery .= $this->buildWhere();
        
        return $sQuery;
    }
    
    /**
     * Build the delete query.
     * 
     * @return string $sQuery
     */
    private function buildDelete()
    {
        $sQuery = 'DELETE FROM '.$this->sTable;
        
        $sQuery .= $this->buildWhere();
        
        return $sQuery;
    }
    
    /**
     * Build the where clause.
     * 
     * @return string $sWhere
     */
    private function buildWhere()
    {
        $sWhere     = '';
        $sAndWhere  = '';
        if (!empty($this->aWhere['and']))
        {
            $sAndWhere = implode(' AND ', $this->aWhere['and']);
            $sWhere .= ' WHERE '.$sAndWhere;
        }

        if (!empty($this->aWhere['or']))
        {
            if ($sAndWhere = '')
            {
                $sWhere .= ' WHERE ';
            }
            else
            {
                $sWhere .= ' OR ';
            }

            $sWhere .= implode(' OR ', $this->aWhere['or']);
        }
        
        return $sWhere;
    }
}
