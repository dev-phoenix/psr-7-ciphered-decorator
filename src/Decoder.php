<?php

/**
 * 
 * t1.decoder.class.php
 */
namespace DevPhoenix\Psr7Decorator;

interface DecoderInterface {
}

/**
 * implement encription and decription data
 */
class Decoder implements DecoderInterface{

    public string $mediakey = '';
    private string $hkdf_alg = 'sha256'; // expland mediakey algoritm for expand MediaKey
    private string $hmac_alg = 'sha256'; // generate macKey algoritm
    private string $decript_alg = 'aes-256-cbc';
    private string $key = '';

    private array $mediaKeyExpanded = [];
    private string $macKey = '';
    public array $encriptedFile = [];
    public array $originalFile = [];

    public string $criptedFileContent = '';
    public string $decriptedFileContent = '';

    
    public function __construct() {
    }

    /** not use */
    function __destruct(){}

    function init() {
    }
    //*********************************************** */

    /**
     * helper
     * converter integer to size strign
     */
    public function strSize(int|string $int): string {
        $int = (int) $int;
        $_g = 1024 * 1024 * 1024;
        $_m = 1024 * 1024;
        $_k = 1024;
        $g = '';
        $m = '';
        $k = '';
        $i = $int;
        $out = [];
        if($i > $_g) {$d = (int) ($i / $_g); $i = $i - $d * $_g; $out[] = sprintf("%dG", $d);}
        if($i > $_m) {$d = (int) ($i / $_m);  $i = $i - ($d * $_m); $out[] = sprintf("%dM", $d);}
        if($i > $_k) {$d = (int) ($i / $_k); $i = $i - $d * $_k; $out[] = sprintf("%dk", $d);}
        if($i > 0) {$d = $i; $out[] = sprintf("%d", $d);}
        $out = implode(' ', $out);
        if(!$out) $out = 0;
        return $out;
    }

    public static function pre(mixed $d, string $t='', int $v=0, int $show=1): ?string{
        $ttpl = '<b>_t_:</b><br/>'; $ptpl = '<div>_t_<pre>_p_</pre></div>';
        if($t)$t=strtr($ttpl,['_t_'=>$t]);ob_start(); if($v === 'h' )echo htmlspecialchars($d);
        else  if($v)var_dump($d); else print_r($d); $out = ob_get_clean();
        $out = strtr($ptpl, ['_t_'=>$t,'_p_'=>$out]); if($show) echo $out; return $out;}

    function processEncript($mediaKey = '', $data = ''){
        $this -> getMediaKey($mediaKey);
        $this -> expandMediaKey($this -> mediakey);
        $this -> getFileOriginal($data);
        $this -> cipheringFile();
        return $this -> criptedFileContent;
    }

    function processDecript($mediaKey = '', $data = ''){
        $this -> getMediaKey($mediaKey);
        $this -> expandMediaKey($this -> mediakey);
        // $this -> getFileEncripted();

        $this -> getFileEncripted($data);

        $this -> genEncriptedFileMacKey();
        $valid = $this -> validateEncriptedFile();
        if(!$valid) { throw new \Exception('Encripted file is not valid.'); }

        $this -> uncipheringFile();
        return $this -> decriptedFileContent;
    }

    /**
     * get mediakey 
     */
    function getMediaKey($mediakey = '') {
        // get mediakey
        $this -> mediakey = $mediakey;
        if(!$mediakey) { throw new \Exception('Decription key is empty.'); }
        // exppand key
        // if(!array_key_exists($this -> inFileName, $this -> media_types_info)) {
        //     $keys = array_keys($this -> media_types_info);
        //     $keys = implode(", ", $keys);
        //     throw new Exception(sprintf('Application info not found. Key "%s" not in keys: %s.', $this -> inFileName, $keys));
        // }

        // $this -> expandMediaKey($mediakey, $salt);
    }

