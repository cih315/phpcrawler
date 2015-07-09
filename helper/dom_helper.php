<?php

// no namespace, add these functions to global scope

//
// simple escape without quotes
//
if(!function_exists('esc')) {
    function esc($s) {
        return htmlspecialchars($s, ENT_NOQUOTES);
    }
}

//
// full escape with quotes (for attributes)
//
if(!function_exists('fesc')) {
    function fesc($s) {
        return htmlspecialchars($s, ENT_QUOTES);
    }
}

//
// cloak string in HTML
//
if(!function_exists('cloakHTML')) {
    function cloakHTML($s) {
        $s = '' . $s;
        $return = array();
        for($i = 0; $i < strlen($s); $i++)
            if(ctype_alnum($s[$i])) $return[] = '&#' . ord($s[$i]) . ';';
            else $return[] = fesc($s[$i]);
        return implode('', $return);
    }
}

//
// template()
//
if(!function_exists('template')) {
    function template() { // $template, $vars, $raw = false
        @ extract(func_get_arg(1));
        ob_start();
        @ include(func_get_arg(0));
        $_ob = ob_get_clean();
        $_raw = ((func_num_args() > 2) ? func_get_arg(2) : false);
        if($_raw) return $_ob;
        $_class = str_replace('.', '-', basename(func_get_arg(0)));
        return sprintf('<div class="%s">', $_class) . $_ob . '</div>';
    }
}

//
// validate host
//
if(!function_exists('validateHost')) {
    function validateHost($host) {
        return (
            preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $host)
                and
            preg_match('/^.{1,253}$/', $host)
                and
            preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $host)
        );
    }
}

//
// get host from URL
//
if(!function_exists('host')) {
    function host($url) {
        $host = strtolower(parse_url($url, PHP_URL_HOST));
        if(validateHost($host)) return $host;
        return null;
    }
}

//
// current date
//
if(!function_exists('curdate')) {
    function curdate($days = 0) {
        return date(SQL_FORMAT_DATE, time() + $days * 24 * 3600);
    }
}

//
// current time
//
if(!function_exists('now')) {
    function now($seconds = 0) {
        return date(SQL_FORMAT_DATETIME, time() + $seconds);
    }
}

//
// get tag's attributes
//
if(!function_exists('getTagAttr')) {
    function getTagAttr($tag, $attr = null) {
        $tag = trim($tag);
        $tag = preg_replace('~^(<\w+[^>]*>).*$~is', '$1', $tag);
        preg_match_all('~\b(?P<attr>[\w-]+)=([\'"])?(?P<value>.*?)\2~', $tag, $one, PREG_SET_ORDER);
        preg_match_all('~\b(?P<attr>[\w-]+)=(?P<value>\w+)~', $tag, $two, PREG_SET_ORDER);
        $collector = array();
        foreach(array_merge($one, $two) as $elem)
            $collector[strtolower($elem['attr'])] = html_entity_decode($elem['value'], ENT_QUOTES);
        if(!is_null($attr)) return @ $collector[$attr];
        return $collector;
    }
}

//
// split content to lines (trim'em, eliminate empty)
//
if(!function_exists('nsplit')) {
    function nsplit($value) {
        $value = str_replace(chr(13), chr(10), $value);
        $value = explode(chr(10), $value);
        $value = array_map('trim', $value);
        $value = array_filter($value);
        return array_values($value);
    }
}

//
// check if argument is anonymous function
//
if(!function_exists('is_closure')) {
    function is_closure($obj) {
        return is_callable($obj) and is_object($obj);
    }
}

//
// check if string is regular ip
//
if(!function_exists('is_ip')) {
    function is_ip($string) {
        $string = trim($string);
        preg_match("~^\d+\.\d+\.\d+\.\d+$~", $string, $match);
        if(!$match) return false;
        $string = explode('.', $string);
        $string = array_values(array_filter($string, function($part) {
            $part = intval($part);
            return (0 <= $part) and ($part <= 255);
        }));
        return count($string) === 4;
    }
}

