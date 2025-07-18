<?php

namespace DevPhoenix\Psr7Decorator;

use DevPhoenix\Psr7Decorator\StreamDataDecoderBase;

/**
 * Concrete stream encode decorator
 */
class StreamDecoratorEncode extends StreamDataDecoderBase{

    /**
     * decorator for outcoming data
     */
    function read($length): string { return $this->encode(parent::read($length));}
    /**
     * Stream Encode
     * ciphering incoming decoded data to encoded data
     */
    function encode(string $string): string{
        $string = $this -> decoder -> processEncript($this -> key, $string);
        return $string;
    }
    public function getContents(): string { return $this->encode(parent::getContents());}
}