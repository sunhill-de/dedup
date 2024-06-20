<?php

namespace Sunhill\Dedup;

class Filter
{
    
    protected $conditions = [];
    
    protected $target;
    
    static protected $group = '';
    
    static protected $priority = 50;
    
    /**
     * Returns the group of this filter. The FilterManager uses this group to apply
     * only filters that are provided for the given item
     * 
     * @return string
     */
    public function getGroup(): string
    {
        return static::$group;
    }
    
    /**
     * The filtes of one group are sorted by priority by the filter manager
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return static::$priority;    
    }
    
    /**
     * Sets the needed conditions for this filter
     * 
     * @param array $conditions
     * @return \Sunhill\Dedup\Filter
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }
    
    public function mergeConditions(array $conditions)
    {
        $this->conditions = array_merge($this->conditions, $conditions);
        return $this;
    }
    
    public function setTarget(Filterable $target)
    {
        $this->target = $target;
        return $this;
    }
    
    public function matches(): bool
    {
        foreach ($this->conditions as $key => $value) {
            $method = 'get_'.$key;
            $current = $this->target->getCondition($key);
            if ($current !== $value) {
                return false;
            }
        }
        return true;
    }
    
    public function execute(): string
    {
        
    }
}