//
// str_replace just once
//
if(!function_exists('str_replace_once')) {
    function str_replace_once($needle, $replace, $haystack) {
        @ $pos = strpos($haystack, $needle);
        if($pos === false) return $haystack;
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
}

//
// cron string
//
if(!function_exists('str_crop')) {
    function str_crop($string, $begin, $end) {
        @ $one = strpos($string, $begin);
        if($one === false) return null;
        $one += strlen($begin);
        $string = substr($string, $one);
        @ $two = strpos($string, $end);
        if($two === false) return null;
        return substr($string, 0, $two);
    }
}

//
// check if array is fully associative
//
if(!function_exists('is_assoc')) {
    function is_assoc($arr) {
        if(!is_array($arr)) return false;
        $count0 = count($arr);
        $count1 = count(array_filter(array_keys($arr), 'is_string'));
        return $count0 === $count1;
    }
}

//
// truncate long strings
//
if(!function_exists('str_truncate')) {
    function str_truncate($string, $len = 40, $center = true, $replacer = '...') {
        $l = mb_strlen($replacer);
        if($center and $len < (2 + $l)) $len = (2 + $l);
        if(!$center and $len < (1 + $l)) $len = (1 + $l);
        if($center and mb_strlen($string) > $len) {
            $len -= $l;
            $begin = ceil($len / 2);
            $end = $len - $begin;
            return mb_substr($string, 0, $begin) . $replacer . mb_substr($string, - $end);
        } elseif(!$center and mb_strlen($string) > $len) {
            $len -= $l;
            $begin = $len;
            return mb_substr($string, 0, $begin) . $replacer;
        } else return $string;
    }
}

//
// smart array shuffle
//
if(!function_exists('mt_shuffle')) {
    function mt_shuffle(& $items, $seed = null) {
        $keys = array_keys($items);
        $SEED = '';
        for($i = count($items) - 1; $i > 0; $i--) {
            if($seed) {
                $j = rand_from_string($SEED . $seed) % ($i + 1);
                $SEED .= $j;
            } else $j = mt_rand(0, $i);
            list($items[$keys[$i]], $items[$keys[$j]]) = array($items[$keys[$j]], $items[$keys[$i]]);
        }
    }
}

//
// get extension
//
if(!function_exists('file_get_ext')) {
    function file_get_ext($file) {
        $ext = $file;
        $ext = trim($ext);
        $ext = explode('/', $ext);
        $ext = end($ext);
        $ext = explode('.', $ext);
        $ext = end($ext);
        $ext = strtolower($ext);
        if($ext === strtolower(basename($file))) return '';
        return $ext;
    }
}

//
// get basename without extension
//
if(!function_exists('file_get_name')) {
    function file_get_name($file) {
        $file = trim($file);
        $file = explode('/', $file);
        $file = end($file);
        list($file) = explode('.', $file);
        return $file;
    }
}

//
// get random integer from string
//
if(!function_exists('rand_from_string')) {
    function rand_from_string($string) {
        $int = md5($string);
        $int = preg_replace('/[^0-9]/', '', $int);
        $int = substr($int, 0, strlen(mt_getrandmax() . '') - 1);
        return intval($int);
    }
}

//
// normal distribution function
//
if(!function_exists('gauss')) {
    function gauss($peak = 0, $stdev = 1, $seed = null) {
        $x = ($seed ? rand_from_string($seed) : mt_rand()) / mt_getrandmax();
        $y = ($seed ? rand_from_string($seed . $x) : mt_rand()) / mt_getrandmax();
        $gauss = sqrt(-2 * log($x)) * cos(2 * pi() * $y);
        return $gauss * $stdev + $peak;
    }
}

//
// get user agent string for browser emulation
// can be filtered by regular expression and seeded for static randomness
//
if(!function_exists('getUserAgent')) {
    function getUserAgent($re = null, $seed = null) {
        $LIST = __DIR__ . '/ua.list.txt';
        //
        if(is_array($seed)) $seed = json_encode($seed);
        if($seed and is_string($seed . '')) $seed = rand_from_string($seed . '');
        else $seed = rand_from_string(microtime(true) . '');
        if(!is_file($LIST)) goto error;
        $list = nsplit(file_get_contents($LIST));
        if($re and is_string($re))
            $list = array_values(array_filter($list, function($line) use($re) {
                if(preg_match('~^\w+$~', $re)) $re = "~{$re}~i";
                return @ preg_match($re, $line);
            }));
        if(!$list) goto error;
        $ua = $list[$seed % count($list)];
        if($ua) return $ua;
        error:
        trigger_error('INVALID UA', E_USER_WARNING);
        return null;
    }
}

//
// parse proxy string
//
if(!function_exists('parseProxy')) {
    function parseProxy($proxy) {
        if(!is_string($proxy)) return null;
        $proxy = parse_url($proxy);
        if(!in_array($proxy['scheme'], array('proxy', 'socks'))) return null;
        if(!$proxy['host'] or !isset($proxy['port'])) return null;
        @ $user = $proxy['user'] ? : '';
        @ $pass = $proxy['pass'] ? : '';
        if($user and $pass) $user .= ':' . $pass;
        $proxy['host:port'] = "{$proxy['host']}:{$proxy['port']}";
        $proxy['user:pass'] = $user;
        return $proxy;
    }
}

//
// check audio file (by URL)
//
if(!function_exists('checkAudio')) {
    function checkAudio($url, $options = array()) {
        if(!host($url) or !is_array($options)) return null;
        $options = $options + array(
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RANGE => '0-64'
        );
        $_ = curl($url, $options);
        $parts = explode("\r\n\r\n", $_);
        $body = array_pop($parts);
        $file = rtrim(`mktemp`);
        file_put_contents($file, $body);
        $_ = shell_exec("file '{$file}' 2>&1");
        unlink($file);
        if(is_numeric(stripos($_, 'audio file'))) return true;
        if(
            is_numeric(stripos($_, 'mpeg adts'))
                and
            is_numeric(stripos($_, 'layer iii'))
        ) return true;
        return false;
    }
}

//
// check image file (by URL)
//
if(!function_exists('checkImage')) {
    function checkImage($url, $options = array()) {
        if(!host($url) or !is_array($options)) return null;
        $options = $options + array(
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RANGE => '0-64'
        );
        $_ = curl($url, $options);
        $parts = explode("\r\n\r\n", $_);
        $body = array_pop($parts);
        $file = rtrim(`mktemp`);
        file_put_contents($file, $body);
        $_ = shell_exec("file '{$file}' 2>&1");
        unlink($file);
        if(is_numeric(stripos($_, 'png image data'))) return true;
        if(is_numeric(stripos($_, 'gif image data'))) return true;
        if(is_numeric(stripos($_, 'jpeg image data'))) return true;
        return false;
    }
}

//
// simple interface to cURL
//
if(!function_exists('curl')) {
    function curl($url, $options = array()) {
        if(!host($url) or !is_array($options)) return null;
        @ $proxy = $options['proxy'];
        unset($options['proxy']);
        $options = $options + array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => getUserAgent(),
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_MAXREDIRS => 5
        );
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        if($proxy === true)
            if(!function_exists('getProxy')) return null;
            else $proxy = getProxy();
        $parse = parseProxy($proxy);
        if($proxy and !$parse) return null;
        if($parse) {
            // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, $parse['host:port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $parse['user:pass']);
        }
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $info = curl_getinfo($ch);
        $code = intval($info['http_code']);
        curl_close($ch);
        if($err or !in_array($code, array(200, 206)))
            return null;
        return $content;
    }
}

