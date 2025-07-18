<?php

namespace DevPhoenix\Psr7Decorator;

use Psr\Http\Message\StreamInterface;

/**
 * decorator
 * 
 * GuzzleHttp\Psr7
 * Psr7\AppendStream
 */
class AppendStreamDecorator{
    protected StreamInterface $parent;
    private $self_call = [];

    public function __construct(StreamInterface $parent){
        $this->parent = $parent;
    }
    
    public function __call($name, $arguments)
    {
        // if(!property_exists($this, $name))$this -> $self_call[$name]
        if(!array_key_exists($name, $this -> self_call)) $this -> self_call[$name] = 0;
        $this -> self_call[$name] ++;
        // Note: value of $name is case sensitive.
        echo "Calling {$this -> self_call[$name]} object method '$name' "
            . implode(', ', $arguments). "<br/>\n";
        
        return call_user_func([$this->parent, $name], ...$arguments);
        // return $this->parent[$name](...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        // Note: value of $name is case sensitive.
        echo "Calling static method '$name' "
            . implode(', ', $arguments). "\n";
    }

    public function __toString()
    {
        return $this->parent;
    }   
    
}