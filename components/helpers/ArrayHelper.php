<?php

namespace app\components\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper {

    public static function getLabels($keys, $array) {
        if (ArrayHelper::isTraversable($keys)) {
            $ret = [];
            foreach ($keys as $k) {
                $ret[$k] = static::getLabels($k, $array);
            }
            return $ret;
        } else {
            return isset($array[$keys]) ? $array[$keys] : $keys;
        }
    }

    public static function removeEmpty($linksArray) {
        return array_filter($linksArray, function ($value) {
            return !is_null($value) && $value !== '';
        });
    }

    public static function getLabelsCase($column, $array) {
        $ret = [];
        foreach ($array as $k => $v) {
            $ret[$k] = 'when ' . $column . "='" . $k . "' then '$v' ";
        }
        return new \yii\db\Expression('case ' . implode('', $ret) . ' end');
    }

    public static function getMysqlReplace($columnName, array $find, $subtitute) {
        $ret = $columnName;
        $ret = "replaceAll($ret,'[\"" . implode('","', $find) . "\"]','$subtitute')";

        return $ret;
    }

    public static function getMysqlParseRemark($columnName, $asexpression = 1) {
        $ret = "SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX($columnName, '\n',1),'][',-2),'][',1)";
        if ($asexpression)
            return new \yii\db\Expression($ret);
        else
            return $ret;
    }

    public static function getMysqlAddress($alias, $asexpression = 1) {
        $ret = "ifnull(json_unquote(json_extract($alias.billing_address,'$.addr')),$alias.installation_address)";
        if ($asexpression)
            return new \yii\db\Expression($ret);
        else
            return $ret;
    }

    public static function getMysqlCustName($alias, $asexpression = 1) {
        $ret = "replace(trim(replace(concat(ifnull($alias.fname,''),' ',ifnull($alias.mname,''),' ', ifnull($alias.lname,'')),' ','')),'\n','')";
        if ($asexpression)
            return new \yii\db\Expression($ret);
        else
            return $ret;
    }

    public static function getJsonExtract($column, $key, $default = '', $asexpression = 1) {
        $ret = "ifnull(jsonExtract($column,'$key'),'$default')";
        if ($asexpression)
            return new \yii\db\Expression($ret);
        else
            return $ret;
    }

    static function getKeyIfExists($key, $array, $default = null) {
        if (!is_array($array)) {
            $array = json_decode($array, true);
        }
        if ((is_string($key) || is_int($key) ) && is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $default;
        }
    }

    static function isCisco($id) {
        $cac = \Yii::$app->cache->get('CISCO_IDS');
        if (!$cac) {
            $cac = array_keys(\app\models\cas\Casvendor::find()->andWhere(['cas_type' => CAS_CISCO])->select(['id'])->indexBy('id')->asArray()->all());
            \Yii::$app->cache->set('CISCO_IDS', $cac, strtotime('+1year'));
        }
        return $cac;
    }

    static function castInt($inp) {
        if (is_null($inp)) {
            return null;
        }
        if (is_array($inp)) {
            $ret = [];
            foreach ($inp as $k => $v) {
                $ret[$k] = self::castInt($v);
            }
            return array_values($ret);
        } else {
            return intval(trim($inp, "=\n\r%"));
        }
    }

    static function castIntandMongoId($inp, $dual = false) {
        if (is_null($inp)) {
            return null;
        }
        if (is_array($inp)) {
            $ret = [];
            foreach ($inp as $k => $v) {
                $arr = self::castIntandMongoId($v, $dual);
                $ret = array_merge($ret, $arr);
            }
            return $ret;
        } else {
            if (is_numeric($inp)) {
                $ret[$inp . 'int'] = intval($inp);
                if (preg_match('/^[a-f\d]{24}$/i', $inp)) {
                    $ret[$inp . 'mg'] = new \MongoDB\BSON\ObjectId($inp);
                }
            } else {
                if (preg_match('/^[a-f\d]{24}$/i', $inp)) {
                    $ret[$inp . 'mg'] = new \MongoDB\BSON\ObjectId($inp);
                }
                $ret[$inp . 'str'] = $inp;
                return $ret;
            }
        }
    }