//
// file_post_contents
//
if(!function_exists('file_post_contents')) {
    function file_post_contents($url, $array) {
        $postdata = http_build_query($array);
        $eol = "\r\n";
        $header = array(
            'User-Agent: ' . getUserAgent(),
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($postdata)
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => implode($eol, $header),
                'content' => $postdata
            )
        );
        $context  = stream_context_create($opts);
        return file_get_contents($url, false, $context);
    }
}

//
// http://php.net/manual/en/function.array-column.php
//
if(!function_exists('array_column')) {
    function array_column($input = null, $columnKey = null, $indexKey = null) {
        $argc = func_num_args();
        $params = func_get_args();
        // Argument check...
        if($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if(!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }
        if(
            !is_int($params[1])
            and !is_float($params[1])
            and !is_string($params[1])
            and $params[1] !== null
            and !(
                is_object($params[1]) and method_exists($params[1], '__toString')
            )
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return null;
        }
        if(
            isset($params[2])
            and !is_int($params[2])
            and !is_float($params[2])
            and !is_string($params[2])
            and !(
                is_object($params[2]) and method_exists($params[2], '__toString')
            )
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return null;
        }
        // Let's prepare...
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] === null) ? null : ($params[1] . '');
        $paramsIndexKey = null;
        if(isset($params[2]))
            if(is_float($params[2]) or is_int($params[2]))
                $paramsIndexKey = intval($params[2]);
            else
                $paramsIndexKey = ($params[2] . '');
        $resultArray = array();
        // Go!!!
        foreach($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if($paramsIndexKey !== null and array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = ($row[$paramsIndexKey] . '');
            }
            if($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif(is_array($row) and array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if($valueSet and $keySet) $resultArray[$key] = $value;
            elseif($valueSet) $resultArray[] = $value;
        }
        return $resultArray;
    }
}

//
// check whether IP belongs to a list
//
if(!function_exists('checkIP')) {
    function checkIP($ip, $list) {
        if(!validateHost($ip)) return false;
        if(!is_array($list)) $list = explode(',', $list);
        foreach($list as $mask) {
            $mask = str_replace('.', '\\.', $mask);
            $mask = str_replace('*', '[0-9]+(\\.[0-9]+)*', $mask);
            if(preg_match("~^{$mask}$~", $ip)) return true;
        }
        return false;
    }
}

//
// upload file to storage
//
function toStorage($file, $settings = array()) {
    @ $naming = $settings['naming'] ? $settings['naming'] : "%s";
    @ $ext = $settings['ext'] ? $settings['ext'] : "";
    @ $delete = $settings['delete'] ? $settings['delete'] : false;
    @ $subdir = $settings['subdir'] ? $settings['subdir'] : false;
    @ $dir = $settings['dir'] ? $settings['dir'] : false;
    $add = 0;
    clearstatcache();
    $dir = rtrim($dir, '/');
    if(host($file)) {
        $tempDirectory = rtrim(`mktemp -d`);
        $content = curl($file);
        if(!is_string($content)) {
            _warn(__FUNCTION__, 'INVALID FILE (URL)!');
            return null;
        }
        $path = parse_url($file, PHP_URL_PATH);
        $name = basename($path);
        if(!$name) $name = mt_rand() . '.tmp';
        $file = "{$tempDirectory}/{$name}";
        file_put_contents($file, $content);
    }
    // $dir - это каталог! $file - это файл!
    if(!is_file($file) or !is_dir($dir)) {
        _warn(__FUNCTION__, 'INVALID FILE OR DIRECTORY!');
        return null;
    }
    $hash = function($_) { return hash('sha256', $_, false); };
    //
    $name = file_get_name($file);
    $ext = $ext ? $ext : file_get_ext($file);
    if($ext) $ext = ".{$ext}";
    //
    if(!$name) $name = mt_rand();
    $name = str_replace(' ', '-', $name);
    if(is_callable($naming)) $name = $naming($name);
    elseif(is_string($naming)) $name = sprintf($naming, $name);
    $name = preg_replace('~\W+~', ' ', $name);
    $name = preg_replace('~\s+~', '-', $name);
    // $name = Normalizer::go($name, 'tr,latinRu,en,hyphen');
    if(!$name) $name = mt_rand();
    //
    $fileHash = $hash(file_get_contents($file));
    if(!$subdir) $subdir = rand_from_string($fileHash) % 1000;
    if(!is_dir($_ = $dir . '/' . $subdir)) mkdir($_, 0755);
    $_ = "/{$subdir}/{$name}{$ext}";
    while(true):
        if(is_file($dir . $_) and $hash(file_get_contents($dir . $_)) === $fileHash) {
            if($delete) unlink($file);
            if(isset($tempDirectory)) `rm -rf '{$tempDirectory}'`;
            return $_;
        }
        if(!is_file($dir . $_)) {
            file_put_contents($dir . $_, file_get_contents($file));
            if($delete) unlink($file);
            if(isset($tempDirectory)) `rm -rf '{$tempDirectory}'`;
            return $_;
        }
        $_ = "/{$subdir}/{$name}-{$add}{$ext}";
        $add += 1;
    endwhile;
}

//
// alias to trigger_error with E_USER_NOTICE
//
if(!function_exists('_log')) {
    function _log($source, $message) {
        $now = now();
        trigger_error("[log] [{$source}] {$now}: {$message}", E_USER_NOTICE);
    }
}

//
// alias to trigger_error with E_USER_WARNING
//
if(!function_exists('_warn')) {
    function _warn($source, $message) {
        $now = now();
        trigger_error("[warn] [{$source}] {$now}: {$message}", E_USER_WARNING);
    }
}

//
// alias to trigger_error with E_USER_ERROR
//
if(!function_exists('_err')) {
    function _err($source, $message, $class = 'Exception') {
        $now = now();
        $rc = new \ReflectionClass($class);
        $exception = $rc -> newInstance("[err] [{$source}] {$now}: {$message}");
        throw $exception;
    }
}

//
// secret global vars
//
if(!function_exists('myGlobal')) {
    function & myGlobal($var) {
        $var = md5(__FUNCTION__) . "_{$var}";
        if(!isset($GLOBALS[$var])) $GLOBALS[$var] = null;
        return $GLOBALS[$var];
    }
}