    /**
     * Expand mediakey to ExpandedMediaKey
     */
    function expandMediaKey($mediakey, $salt='') {
        // $salt = $this -> media_types_info[$this -> inFileName];

        // split key
        $this -> _expandMediaKey($mediakey, $salt);
    }

    function _expandMediaKey($mediakey, $salt) {
        $alg = $this -> hkdf_alg;
        $mediaKeyExpanded = hash_hkdf($alg, $mediakey, 112, $salt);

        // split key
        $mediakey_split = [];
        $mediakey_split['iv'] = substr($mediaKeyExpanded, 0, 16);
        $mediakey_split['cipherKey'] = substr($mediaKeyExpanded, 16, 48-16);
        $mediakey_split['macKey'] = substr($mediaKeyExpanded, 48, 80-48);
        $mediakey_split['refKey'] = substr($mediaKeyExpanded, 80, 112-80);

        $this -> mediaKeyExpanded = $mediakey_split;
    }

    /** get file ciphered content and mac key */
    function getFileEncripted($encrypted = '') {
        // get encripted file block
        // $encrypted = $this -> get_encrypted($this -> inFileName);
        $encrypted_split = [];
        $encrypted_split['file'] = substr($encrypted, 0, -10); // file
        $encrypted_split['mac'] = substr($encrypted, -10); // mac key

        $this -> encriptedFile = $encrypted_split;
    }

    /** get file original content */
    function getFileOriginal($orig = '') {
        // get encripted file block
        // $orig = $this -> get_original($this -> inFileName);
        $orig_split = [];
        $orig_split['file'] = $orig; // file
        $orig_split['mac'] = ''; // mac key

        $this -> originalFile = $orig_split;
    }

    /** check is file valid */
    function genEncriptedFileMacKey() {
        // gen hmac 
        $iv_file = $this -> mediaKeyExpanded['iv'] . $this -> encriptedFile['file'];
        $this -> _genEncriptedFileMacKey($iv_file);
    }

    function _genEncriptedFileMacKey($iv_file) {
        // gen hmac 
        $alg = $this -> hmac_alg;
        $encrypted_hmac = hash_hmac($alg, $iv_file, $this -> mediaKeyExpanded['macKey'], 1);
        $check_mac = substr($encrypted_hmac, 0, 10);
        $this -> macKey = $check_mac;
    }

    function validateEncriptedFile() {
        $check_mac = $this -> macKey;
        $is_valid = ($check_mac == $this -> encriptedFile['mac']);
        // Decoder::pre(($check_mac == $this -> encriptedFile['mac']), '($check_mac == $this -> encriptedFile[\'mac\'])');
        // Decoder::pre($check_mac, '$check_mac');
        // Decoder::pre($this -> encriptedFile['mac'], ' $this -> encriptedFile[\'mac\']');
        // Decoder::pre($this -> encriptedFile, ' $this -> encriptedFile');
        return $is_valid;
    }

    /** decript cipher file */
    function uncipheringFile() {
        $alg = $this -> decript_alg;
        $key = $this -> mediaKeyExpanded['cipherKey'];
        $iv = $this -> mediaKeyExpanded['iv'];
        $file = openssl_decrypt($this -> encriptedFile['file'], $alg, $key, OPENSSL_RAW_DATA, $iv);
        $this -> decriptedFileContent = $file;
    }

    /** to cipher oringin file */
    function cipheringFile() {
        $alg = $this -> decript_alg;
        $key = $this -> mediaKeyExpanded['cipherKey'];
        $iv = $this -> mediaKeyExpanded['iv'];
        $file = openssl_encrypt($this -> originalFile['file'], $alg, $key, OPENSSL_RAW_DATA, $iv);
        $this -> encriptedFile['file'] = $file;
        $this -> genEncriptedFileMacKey();
        $this -> criptedFileContent = $file . $this -> macKey;
    }

    public function __set(string $name, mixed $value): void {

    }
}