    static function castString($inp) {

        if (is_array($inp)) {
            $ret = [];
            foreach ($inp as $k => $v) {
                $ret[$k] = self::castString($v);
            }
            return $ret;
        } else {
            return (string) $inp;
        }
    }

    static function castStringInt($inp, $asArray = false) {

        if (is_array($inp)) {
            $ret = [];
            foreach ($inp as $k => $v) {
                $ret[] = self::castInt($v);
                $ret[] = self::castString($v);
            }
            return $asArray ? (array) $ret : $ret;
        } else {
            return self::castStringInt((array) $inp);
        }
    }

    static function transformArraytoIdName($array, $sort = 1) {
        $ret = [];
        if ($sort == 1) {
            ksort($array);
        } else
        if ($sort == 2) {
            asort($array);
        }
        foreach ($array as $id => $name) {
            $ret[] = ['id' => $id, 'name' => $name];
        }
        return $ret;
    }

    static function transToAssocIdname($array) {
        $ret = [];

        foreach ($array as $name) {
            $ret[] = ['id' => $name, 'name' => $name];
        }
        return $ret;
    }

    static function transformArraytoIdNamedfromAssoc($array, $idLabel = 'id', $nameLabel = 'name') {
        $ret = [];

        foreach ($array as $data) {

            $ret[] = ['id' => $data[$idLabel], 'name' => $data[$nameLabel]];
        }
        return $ret;
    }

    public static function getStringInt($data) {
        if (is_array($data)) {
            $ret = [];
            foreach ($data as $d) {
                $ret[] = intval($d);
                $ret[] = (string) $d;
            }
        } else {
            return [intval($data), (string) $data];
        }
    }

