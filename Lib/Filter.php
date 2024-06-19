<?php

namespace Sunhill\Dedup;

class Filter
{
    
    protected $conditions = [];
    
    protected $target;
    
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
    
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }
    
    public function matches(): bool
    {
        foreach ($this->conditions as $key => $value) {
            $method = 'get_'.$key;
            $current = $this->target->$method();
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