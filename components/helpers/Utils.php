<?php

namespace app\components\helpers;

use DateTime;
use \MongoDB\BSON\UTCDateTime as MD;

class Utils {

    //    const DEBUG=false;
    const DEBUG = true;

    private $_lock = [];

    public static function map($f, $objects) {
        if (sizeof($objects) == 0)
            return [];
        $result = [];
        foreach ($objects as $o) {
            $result[] = $f($o);
        }
        return $result;
    }

    /**
     * get time in UTC format
     * picked from https://stackoverflow.com/questions/17909871/getting-date-format-m-d-y-his-u-from-milliseconds
     * TODO: make zones switchable
     */
    public static function getTimeStamp($format = null) {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        if (isset($format))
            return $d->format($format);
        return $d; //->format("Y-m-d H:i:s.u"); // note at point on "u"
    }

    public static function getMongoTimeStamp($d = null) {
        if ($d === null) {
            $d = static::getTimeStamp();
        }
        return new MD($d ? $d->format('Uv') : null);
    }

    /**
     * Log messages javascript console.log style
     */
    public static function l() {
        $level = \Yii::$app->variable->get('printlevel', 0);
        $args = func_get_args();
        $clevel = isset($args['level']) ? $args['level'] : null;

        $print = self::DEBUG && \app\models\ViAccess::isConsole();
        if ($print || YII_DEBUG) {
            $args = []; //func_get_args();
            // $t = microtime(true);
            // $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d = '[' . static::getTimeStamp("Y-m-d\TH:i:s.u") . ']::';
            // $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            $args[] = $d;
            $args = array_merge($args, func_get_args());
            $args[] = "[from " . $caller['file'] . "(" . $caller['line'] . ")]";
            // $args[]='time:'.$d->format("Y-m-d H:i:s.u");
            // $args[]='time:'.date_create()->format('Y-m-d H:i:s');
            if ($print) {
                foreach ($args as $o) {
                    if (is_bool($o)) {
                        echo $o ? "__TRUE__" : "__FALSE__";
                    } else {
                        print_r($o);
                    }
                    echo " ";
                }
                echo "\n";
            } else {
                \Yii::debug($args, 'Utils::l');
            }
        }
    }

    /**
     * Picks objects from assoc $array with keys in $values
     *
     */
    public static function array_pick($array, $values) {
        // return array_filter($array,
        //                     function($v,$k)use($values){
        //                         return array_search($k,$values)!==false;
        //                     },
        //                     ARRAY_FILTER_USE_BOTH
        // );
        $picked = [];
        foreach ($values as $value) {
            if (isset($array[$value]))
                $picked[$value] = $array[$value];
        }
        return $picked;
    }

    /**
     * Halt processing and wait for an event on key
     * 
     */
    public function h($k) {
        $this->h[$k] = true;
        return $this->h[$k];
    }

    public static function snakeToCamel($str) {
        return preg_replace_callback('/_(.?)/', function ($matches) {
            return ucfirst($matches[1]);
        }, $str);
    }

    /**
     * TODO:WARNING: currently only supports two variables 
     * thisThat => this_that;
     * thisThatThere => this_thatthere
     */
    public static function camelToSnake($str) {
        // return preg_replace_callback('|[A-Z](.+)|', function($matches) {
        //     print_r($matches);
        //     return '_'.strtolower($matches[0]);
        // }, $str);
        $strA = explode('_', $str);
        return array_shift($strA) . implode(array_map('ucwords', $strA));
    }

    /**
     * Resume
     *
     */
    public static function r() {
        
    }

    /**
     * break
     *
     */
    public static function _break() {
        
    }

    public static function isAssoc($arr) {
        return ArrayHelper::isAssociative($arr);
//        if (array() === $arr)
//            return false;
//        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function getDBCase($array = [], $column) {
        //        new \yii\db\Expression("case isHD when 1 then 'HD' else 'SD' end")
        $case = [];
        $params = [];
        foreach ($array as $k => $v) {
            $case[] = ':K' . $k . ' then :V' . $k;
            $params[':K' . $k] = $k;
            $params[':V' . $k] = $v;
        }
        return new \yii\db\Expression("case [[$column]] when " . implode(' when ', $case) . ' else null end', $params);
    }

