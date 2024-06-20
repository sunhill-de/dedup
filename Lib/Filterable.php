<?php

namespace Sunhill\Dedup;

/**
 * The interface filterable provides a standarized interface for filters to 
 * access informations.
 * 
 * @author Klaus Dimde
 *
 */
interface Filterable
{
    /**
     * Returns if the item has the given condition
     * 
     * @param string $name
     * @return bool
     */
    public function hasCondition(string $name): bool;
    
    /**
     * Returns if the item is writeable
     * 
     * @param string $name
     * @return bool
     */
    public function conditionWriteable(string $name): bool;
    
    /**
     * Returns the actual value of the condition
     * 
     * @param string $name
     */
    public function getCondition(string $name);
    
    /**
     * Sets the actual value of the condition
     * 
     * @param string $name
     * @param unknown $value
     */
    public function setCondition(string $name, $value);
    
}