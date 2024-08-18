<?php

namespace Sunhill\Dedup\FileFilters;

use Sunhill\Dedup\Filter\Filter;

class FileFilter extends Filter
{
    
    static protected $group = 'Files';

    /**
     * Adds a message entry to the messages.
     * @param string $message
     */
    public function message(string $message)
    {
        if (!$this->container->hasCondition('message')) {
            // There are no messages yet
            $this->container->setCondition('message', $message);
        } else if (is_array($messages = $this->container->getCondition('message'))) {
            // There are aleady multiple messages
            $messages[] = $message;
            $this->container->setContainer('messages', $messages);
        } else {
            // There was only one message yet, make an array out of it
            $messages = [$messages, $message];
            $this->container->setContainer('messages', $messages);
        }
    }
    
    
}