    public static function getOTP() {
        return rand(100000, 999999);
    }

    static function getFormatedException($ex, $eatr = 'ERROR:') {
//        if (YII_DEBUG) {
//        Throw $ex;
        $bt = debug_backtrace();
        $caller = array_shift($bt);
//        print_r($ex->getTrace());
        $args = [$eatr, "date" => date('c'), 'code' => "code:" . $ex->getCode(), 'message' => $ex->getMessage(), 'errfile' => $ex->getFile(), 'trace' => $ex->getTraceAsString(), 'at line' => $ex->getLine()];
        $args['called_from'] = "from " . $caller['file'] . "(" . $caller['line'] . ")";
        FileHelper::log('@runtime/multi_jobs_errors.log', '[' . implode('][', $args) . ']', true);
        return [$ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getTraceAsString()];
//        } else {
//            return $ex->getMessage();
//        }
    }

    public static function dechex($value, $bytes = 1, $pad = '') {
//        echo 'V='.$value."\n";
        $bytes *= 2; //this is because of char encoding where 2 chars=1 byte
        return sprintf('%0' . $bytes . 'X', $value);
    }

    public static function binhex($value, $bytes = 1, $unit = 2) {
//        echo 'V='.$value."\n";
        $bytes *= $unit; //this is because of char encoding where 2 chars=1 byte
        return sprintf('%0' . $bytes . 'X', bindec($value));
    }

    public static function strpad($value, $bytes = 1) {
        return str_pad($value, $bytes * 2, "0", STR_PAD_LEFT);
    }

    public static function decbin($value, $bits = 1) {
        return sprintf('%0' . $bits . 'd', decbin($value));
    }

    public static function maskMobileNo($number) {
        $number = preg_replace("/[^\d]/", "", $number);

        // get number length.
        $length = strlen($number);
        // if number = 10
        if ($length == 10) {
            $number = preg_replace("/^1?(\d{2})(\d{4})(\d{4})$/", "$1XXXX$3", $number);
        } else {
            $number = preg_replace("/^1?(\d{4})(\d{4})(\d{4})$/", "$1XXXX$3", $number);
        }

        return $number;
    }

    public static function OtpCacheKey($mobileno, $type) {
        \Yii::debug(['OTP_' . $type . $mobileno], 'KEY_OTP_');
        return md5('OTP_' . $type . $mobileno);
    }

    public static function generateOtp($mobileno, $type, $ttl, &$expiry = null) {
        $cacheKey = self::otpCacheKey($mobileno, $type);
        $cacheOtp = \Yii::$app->cache->get($cacheKey);
        if ($cacheOtp) {
            $otp = $cacheOtp;
        } else {
            $otp = \components\helper\Utils::getOTP();
        }
        $expiry = date('d M h:i A', strtotime('+' . $ttl . ' seconds'));
        \Yii::$app->cache->set($cacheKey, $otp, $ttl);
        \Yii::debug([$cacheKey, $otp], '_SET_OTP_');

        return $otp;
    }

    public static function sendOtp($category, $subcategory, $mobileno, $user_id, $replaceText, $data = [], $advanceCache = false, $ttl = OTP_TTL_SEC, &$message = '') {
        //get the randon umber for OTP
        //save the OTP for specific time
        $t = '';
        if ($advanceCache) {
            $t = $category . $subcategory;
        }
        $expiry = '';
        $otp = \components\helper\Utils::generateOtp($mobileno, $t, $ttl, $expiry);

        //prepare message tobe send to user
        $replaceTextArray = \yii\helpers\ArrayHelper::merge(['<otp>' => $otp, '<expiry>' => $expiry], $replaceText);
        $message = \app\components\MessageText::getSMSText($category, $subcategory, $replaceTextArray);
        $data = array_merge($data, ['id' => $user_id, 'mobileno' => $mobileno, 'message' => $message, 'type' => $subcategory, 'category' => $category]);

        return (new \app\components\SendSMS())->send($data);
    }

