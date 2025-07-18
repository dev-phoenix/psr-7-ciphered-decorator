<?php
/**
 * 
 * debug helper
 */
namespace DevPhoenix\Psr7Decorator;

/**
 * provide tools for debug and testing
 */
class H{

    /**
     * helper
     * converter integer to size strign
     */
    public static function strSize(int|string $int): string {
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

    /**
     * helper
     * output prepared data needed for debug
     */
    public static function pre(mixed $d, string $t='', int $v=0, int $show=1): ?string{
        $ttpl = '<b>_t_:</b><br/>'; $ptpl = '<div>_t_<pre>_p_</pre></div>';
        if($t)$t=strtr($ttpl,['_t_'=>$t]);ob_start(); if($v === 'h' )echo htmlspecialchars($d);
        else  if($v)var_dump($d); else print_r($d); $out = ob_get_clean();
        $out = strtr($ptpl, ['_t_'=>$t,'_p_'=>$out]); if($show) echo $out; return $out;}

}