<?php

/**
 * 
 * t1.decoder.tester.class.php
 * 
 */
// include 't1.decoder.class.php';

namespace DevPhoenix\Psr7Decorator;

use DevPhoenix\Psr7Decorator\Decoder;

/**
 * implement tester of encription and decription files
 * Cipher test class specially for files
 */
class DecoderTester extends Decoder{

    public string $inFileName = '';
    public string $outFileName = '';
    public int $new_dir_permissions = 0777;

    private string $ROOT_DIR = '';
    private string $PARENT_DIR = '';
    private string $SRC_DIR = '';
    public string $OUT_DIR = '';

    private array $media_types_info = [];

    
    public function __construct($src_in_dir = 'source',$src_out_dir = 'results') {

        // $this -> decript_alg = 'aes-128-cbc';
        // H::pre($this -> decript_alg, 'Used decript algoritm');

        $src_dir_name = '/_src/i2crm-php-test-task-a0d44ecafc60/samples';
        $out_dir_name = '/_src/i2crm-php-test-task-a0d44ecafc60/results';

        // set target directories
        $root_dir = dirname(__FILE__);
        $parent_dir = dirname(dirname(dirname(__FILE__)));
        $src_dir = $parent_dir . $src_dir_name;
        $out_dir = $parent_dir . $out_dir_name;

        if($src_in_dir && is_dir($src_in_dir)) $src_dir = $src_in_dir;
        if($src_out_dir && is_dir($src_out_dir)) $out_dir = $src_out_dir;

        $this -> ROOT_DIR = $root_dir . '/';
        $this -> PARENT_DIR = $parent_dir . '/';
        $this -> SRC_DIR = $src_dir;
        $this -> OUT_DIR = $out_dir;

        if(strlen($this -> SRC_DIR) && $this -> SRC_DIR[strlen($this -> SRC_DIR) - 1] != '/')
            $this -> SRC_DIR = $this -> SRC_DIR . '/';
        if(strlen($this -> OUT_DIR) && $this -> OUT_DIR[strlen($this -> OUT_DIR) - 1] != '/')
            $this -> OUT_DIR = $this -> OUT_DIR . '/';

        $this -> init();
    }

    function init() {
        // init application info
        $media_types_info = [];
        $media_types_info['IMAGE']      = 'WhatsApp Image Keys';
        $media_types_info['VIDEO']      = 'WhatsApp Video Keys';
        $media_types_info['AUDIO']      = 'WhatsApp Audio Keys';
        $media_types_info['DOCUMENT']   = 'WhatsApp Document Keys';
        $this -> media_types_info = $media_types_info;
        // H::pre($this -> media_types_info, '$this -> media_types_info');
    }
    //*********************************************** */

    /** get file content */
    function get_from(string $fname, string $ext, string $dir, int $debug = 0): string{
        $data = '';
        $fpath = $dir.$fname.'.'.$ext;
        return file_get_contents($fpath);
    }

    /** put content to file */
    function save_to(string $fname, string $data, string $ext, string $dir, int $debug = 0): string{
        $fpath = $dir.$fname.'.'.$ext;
        try{
            $res = file_put_contents($fpath, $data);
        } catch ( \Exception $e ) {
        }
        return $res;
    }

    /** get key file content */
    function get_key(string $fname): string{
        return $this -> get_from($fname, 'key', $this -> SRC_DIR);
    }

    /** get encrypted souce file content */
    function get_encrypted(string $fname): string{
        return $this -> get_from($fname, 'encrypted', $this -> SRC_DIR);
    }

    /** get original file content */
    function get_original(string $fname): string{
        return $this -> get_from($fname, 'original', $this -> SRC_DIR);
    }

    /** get striming file content */
    function get_sidecar(string $fname): string{
        return $this -> get_from($fname, 'sidecar', $this -> SRC_DIR);
    }

    /** put key file content */
    function save_key(string $fname, string $data): string{
        return $this -> save_to($fname, $data, 'key', $this -> OUT_DIR);
    }

    /** put encrypted souce file content */
    function save_encrypted(string $fname, string $data): string{
        return $this -> save_to($fname, $data, 'encrypted', $this -> OUT_DIR);
    }

    /** put original file content */
    function save_original(string $fname, string $data): string{
        return $this -> save_to($fname, $data, 'original', $this -> OUT_DIR);
    }

    /** put striming file content */
    function save_sidecar(string $fname, string $data): string{
        return $this -> save_to($fname, $data, 'sidecar', $this -> OUT_DIR);
    }

    /**
     * get mediakey 
     */
    function getMediaKey($mediakey = '') {
        // get mediakey
        $this -> mediakey = $this -> get_key($this -> inFileName);
        $mediakey = $this -> mediakey;
        if(!$mediakey) { throw new \Exception('Decription key file not exists.'); }
        // exppand key
        if(!array_key_exists($this -> inFileName, $this -> media_types_info)) {
            $keys = array_keys($this -> media_types_info);
            $keys = implode(", ", $keys);
            throw new \Exception(sprintf('Application info not found. Key "%s" not in keys: %s.', $this -> inFileName, $keys));
        }
    }