    public static function validateOtp($mobileno, $otp, $type = '', $drop = true, &$found = false) {
        if (DEFAULT_OTP && DEFAULT_OTP == $otp) {
            return true;
        }
        $cacheKey = self::otpCacheKey($mobileno, $type);
        $cacheOtp = \Yii::$app->cache->get($cacheKey);
        \Yii::debug([$cacheKey, $cacheOtp, $otp], '_GET_OTP_');
        if ($cacheOtp) {
            $found = true;
        }
        if (!empty($cacheOtp) && ($cacheOtp == $otp)) {
            if ($drop) {
                \Yii::$app->cache->delete($cacheKey);
            }
            return TRUE;
        }
        return FALSE;
    }

    public static function numberFormat($v, $decimal = 2) {
        return round($v, $decimal);
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function getFinancialYear($date, $short = false) {
        $month = date("m", strtotime($date));
        $year1 = $short ? date("y", strtotime($date)) : date("Y", strtotime($date));
        if ($month > 3) {
            $year = $short ? $year1 . ($year1 + 1) : $year1 . "-" . ($year1 + 1);
        } else {
            $year = $short ? ($year1 - 1) . $year1 : ($year1 - 1) . "-" . $year1;
        }
        return $year; // 2015-2016
    }

    public static function getActivePaymentGateWay($status = \app\models\Status::ACTIVE) {
        $pd = \app\models\ConfigMaster::find()
                        ->where(['name' => \components\Constants::PAYMENT_MODES])
                        ->isActive($status)->indexBy('attribute')->asArray()->all();
        if (!empty($pd)) {
            return \Yii::$app->cache->getOrSet('pycnf1', function () use ($pd) {
                        return \yii\helpers\ArrayHelper::map($pd, 'attribute', function ($a) {
                            $jsn = json_decode($a['value'], 1);
                            $d = ["class" => $jsn['class'], "url" => $jsn['url'], 'id' => $a['id'], 'cond' => $a['conditions']];
                            $d["track_url"] = !empty($jsn['track_url']) ? $jsn['track_url'] : "";
                            $d['param'] = array_diff($jsn, $d);
                            return $d;
                        });
                    });
        }
        return [];
    }

    public static function getCurl($url, $data, $headers = [], $method = "post") {
        $ch = curl_init();
        self::l("URL", trim($url));
        self::l("URL", $headers);
        curl_setopt($ch, CURLOPT_URL, trim($url));
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // define what you want to post
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
        if (defined('EFFI_SSLVERSION')) {
            curl_setopt($ch, CURLOPT_SSLVERSION, EFFI_SSLVERSION);
        }

        if (empty($headers)) {
            $headers = ['Content-Type: application/json'];
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch); // execute
        $info = curl_getinfo($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if (!empty($err)) {
            self::l($err);
        }
        return $output;
    }

    static function getOperatorHierarchy($fromId, $toId) {
        $from_operator_ids = !is_array($fromId) ? array_reverse(explode("-", $fromId)) : $fromId;
        $operator = \app\models\Operator::findOne(['id' => $toId]);
        $to_operator_ids = [];
        if ($operator instanceof \app\models\Operator) {
            if (EXCLUDE_DB_FROM_SHIFT_CHALLAN && $operator->billed_by == USER_TYPE_MSO) {
                $to_operator_ids = [$operator->mso_id,
                    !empty($operator->branch) ? $operator->branch->id : 0,
                    $operator->id
                ];
            } else {
                $to_operator_ids = [$operator->mso_id,
                    !empty($operator->branch) ? $operator->branch->id : 0,
                    !empty($operator->distributor) ? $operator->distributor->id : 0,
                    $operator->id
                ];
            }
        } else {
            $to_operator_ids = [$toId];
        }

        $uniqueid = current(array_intersect($from_operator_ids, $to_operator_ids));

        return \yii\helpers\ArrayHelper::merge(
                        array_slice($from_operator_ids, 0, array_search($uniqueid, $from_operator_ids) + 1), array_slice($to_operator_ids, array_search($uniqueid, $to_operator_ids) + 1)
        );
    }

    static function generateChallan($from_id, $to_id, $stockList) {
        $operator = \app\models\Operator::find()->where(['id' => [$from_id, $to_id]])->indexBy('id')->all();
        $fromOperator = $operator[$from_id];
        $toOperator = $operator[$to_id];

        $model = new \app\models\mongo\Challan(['scenario' => \app\models\mongo\Challan::SCENARIO_CREATE]);
        $model->po_date = date("Y-m-d");
        $model->description = "Subscriber Shifting from $fromOperator->name to $toOperator->name";
        $model->status = \app\models\mongo\Challan::STATUS_DONE;
        $model->from_warehouse_id = !empty($fromOperator->warehouse) ? $fromOperator->warehouse[0]['id'] : 0;
        $model->to_warehouse_id = !empty($toOperator->warehouse) ? $toOperator->warehouse[0]['id'] : 0;
        $model->flow_type = \app\models\mongo\Challan::FLOW_TYPE_SHIFTING;
        $model->from_operator_id = $fromOperator->id;
        $model->to_operator_id = $toOperator->id;
        $model->scheme_id = 0;
        $model->amount = 0;
        $model->quantity = count($stockList);
        $model->tax = 0;
        $model->allot_type = \app\models\mongo\Challan::ALLOT_TYPE_FREE;
        $model->total = 0;
        $model->stock_type = \app\models\mongo\Challan::STOCK_PAIRING;
        $model->stock_list = $stockList;
        $model->stock_details = $model->getStockDetails();
        $model->po_id = 0;
        $model->uploadQ_id = 0;
        $model->expireAt = 1;
        if ($model->validate() && $model->save()) {
            $accounts = \app\models\subscriber\SubscriberAccount::find()
                            ->where(['smartcardno' => $stockList])->all();

            foreach ($accounts as $account) {
                $data = [
                    'from_warehouse_id' => $model->from_warehouse_id,
                    'to_warehouse_id' => $model->to_warehouse_id,
                    'scheme_id' => $model->scheme_id,
                    'from_operator_id' => $model->from_operator_id,
                    'to_operator_id' => $model->to_operator_id,
                    'smartcardno' => $account->smartcardno,
                    'stbno' => $account->stbno,
                    'operator_id' => $model->flow_type == \app\models\mongo\Challan::FLOW_TYPE_DEALLOT ? $model->from_operator_id : $model->to_operator_id,
                    'brand_id' => $account->stb->stbbrand->id,
                    'brand' => $account->stb->stbbrand->name,
                    'state' => $account->stb->state,
                    'amount' => 0,
                    'tax' => 0,
                    'transaction_type' => $model->flow_type == \app\models\mongo\Challan::FLOW_TYPE_ALLOT ? 0 : 1,
                    'po_id' => $model->_id,
                    'po_number' => $model->po_number,
                    'po_date' => date("Y-m-d")
                ];
                $challanObj = new \app\models\ChallanDetailsList(['scenario' => \app\models\ChallanDetailsList::SCENARIO_CREATE]);
                $challanObj->load($data);

                if ($challanObj->validate() && $challanObj->save()) {
                    $extra_macros = [
                        "{scno}" => $account->smartcardno,
                        "{stbno}" => $account->stbno,
                        "{from_lco}" => $fromOperator->name,
                        "{to_lco}" => $toOperator->name,
                        "{po_number}" => $model->po_number,
                        'doc_number' => $model->po_number,
                        'return_doc_number' => $model->po_number_received,
                        'from_operator' => !empty($fromOperator) ? $fromOperator->name . "(" . $fromOperator->code . ")" : null,
                        'to_operator' => !empty($toOperator) ? $toOperator->name . "(" . $toOperator->code . ")" : null
                    ];
                    $event = \app\models\inventory\TrackScStb::ChallanSubscriberShifting;
                    \app\models\inventory\TrackScStb::saveTrack($account, $event
                            , \app\models\ViAccess::getCurrentUsername(), null, false, $extra_macros);
                }
            }
        }
    }

    static function grnShiftChallan($data) {
        $to_operator_id = !empty($data['to']) ? $data['to'] : 0;
        $resp = [];
        $tmpPoId = time();
        if (isset($data['from'])) {
            foreach ($data['from'] as $option => $stock) {
                $operatorList = \components\helper\Utils::getOperatorHierarchy($option, $to_operator_id);

                if (!empty($operatorList)) {
                    for ($i = 0; $i < count($operatorList) - 1; $i++) {
                        self::generateGrnChallan($operatorList[$i], $operatorList[$i + 1], $stock, $resp, $tmpPoId);
                    }
                    $qd = new \stdClass();
                    $qd->_id = $tmpPoId;
                    \components\challan\Chabs::genChallan($qd, "grn_subscriber_shifting", $tmpPoId);
                }
            }
        }
    }

    static function generateGrnChallan($from_operator_id, $to_operator_id, $smcs, &$resp, $tmpPoId) {
        $operator = \app\models\Operator::find()->where(['id' => [$from_operator_id, $to_operator_id]])
                        ->indexBy('id')->all();

        $fromOperator = $operator[$from_operator_id];
        $toOperator = $operator[$to_operator_id];

        if (empty($resp[$toOperator->type])) {
            $subQuery = \app\models\ChallanDetailsList::find()
                            ->select(['from_operator_id', 'to_operator_id', 'smartcardno', "created_at" => 'max(created_at)'])
                            ->where([
                                'from_operator_id' => $to_operator_id, 'to_operator_id' => $from_operator_id,
                                'smartcardno' => $smcs
                            ])->groupBy(['from_operator_id', 'to_operator_id', 'smartcardno']);

            $challanD = \app\models\ChallanDetailsList::find()->alias('a')
                    ->innerJoin(['m' => $subQuery], 'm.from_operator_id=a.from_operator_id and m.to_operator_id and m.created_at=a.created_at and m.smartcardno=a.smartcardno')
                    ->all();
            if (!empty($challanD)) {
                foreach ($challanD as $key => $model) {
                    $data = [
                        'from_warehouse_id' => $model->to_warehouse_id,
                        'to_warehouse_id' => $model->from_warehouse_id,
                        'scheme_id' => $model->scheme_id,
                        'from_operator_id' => $model->to_operator_id,
                        'to_operator_id' => $model->from_operator_id,
                        'smartcardno' => $model->smartcardno,
                        'stbno' => $model->stbno,
                        'operator_id' => $model->to_operator_id,
                        'brand_id' => $model->brand_id,
                        'brand' => $model->brand,
                        'state' => $model->state,
                        'amount' => $model->amount,
                        'tax' => $model->tax,
                        'transaction_type' => 1,
                        'po_id' => $tmpPoId,
                        'po_number' => "grn_subscriber_shifting",
                        'po_date' => date("Y-m-d"),
                        "allot_type" => !empty($model->allot_type) ? strrev($model->allot_type) : $fromOperator->type . $toOperator->type,
                        "flow_type" => \components\challan\Chabs::FLOW_TYPE_DEALLOT,
                        "trans_type" => $model->trans_type,
                        "uploadQ_id" => $tmpPoId,
                        "stock_type" => \app\models\mongo\Challan::STOCK_PAIRING
                    ];

                    $challanObj = new \app\models\ChallanDetailsList(['scenario' => \app\models\ChallanDetailsList::SCENARIO_CREATE]);
                    $challanObj->load($data);
                    if ($challanObj->validate() && $challanObj->save()) {
                        $resp[$fromOperator->type] = [
                            'amount' => $model->amount,
                            "tax" => $model->tax,
                            "scheme_id" => $model->scheme_id,
                            "stock_type" => $data["stock_type"],
                            "trans_type" => $model->trans_type
                        ];
                    }
                }
            }
        } else {
            $accounts = \app\models\subscriber\SubscriberAccount::find()->where(['smartcardno' => $smcs])->all();
            foreach ($accounts as $account) {
                $data = [
                    'from_warehouse_id' => !empty($fromOperator->warehouse) ? $fromOperator->warehouse[0]['id'] : 0,
                    'to_warehouse_id' => !empty($toOperator->warehouse) ? $toOperator->warehouse[0]['id'] : 0,
                    'scheme_id' => $resp[$toOperator->type]['scheme_id'],
                    'from_operator_id' => $fromOperator->id,
                    'to_operator_id' => $toOperator->id,
                    'smartcardno' => $account->smartcardno,
                    'stbno' => $account->stbno,
                    'operator_id' => $toOperator->id,
                    'brand_id' => $account->stb->stbbrand->id,
                    'brand' => $account->stb->stbbrand->name,
                    'state' => $account->stb->state,
                    'amount' => $resp[$toOperator->type]['amount'],
                    'tax' => $resp[$toOperator->type]['tax'],
                    'transaction_type' => 0,
                    'po_id' => $tmpPoId,
                    'po_number' => "grn_subscriber_shifting",
                    'po_date' => date("Y-m-d"),
                    "allot_type" => $fromOperator->type . $toOperator->type,
                    "flow_type" => \components\challan\Chabs::FLOW_TYPE_ALLOT,
                    "trans_type" => $resp[$toOperator->type]['trans_type'],
                    "uploadQ_id" => $tmpPoId,
                    "stock_type" => \app\models\mongo\Challan::STOCK_PAIRING
                ];

                $challanObj = new \app\models\ChallanDetailsList(['scenario' => \app\models\ChallanDetailsList::SCENARIO_CREATE]);
                $challanObj->load($data);
                if ($challanObj->validate()) {
                    $challanObj->save();
                }
            }
        }
    }

    static function ShiftingChallan($data) {
        $to_operator_id = !empty($data['to']) ? $data['to'] : 0;
        if (isset($data['from'])) {
            foreach ($data['from'] as $option => $stock) {
                $operatorList = \components\helper\Utils::getOperatorHierarchy($option, $to_operator_id);
                if (!empty($operatorList)) {
                    for ($i = 0; $i < count($operatorList) - 1; $i++) {
                        self::generateChallan($operatorList[$i], $operatorList[$i + 1], $stock);
                    }
                }
            }
        }
    }

    public static function sendSMS($category, $subcategory, $mobileno, $user_id, $replaceText = []) {
        //prepare message tobe send to user
        $message = \app\components\MessageText::getSMSText($category, $subcategory, $replaceText);
        $data = ['id' => $user_id, 'mobileno' => $mobileno, 'message' => $message, 'type' => $subcategory, 'category' => $category];
        return (new \app\components\SendSMS())->send($data);
    }

    public static function validateMetaData($meta_array, $columns) {
        $validate = true;
        foreach ($columns as $column => $rule) {
            if (!empty($rule['isRequired'])) {
                $validate = $validate && !empty(\yii\helpers\ArrayHelper::getColumn($meta_array, $column));
            }
            if (!empty($rule['type_check'])) {
                $data = \yii\helpers\ArrayHelper::getColumn($meta_array, $column);
                $validate = $validate && array_walk($data, function (&$items, $key, $rules) {
                            list($column, $condition) = $rules;

                            $dynaminModel = (new \yii\base\DynamicModel([$column]))
                                    ->addRule([$column], ($condition['isRequired']) ? 'required' : 'safe')
                                    ->addRule([$column], 'each', ['rule' => [$condition['type_check']]]);

                            $dynaminModel->$column = $items;
                            return $dynaminModel->validate();
                        }, [$column, $rule]);
            }
        }
        return $validate;
    }

    public static function sendBackupMail($subject, $body, $to, array $attachment = null) {
        $ob = \Yii::$app->effi_mailer->compose()
                ->setTo($to)
                ->setSubject($subject)
                ->setTextBody($body);

        if ($attachment) {
            foreach ($attachment as $f) {
                $ob->attach($f);
            }
        }
        return $ob->send();
    }

    public static function composeMails($template, $data, $to, $subject, $cc = null, $type = 'TICKET_EMAIL', $force = false) {
        if (TICKET_EMAIL || $force) {
            try {
                $d = \Yii::$app->mailer->compose('@mail_template/' . $template, $data)
//                    ->setFrom(FROM_EMAIL)
                        ->setTo($to)
                        ->setSubject($subject);
                if (!is_null($cc)) {
                    if ($cc) {
                        $d->setCc($cc);
                    }
                } else {
                    if (EMAIL_CC && isset(EMAIL_CC[$type]) && !empty(EMAIL_CC[$type])) {
                        $d->setCc(EMAIL_CC[$type]);
                    }
                }
                if (EMAIL_BCC && isset(EMAIL_BCC[$type]) && !empty(EMAIL_BCC[$type])) {
                    $d->setBcc(EMAIL_BCC[$type]);
                }
                $ret = $d->send();
                self::l($to, "TOO ");
                self::l($d->getFrom(), "FROM_EMAIL");
                self::l($d->getTo(), "TO_EMAIL");
                self::l($d->getCc(), "CC_EMAIL");
                self::l($subject, "SUB_EMAIL");
                self::l($ret, "TICKET_EMAIL");
                self::l($ret);
            } catch (\Exception $ex) {
                return $ex->getMessage();
            }
            return $ret;
//            print_r($d);
//            echo "mailesends";
        } else {
            return 'NOT ENABLED';
        }
    }

    public static function getExtraPaymentConfig($operator_id, $attr, $type) {
        $conf = \app\models\ConfigMaster::find()->andWhere(["name" => "PAYMENT_SETTING", "attribute" => $attr])->isActive()->one();
        if ($conf instanceof \app\models\ConfigMaster) {
            if (in_array($operator_id, $conf->value['operator_id']) && $conf->value['setting_for'] == $type) {
                return $conf->value['settings'];
            }
        }
        return [];
    }

    public static function generatePaymentOrderId(\components\models\BaseModel $obj) {
        $opId = $obj instanceof \app\models\subscriber\SubscriberAccount ? $obj->operator_id : $obj->id;
        $operator = \app\models\Operator::findOne($opId);
        $prefix = $opId;
        if ($operator instanceof \app\models\Operator) {
            $prefix = $operator->code;
        }
        $seq = \app\models\MongoSequence::getNextId('OnlinePaymentRecietNo_');
        $fy = \components\helper\Utils::getFinancialYear(date("Y-m-d"), true);
        return $prefix . "-" . $seq;
    }

    public static function grnValidation($fromOperator_id, $toOperator_id, $smcs) {
        if (!GRN_RETURN_GOODS) {
            return TRUE;
        }
        $cop = \app\models\Operator::findOne(['id' => $fromOperator_id]);
        $rt = [];
        if (EXCLUDE_DB_FROM_SHIFT_CHALLAN && $cop->billed_by == USER_TYPE_MSO) {
            $rt = [$cop->mso_id,
                !empty($cop->branch_id) ? $cop->branch_id : 0,
                $cop->id
            ];
        } else {
            $rt = [$cop->mso_id,
                !empty($cop->branch_id) ? $cop->branch_id : 0,
                !empty($cop->distributor_id) ? $cop->distributor_id : 0,
                $cop->id
            ];
        }

        $data = $resp = [];
        $fromOperators = array_reverse($rt);

        $subQuery = \app\models\ChallanDetailsList::find()
                        ->select(['from_operator_id', 'to_operator_id', 'smartcardno', "created_at" => 'max(created_at)'])
                        ->where([
                            'from_operator_id' => $fromOperators, 'to_operator_id' => $fromOperators,
                            'smartcardno' => $smcs
                        ])->groupBy(['from_operator_id', 'to_operator_id', 'smartcardno']);

        $model = \app\models\ChallanDetailsList::find()->alias('a')
                ->innerJoin(['m' => $subQuery], 'm.from_operator_id=a.from_operator_id and m.to_operator_id and m.created_at=a.created_at and m.smartcardno=a.smartcardno')
                ->all();
        if (!empty($model)) {
            foreach ($model as $key => $val) {
                if (empty($data[$val->toOperator->type])) {
                    $data[$val->toOperator->type] = 0;
                }
                $data[$val->toOperator->type] += ($val->amount + $val->tax);
            }
        }



        if (!empty($data)) {
            $is_valid = true;
            $toOperator = \app\models\Operator::findOne(['id' => $toOperator_id]);
            if ($toOperator instanceof \app\models\Operator) {
                $optlst = [
                    \app\models\ViAccess::USER_TYPE_LCO => $toOperator->id,
                    \app\models\ViAccess::USER_TYPE_DISTRIBUTOR => $toOperator->distributor_id,
                    \app\models\ViAccess::USER_TYPE_BRANCH => $toOperator->branch_id
                ];

                if ($cop->distributor_id == $toOperator->distributor_id) {
                    unset($data[\app\models\ViAccess::USER_TYPE_DISTRIBUTOR]);
                }

                foreach ($optlst as $opt_type => $opt_id) {
                    if (!empty($data[$opt_type])) {
                        $fromOpt = \app\models\Operator::findOne(['id' => $opt_id]);
                        if ($fromOpt instanceof \app\models\Operator) {
                            $is_valid = $is_valid && $fromOpt->isOperatorBalanceAvailable($data[$opt_type], \components\Constants::HARDWARE_WALLET);
                        }
                    }
                }
            }
            return $is_valid;
        } else {
            return TRUE;
        }
    }

}
