<?php

namespace DevPhoenix\Psr7Decorator;

use DevPhoenix\Psr7Decorator\StreamDataDecoderBase;

/**
 * Concrete stream decode decorator
 */
class StreamDecoratorDecode extends StreamDataDecoderBase{
    /**
     * Stream Decode
     * decodes incoming ciphered data to decoded data
     */
    function decode(string $string): string{
        $string = $this -> decoder -> processDecript($this -> key, $string);
        return $string;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    // public function getContents() { return $this->parent->getContents();}
    public function getContents(): string { return $this->decode(parent::getContents());}
}