    public static function is1DIndexed($array, $consecutive = false) {
        if (static::isIndexed($array, $consecutive)) {
            if (static::isTraversable(current($array))) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public static function isIntegerOr1dArray($array) {
        if (static::isNonEmpty($array)) {
            if (is_numeric($array)) {
                return true;
            } elseif (static::is1DIndexed($array)) {
                return true;
            }
        }
        return false;
    }

    public static function trim($v, $n = '') {
        if (is_array($v)) {
            $return = [];
            foreach ($v as $k => $val) {
                $return[$k] = static::trim($val);
            }
            return $return;
        } else {
            return trim($v, $n);
        }
    }

    public static function ifEmpty($d, $r) {
        return $d ? $d : $r;
    }

    public static function isNonEmpty($v) {
        $v = static::trim($v);
        if (!empty($v)) {
            return true;
        } else {
            if (is_null($v)) {
                return false;
            }
            if ($v == '') {
                return false;
            }
            if (is_array($v) && empty($v)) {
                return false;
            }
        }
        return true;
    }

    public static function isZero($v) {
        return self::isNonEmpty($v) && $v == '0';
    }

    public static function isStringOr1dArray($array) {
//        var_dump($array);exit;

        if (static::isNonEmpty($array)) {
            if (is_string($array)) {
                return true;
            } elseif (static::is1DIndexed($array)) {
                return true;
            }
        }
        return false;
    }

    public static function compareArrayKeys($source, $validationArray) {
        $vkeys = array_keys($validationArray);
        $skeys = array_keys($source);
        return array_diff($vkeys, $skeys);
    }

    public static function getValues($labelArr, $keysArr) {
        $return = [];
        foreach ($keysArr as $k) {
            if (isset($labelArr[$k])) {
                $return[$k] = $labelArr[$k];
            }
        }
        return $return;
    }

    public static function getLowerValue($currentKey, $sortedArr, $diff = 1) {
        if ($currentKey >= 0) {
            $k = array_search($currentKey, $sortedArr);
            if ($k !== false) {
                return isset($arr[$k - $diff]) ? $arr[$k - $diff] : self::getLowerValue($currentKey - 1, $sortedArr, $diff);
            } else {
                return self::getLowerValue($currentKey - 1, $sortedArr, 0);
            }
        } else {
            return false;
        }
    }

    public static function appendGetParams($k, $v, $glue = ',') {
        if (isset(\Yii::$app->request->queryParams[$k])) {
            if (is_array(\Yii::$app->request->queryParams[$k])) {
                $_GET[$k][] = $v;
            } else {
                $_GET[$k] .= $glue . $v;
            }
        } else {
            $_GET[$k] = $v;
        }
    }

    public static function setGetParams($k, $v) {
        $_GET[$k] = $v;
    }

    public static function isClosure($t) {
        return is_object($t) && ($t instanceof \Closure);
    }

    public static function print_attr($t, $ret = false) {
        if (self::isIndexed($t)) {
            $return = [];
            foreach ($t as $d) {
                $return[] = self::print_attr($d, 1);
            }
            print_r($return, $ret);
        } else {
            if ($t instanceof \components\models\BaseModel || $t instanceof \components\models\BaseMongoModel) {
                if ($ret === 1) {
                    return $t->attributes;
                } else {
                    print_r($t->attributes, $ret);
                }
            } else {
                if ($ret === 1) {
                    return $t;
                } else {
                    print_r($t, $ret);
                }
            }
        }
    }

    public static function getAttributeLabel($attr) {
        return ucwords(str_replace('_', ' ', str_replace('_id', '', $attr)));
    }

    static function _filter($array, $filters, $multi = false) {
        $return = [];

        if ($multi) {
            foreach ($array as $k => $a) {
                $return[$k] = static::filter($a, $filters);
            }
        } else {
            $_filter = array_values($filters);
            $data = parent::filter($array, $filters);
            foreach ($filters as $k => $v) {
                if (isset($data[$v])) {
                    $_k = is_int($k) ? $v : $k;
                    $return[$_k] = $data[$v];
                }
            }
        }
        return $return;
    }

    public static function removeDualSpaces($subject, $search = '  ', $replace = ' ') {
        $subject = str_replace($search, $replace, $subject);
        if (strpos($subject, $search) !== false && $search != $replace) {
            $subject = static::removeDualSpaces($subject, $search, $replace);
        }
        return $subject;
    }

    public static function joinExcept($TwoDarray, $exceptIndex = null) {
        if (isset($TwoDarray[$exceptIndex])) {
            unset($TwoDarray[$exceptIndex]);
        }
        $ret = [];
        foreach ($TwoDarray as $a) {
            $ret = array_merge($ret, $a);
        }
        return array_values(array_unique($ret));
    }

    public static function concat(...$params) {
        $ret = [];
        foreach ($params as $p) {
            if (is_array($p)) {
                $ret[] = implode(' ', $p);
            } else {
                $ret[] = $p;
            }
        }
        return trim(implode(' ', $ret), PHP_EOL);
    }

    public static function concat_d($delimiter, ...$params) {
        $ret = [];
        foreach ($params as $p) {
            if (is_array($p)) {
                $ret[] = self::concat_d($delimiter, ...$p);
            } else {
                if ($delimiter !== false) {
                    $ret[] = $delimiter . ' ' . $p;
                } else {
                    $ret[] = $p;
                }
            }
        }
        if ($delimiter !== false) {
            return trim(implode($delimiter, $ret), PHP_EOL);
        } else {
            return $ret;
        }
    }

    public static function appendArray(Array $array, ...$params) {
        foreach ($params as $p) {
            $array[] = $p;
        }
        return $array;
    }

    public static function getOverlappingDetails($base2DArray, $compareArray) {
        $ret = [];
        $interect_all = [];
        foreach ($base2DArray as $bid => $arr) {
            $interect = array_intersect($arr, $compareArray);
            if ($interect > 0) {
                $interect_all = array_unique(array_merge($interect_all, $interect));
                $ret[$bid] = $interect;
            }
        }
        return [$ret, $interect_all];
    }

    static function filterAttributes($array, $kv = [], $kbNull = [], $extra = []) {
        $ret = [];
        foreach ($kv as $k => $a) {
            if (is_callable($a)) {
                $key = $k;
                $ret[$key] = $a($array);
            } else {
                $key = is_int($k) ? $a : $k;

                $ifNull = isset($kbNull[$key]) ? $kbNull[$key] : null;
                if (strpos($a, '.') === false) {
                    $ret[is_int($k) ? $a : $k] = isset($array[$a]) && !is_null($array[$a]) ? $array[$a] : $ifNull;
                } else {
                    $keys = explode('.', $a);
//                \components\helper\Utils::l($keys );
                    $base = $array;
                    foreach ($keys as $_k) {
//                    if(property_exists($base, $_k))
                        $base = is_array($base) && array_key_exists($_k, $base) ? (!is_null($base[$_k]) ? $base[$_k] : $ifNull) : null;
                    }
                    $ret[is_int($k) ? $a : $k] = !is_null($base) ? $base : $ifNull;
                }
            }
        }
        if ($extra) {
            foreach ($extra as $k => $v) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    static function toMongoId($ar) {
        if (!is_array($ar)) {
            $ar = (array) $ar;
        }
        $return = [];
        foreach ($ar as $v) {
            $return[] = new \MongoDB\BSON\ObjectID($v);
        }
        return $return;
    }

    static function searchArray(array $array, array $condition): array {
        $foundItems = array();

        foreach ($array as $item) {
            $find = TRUE;
            foreach ($condition as $key => $value) {
                if (isset($item[$key]) && $item[$key] == $value) {
                    $find = TRUE;
                } else {
                    $find = FALSE;
                }
            }
            if ($find) {
                array_push($foundItems, $item);
            }
        }
        return $foundItems;
    }

    static function filterMultiInQuery($column, $data, $like = true, $mongo = false) {
        if ($mongo) {
            $condition = (new \yii\db\Query);
        } else {
            $condition = (new \yii\mongodb\Query);
        }
        $sc = (array) $data;
        $_scno = [];
        $orlike = [];
        $data = (array) $data;
        foreach ($data as $scno) {
            $kl = $mongo ? \components\models\BaseMongoQuery::isHasLike($scno) : \components\models\BaseQuery::isHasLike($scno);
            if ($like && $kl !== false) {
                if ($mongo) {
                    $condition->Orwhere(['like', $column, \components\models\BaseMongoQuery::exactMatch($scno), \components\models\BaseMongoQuery::isLike($scno)]);
                } else {
                    $_condition = (new \yii\db\Query);
                    $_condition->Orwhere(['like', $column, \components\models\BaseQuery::exactMatch($scno), \components\models\BaseQuery::isLike($scno)]);
                    $sql = $_condition->createCommand()->getRawSql();
                    $orlike[] = substr($sql, stripos($sql, 'WHERE') + 6);
                }
            } else {
                $_scno[] = \components\models\BaseQuery::exactMatch($scno);
            }
        }
        if (!empty($orlike)) {
            $condition->where(implode(' or ', $orlike));
        }
        if (!empty($_scno)) {
            $condition->Orwhere([$column => $_scno]);
        }
        return $condition->where;
    }

    static function recursive_implode(array $array, $glue = ',', $include_keys = false, $trim_all = true) {
        $glued_string = '';

        // Recursively iterates array and adds key/value to glued string
        array_walk_recursive($array, function ($value, $key) use ($glue, $include_keys, &$glued_string) {
            $include_keys and $glued_string .= $key . $glue;
            $glued_string .= $value . $glue;
        });

        // Removes last $glue from string
        strlen($glue) > 0 and $glued_string = substr($glued_string, 0, -strlen($glue));

        // Trim ALL whitespace
        $trim_all and $glued_string = preg_replace("/(\s)/ixsm", '', $glued_string);

        return (string) $glued_string;
    }

    static function implode($input, $delimiter = ',') {
        if (is_array($input)) {
            return self::recursive_implode($input, $delimiter);
        } else {
            return $input;
        }
    }

    static function replaceMacro($input, $macro, $replace) {
        if (self::isTraversable($input)) {
            $ret = [];
            foreach ($input as $k => $v) {
                $ret[$k] = self::replaceMacro($v, $macro, $replace);
            }
            return $ret;
        } else {
            return str_replace($macro, $replace, $input);
        }
    }

    static function sampling($chars, $size, $combinations = array()) {

        # if it's the first iteration, the first set 
        # of combinations is the same as the set of characters

        if (empty($combinations)) {
            $combinations = $chars;
        }
        array_shift($chars);
        # we're done if we're at size 1
        if ($size == 1) {
            return $combinations;
        }

        # initialise array to put new values in
        $new_combinations = array();

        $charUsed = [];
        # loop through existing combinations and character set to create strings
        foreach ($combinations as $combination) {
            foreach ($chars as $char) {
                if ($combination != $char) {
                    $k = $combination . $char;
                    $kr = $char . $combination;
                    if (!isset($charUsed[$kr]) && !isset($charUsed[$kr])) {
                        $new_combinations[] = [$combination, $char];
                        $charUsed[$kr] = 1;
                        $charUsed[$k] = 1;
                    }
                }
            }
        }

        # call same function again for the next iteration
        return self::sampling($chars, $size - 1, $new_combinations);
    }

    static function parseRawJson($str) {
        preg_match_all("/([^,:]+):([^,:]+)/", $str, $r);
        $result = array_combine($r[1], $r[2]);
        return $result;
//        $l = explode(',', $str);
//        $ret = [];
//        foreach ($l as $e) {
//            list($k, $v) = explode(':', $e);
//            $ret[trim($k)] = trim($v);
//        }
//        return $ret;
    }

    static function json_decode($str) {
        if (is_string($str)) {
            return json_decode($str, true);
        } else {
            return $str;
        }
    }

    static function json_encode($str) {
        if (!is_string($str)) {
            return json_encode($str);
        } else {
            return $str;
        }
    }

    static function indexToAssoc($arr) {
        $ret = [];
        foreach ($arr as $k => $v) {
            if (is_int($k)) {
                $ret[$v] = $v;
            } else {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    static function mysqlMac($column) {
        if (CISCO_MAC_EXPORT) {
            return "sctomac($column)";
        } else {
            return $column;
        }
    }

    static function mysqlAddQuote($column, $isDownload = 1, $Asexp = 1, $mac = 0) {
        if ($mac) {
            $column = self::mysqlMac($column);
        }
        if ($isDownload) {
            $return = "addQuote(cast($column as varchar)";
        } else {
            return $column;
        }
        if ($Asexp) {
            return new \yii\db\Expression($return);
        } else {
            return $return;
        }
    }

    static function mysqlDaysLeft($endcolumn, $startcolumn, $Asexp = 1) {
        $daysmnth = \components\Constants::DAYS_IN_MONTH;

        $return = "daysLeft($endcolumn,$startcolumn,0)";
        if ($Asexp) {
            return new \yii\db\Expression($return);
        } else {
            return $return;
        }
    }

    static function mysqlExpiryDate($endcolumn, $startcolumn, $Asexp = 1) {
        $daysmnth = \components\Constants::DAYS_IN_MONTH;
        ;

        $return = "case when $endcolumn=$startcolumn then $endcolumn else DATE_SUB($endcolumn,INTERVAL 1 DAY) end";
        if ($Asexp) {
            return new \yii\db\Expression($return);
        } else {
            return $return;
        }
    }

    static function mysqlInterval($endcolumn, $startcolumn, $Asexp = 1) {

        $return = "getInterval($endcolumn,$startcolumn,0)";
        if ($Asexp) {
            return new \yii\db\Expression($return);
        } else {
            return $return;
        }
    }

    static function addQuote($str, $add = false, $isCisco = false) {
        if ($add) {
            if (CISCO_MAC_EXPORT && $isCisco) {
                if ($isCisco === true && preg_match('/^[0-9A-Fa-f]{12}$/', $str)) {
                    return \components\cas\cisco\AbstractCardCommand::convertToMac($str);
                } elseif (self::isCisco($isCisco) && preg_match('/^[0-9A-Fa-f]{12}$/', $str)) {
                    return \components\cas\cisco\AbstractCardCommand::convertToMac($str);
                }
            }
            if (preg_match('/^[0-9]+$/', $str)) {
                return "'" . $str;
            }
            if (is_numeric($str)) {
                return "'" . $str;
            }
        }
        return $str;
    }

    static function getClassName($class) {
        $path = explode('\\', $class);
        return array_pop($path);
    }

    static function getYearPartition($startYear, $ahead = 1) {
        $current = date('Y');
        $endYear = $current + $ahead;
        $ret = '';
        for ($i = $startYear; $i <= $endYear; $i++) {
            $pt = $i - 1;
            $ret .= "PARTITION p$pt VALUES LESS THAN ($i),";
        }
        $ret .= 'PARTITION pmax VALUES LESS THAN MAXVALUE ';
        return $ret;
    }

    static function ignoreNonUtf8($text) {
        if (is_array($text)) {
            $return = [];
            foreach ($text as $k => $v) {
                $return[$k] = self::ignoreNonUtf8($v);
            }
            return $return;
        } else {
            return @iconv("utf-8", "utf-8//ignore", $text);
        }
    }

}
