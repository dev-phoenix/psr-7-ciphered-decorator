<?php

namespace DevPhoenix\Psr7Decorator;

use DevPhoenix\Psr7Decorator\StreamDecorator;

/**
 * Interface for stream decorator class
 */
interface StreamDataDecoderInterface{
    /**
     * StreamDecode
     * decodes incoming ciphered data to decoded data
     */
    public function decode(string $string): string;
    /**
     * StreamEncode
     * ciphering incoming decoded data to encoded data
     */
    public function encode(string $data): string;
    /**
     * decorator for incoming data
     */
    public function read(string $data): string;
    /**
     * decorator for outcoming data
     */
    public function write(string $data): int;
}

/**
 * Base stream decorator class
 */
class StreamDataDecoderBase extends StreamDecorator implements StreamDataDecoderInterface{
    /**
     * StreamDecode
     * decodes incoming ciphered data to decoded data
     */
    function decode(string $string): string { return $string;}
    /**
     * StreamEncode
     * ciphering incoming decoded data to encoded data
     */
    function encode(string $string): string { return $string;}
    /**
     * decorator for incoming data
     */
    function read($length): string { return $this->decode(parent::read($length));}
    /**
     * decorator for outcoming data
     */
    function write($string): int { return parent::write($this->encode($string));}
}