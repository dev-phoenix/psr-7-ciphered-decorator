<?php
/**
 * 
 * Stream decrorator implementations
 * t1.decorator.class.php
 */

namespace DevPhoenix\Psr7Decorator;

use DevPhoenix\Psr7Decorator\Decoder;

use Psr\Http\Message\StreamInterface;

// ***************************
/** debug helper
 * data
 * title
 * var dump
 * echo
 */
function pre(mixed $d, string $t='', int $v=0, int $show=1): ?string{
    $ttpl = '<b>_t_:</b><br/>'; $ptpl = '<div>_t_<pre>_p_</pre></div>';
    if($t)$t=strtr($ttpl,['_t_'=>$t]);ob_start(); if($v === 'h' )echo htmlspecialchars($d);
	else  if($v)var_dump($d); else print_r($d); $out = ob_get_clean();
    $out = strtr($ptpl, ['_t_'=>$t,'_p_'=>$out]); if($show) echo $out; return $out;}
/**
 * debug helper
 * data
 * title
 * echo
 */
function pred(any $d, string $t='', int $show=1): string {
    return H::pre($d,$t,1,$show);
}
// ***************************

/**
 * Basic stream decorator
 */
class StreamDecorator implements StreamInterface
{
    protected StreamInterface $parent;
    protected string $key = '';
    protected Decoder $decoder;

    public function __construct(StreamInterface $parent, string $key = ''){
        // H::pre($key, 'StreamDecorator $key');
        // echo '<pre>';
        // debug_print_backtrace();
        $this->parent = $parent;
        $this->key = $key;
        $this->decoder = new Decoder();
    }
    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString(): string { return $this->parent->__toString();}

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close(): void { $this->parent->close();}

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach(){ return $this->parent->detach();}

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): ?int { return $this->parent->getSize();}

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell(): int { return $this->parent->tell();}

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof(): bool { return $this->parent->eof();}

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable(): bool { return $this->parent->isSeekable();}

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET): void { $this->parent->seek($offset, $whence);}

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind(): void { $this->parent->rewind();}

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable(): bool { return $this->parent->isWritable();}

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string): int { 
        H::pre($string, 'write');
        $out = $this->parent->write($string);
        H::pre($out, 'write out');
        return $out;
        return $this->parent->write($string);}

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool { return $this->parent->isReadable();}

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length): string {
        H::pre($length, 'read');
        $out = $this->parent->read($length);
        H::pre($out, 'read out');
        return $out;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents(): string { return $this->parent->getContents();}

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata(?string $key = null) { return $this->parent->getMetadata($key);}

    // public function addStream($key = null){ 
    //     H::pre($key, 'addStream');
    //     return $this->parent->addStream($key);}

    // public __call(string $name, array $arguments): mixed
    
    private $self_call = [];
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

    // public function __toString()
    // {
    //     return $this->parent;
    // }   
    
}