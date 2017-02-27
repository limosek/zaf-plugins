<?php

class Report {

    static $api;
    static $apiurl;
    static $apiuser;
    static $apipw;
    static $cache;

    public function initApi($url, $user, $pass) {
        self::$apiurl = $url;
        self::$apiuser = $user;
        self::$apipw = $pass;
        self::$api = new ZabbixApi($url, $user, $pass);

        self::$cache = New \Cache(
                array(
            'name' => 'JS',
            'path' => Config::getOpt("cache.dir") . "/",
            'extension' => '.cache'
                )
        );
        self::$cache->eraseExpired();
    }
    
    public function toCache($key,$data,$expiry=false) {
        if (!$expiry) {
            $expiry=self::$defaultexpiry;
        } else {
            $expiry=min($expiry,self::$maxexpiry);
        }
        self::$cache->store($key, $data, $expiry);
    }
    
    public function fromCache($key) {
        return(self::$cache->retrieve($key));
    }

    public function apiCall($func, $args) {
        $key = serialize($args) . $func . self::$apiurl . self::$apiuser;
        $data = self::fromCache($key);
        if (!$data) {
            $data = self::$api->$func($args);
            self::toCache($key, $data);
        }
        return($data);
    }
    
    public function itemsToArray($items) {
        $array = Array();
        foreach ($items as $i) {
            $array[$i->key_] = $i->itemid;
        }
        return($array);
    }
    
    public static function getValue($host, $item, $function, $args) {
        $items = self::apiCall("itemGet", array(
                    "host" => $host,
                    "search" => Array(
                        "key_" => $item
                    )
                 )
        );
        if (count($items)<1) {
            return(false);
        } else {
            foreach ($items as $i) {
                if ($i->key_==$item) {
                    break;
                }
            }
            if (!$i) {
                return(false);
            }
            $fromtime = time() - 3600 * 24 * 365;
            $history = self::apiCall("historyGet", array(
                        "history" => $i->value_type,
                        "time_from" => $fromtime,
                        "itemids" => $i->itemid,
                        "sortfield" => "clock",
                        "sortorder" => "ASC"
            ));
            switch ($function) {
                case "last":
                    return($history[0]->value);
                    break;
                case "regexp":
                    foreach ($history as $h) {
                        if (preg_match("/$args/",$h->value)) {
                            return("1");
                        }
                    }
                    return("0");
                    break;
                case "iregexp":
                    foreach ($history as $h) {
                        if (preg_match("/$args/i",$h->value)) {
                            return("1");
                        }
                    }
                    return("0");
                    break;
                case "history":
                    $out=Array();
                    foreach ($history as $h) {
                        $out[]=$h->value;
                    }
                    return(join("\n",$out));
                    break;
                case "csv":
                    $out=Array();
                    foreach ($history as $h) {
                        $out[]=$h->value;
                    }
                    return(Csv::row($out,true));
                    break;
            }
            return(false);
        }
    }
    
    public static function expandMacros($query) {
        while (preg_match("/{([^{]*):([^{]*)\.([^{]*)\(([^{]*)\)}/", $query, $matches)) {
            $macro = $matches[0];
            $host = $matches[1];
            $item = $matches[2];
            $function = $matches[3];
            $args = $matches[4];
            $value = self::getValue($host,$item,$function,$args);
            if ($value===false) {
                $value="";
            }
            $query = str_replace("{$macro}", $value, $query);
        }
        return($query);
    }
    
    public static function evalQuery($query) {
        if (preg_match('/\((?>[^)(]+|(?R))*\)/',$query,$match)) {
            if (substr($match[0],1,1)==" ") {
                $subquery=substr(substr($match[0],1),0,strlen($match[0])-2);
                Console::debug("subquery: $subquery");
                $sub=self::evalQuery($subquery);
                if ($sub) {
                    $sub="1=1";
                } else {
                    $sub="1=0";
                }
                Console::debug("=$sub\n");
                return(
                        self::evalQuery(str_replace("($subquery)",$sub,$query))
                        );
            }
        }
        if (preg_match("/ and /",$query)) {
            $queries=preg_split("/ and /",$query);
            Console::debug("( ");
            foreach ($queries as $query) {
                Console::debug("and $query ");
                if (!self::evalQuery($query)) {
                    Console::debug(")=false\n");
                    return(false);
                }
            }
            Console::debug(")=true\n");
            return(true);
        }
        if (preg_match("/ or /",$query)) {
            $queries=preg_split("/ or /",$query);
            Console::debug("( ");
            foreach ($queries as $query) {
                Console::debug("or $query ");
                if (self::evalQuery($query)) {
                    Console::debug(")=true\n");
                    return(true);
                }
            }
            Console::debug("=false\n");
            return(false);
        }
        if (preg_match("/=/",$query)) {
            List($macro,$value)=preg_split("/=/",$query);
            $mvalue=self::expandMacros($macro);
            return($mvalue == $value);
        } elseif (preg_match("/</",$query)) {
            List($macro,$value)=preg_split("/</",$query);
            $mvalue=self::expandMacros($macro);
            return($mvalue < $value);
        } elseif (preg_match("/>/",$query)) {
            List($macro,$value)=preg_split("/>/",$query);
            $mvalue=self::expandMacros($macro);
            return($mvalue > $value);
        } else {
            Console::error(1,"Not a zabbix query: $query\n");
        }
    }

}