    /**
     * Expand mediakey to ExpandedMediaKey
     */
    function expandMediaKey($mediakey, $salt='') {
        $salt = $this -> media_types_info[$this -> inFileName];

        // split key
        $this -> _expandMediaKey($mediakey, $salt);
    }

    /** get file ciphered content and mac key */
    function getFileEncripted($encrypted = '') {
        // get encripted file block
        $encrypted = $this -> get_encrypted($this -> inFileName);
        $encrypted_split = [];
        $encrypted_split['file'] = substr($encrypted, 0, -10); // file
        $encrypted_split['mac'] = substr($encrypted, -10); // mac key

        $this -> encriptedFile = $encrypted_split;
    }

    /** get file original content */
    function getFileOriginal($orig = '') {
        // get encripted file block
        $orig = $this -> get_original($this -> inFileName);
        $orig_split = [];
        $orig_split['file'] = $orig; // file
        $orig_split['mac'] = ''; // mac key

        $this -> originalFile = $orig_split;
    }

    /**
     * set new target directories if it given
     */
    public function initDirs(string $src_in_dir = '', string $src_out_dir = '') {
        if($src_in_dir) $this -> SRC_DIR = $src_in_dir;
        if($src_out_dir) $this -> OUT_DIR = $src_out_dir;

        if(strlen($this -> SRC_DIR) && $this -> SRC_DIR[strlen($this -> SRC_DIR) - 1] != '/')
            $this -> SRC_DIR = $this -> SRC_DIR . '/';
        if(strlen($this -> OUT_DIR) && $this -> OUT_DIR[strlen($this -> OUT_DIR) - 1] != '/')
            $this -> OUT_DIR = $this -> OUT_DIR . '/';
    }

    /**
     * main decription scenario function
     * @param string $fname Incoming file name
     * @param string $out_name Outcoming file name
     * @param string $src_in_dir Incoming files dir
     * @param string $src_out_dir Outcoming files dir
     */
    public function processDecription($fname, $out_name='', $src_in_dir = '', $src_out_dir = '') {
        $this -> sourcesPrepare($fname, $out_name, $src_in_dir, $src_out_dir);
        $this -> processEncriptionDecription(0);
    }

    /**
     * main encription scenario function
     * @param string $fname Incoming file name
     * @param string $out_name Outcoming file name
     * @param string $src_in_dir Incoming files dir
     * @param string $src_out_dir Outcoming files dir
     */
    public function processEncription($fname, $out_name='', $src_in_dir = '', $src_out_dir = '') {
        $this -> sourcesPrepare($fname, $out_name, $src_in_dir, $src_out_dir);
        $this -> processEncriptionDecription(1);
    }

    /**
     * main sources preparation function
     * @param string $fname Incoming file name
     * @param string $out_name Outcoming file name
     * @param string $src_in_dir Incoming files dir
     * @param string $src_out_dir Outcoming files dir
     * @throws \Exception on failure.
     */
    public function sourcesPrepare($fname, $out_name='', $src_in_dir = '', $src_out_dir = '') {
        if(!$fname) { throw new \Exception('Incoming file name not given.'); }
        $this -> inFileName = $fname;
        if($out_name) $this -> outFileName = $out_name;
        else $this -> outFileName = $fname;
        $this -> initDirs($src_in_dir, $src_out_dir);
        // H::pre($this -> OUT_DIR, 'OUT_DIR');
        // H::pre($this -> OUT_DIR, 'OUT_DIR');
        if(!is_dir($this -> OUT_DIR)) mkdir($this -> OUT_DIR, $this -> new_dir_permissions, true);
        if(!is_writable($this -> OUT_DIR)) { throw new \Exception('Target dirictory is not writable.'); }
    }

    /**
     * main encription scenario function
     * @param int $encript 1 = encript, 0 = decript
     * @throws \Exception on failure.
     */
    public function processEncriptionDecription($encript = 1) {
        $this -> getMediaKey();
        $this -> expandMediaKey($this -> mediakey);
        // $this -> getFileEncripted();

        if($encript) { $this -> getFileOriginal(); }
        else { $this -> getFileEncripted(); }

        if(!$encript){
            $this -> genEncriptedFileMacKey();
            $valid = $this -> validateEncriptedFile();
            if(!$valid) { throw new \Exception('Encripted file is not valid.'); }
        }

        if($encript) { $this -> cipheringFile(); }
        else { $this -> uncipheringFile(); }

        if($encript) { $this -> saveEncripted(); }
        else { $this -> saveDecripted(); }
    }

    /** save decripted file content */
    function saveDecripted(): int{
        $size = $this -> save_original($this -> outFileName, $this -> decriptedFileContent);
        return $size;
    }

    /** save uncripted file content */
    function saveEncripted(): int{
        $size = $this -> save_encrypted($this -> outFileName, $this -> criptedFileContent);
        return $size;
    }
}