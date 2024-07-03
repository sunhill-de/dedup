<?php

namespace Sunhill\Dedup\Filter;

class Filter
{
    
    static protected $group = '';
    
    static protected $priority = 50;
    
    static protected $conditions;
    
    protected $container;
    
    /**
     * Setter for $container
     * 
     * @param FilterContainer $container
     * @return self
     */
    public function setContainer(FilterContainer $container): self
    {
        $this->container = $container;
        return $this;
    }
    
    /**
     * Getter for container
     * 
     * @return FilterContainer
     */
    public function getContainer(): FilterContainer
    {
        return $this->container;    
    }

    /**
     * Initializes the condition array. Has to be overwritten by a filter
     */
    protected static function initializeConditions()
    {
        static::$conditions = [];    
    }
    
    /**
     * Checks if the condition list was already initialied. If not, call initializeConditions()
     */
    protected static function checkConditions()
    {
        if (empty(static::$conditions)) {
            static::initializeConditions();
        }
    }

    public static function clearConditions()
    {
        static::initializeConditions();    
    }
    
    /**
     * Adds a condition to the condition list
     * 
     * @param string $name
     * @param unknown $condition
     */
    public static function addCondition(string $name, $condition)
    {
        static::checkConditions();
        static::$conditions[$name] = $condition;
    }
    
    public static function addAlternativeCondition(array $condition)
    {
        static::checkConditions();
        static::$conditions[time()] = $condition;
    }
    
    /**
     * Returns the list of current conditions
     * 
     * @return array
     */
    public static function getConditions(): array
    {
        static::checkConditions();
        return static::$conditions;
    }
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
    
    protected function solveArray(array $array): bool
    {
        foreach ($array as $key => $value) {
            if ($this->container->getCondition($key) == $value) {
                return true;
            }
        }
        return false;
    }
    
    public function matches(?FilterContainer $container = null): bool
    {
        if ($container) {
            $this->setContainer($container);
        }
        foreach (static::getConditions() as $key => $value) {
            if (is_array($value)) {
                if (!$this->solveArray($value)) {
                    return false;
                }
            } else if ($this->container->getCondition($key) !== $value) {
                return false;
            }
        }
        return true;            
    }
    
   public function execute(): string
    {
        
    }
}