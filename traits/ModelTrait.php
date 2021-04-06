<?php

namespace app\traits;

use app\models\User;
use app\models\Operator;
use \yii\helpers\Url;
use app\components\helpers\Utils as U;

trait ModelTrait {

    public $validated;
    public $_report_name;
    public $_srno = 1;
    public $where = null;

    function getReportName() {
        if ($this->_report_name) {
            return $this->_report_name;
        } else {
            $this->_report_name = \Yii::$app->variable->get('_report_name');
        }
        return $this->_report_name;
    }

    function getAttributeLabels() {
        $al = \Yii::$app->variable->get('_attribute_label', null);
        return $al;
    }

    function setReportName($rname) {
        \Yii::$app->variable->set('_report_name', $rname);
        $this->_report_name = $rname;
    }

    function printLog($data) {
        if (\app\models\ViAccess::isConsole()) {
            U::l($data);
        }
    }

    function getReportFields() {
        if ($ret = $this->getAttributeLabels()) {
            return array_keys($ret);
        }
        return '';
    }

    function getReportExtraFields() {
        return '';
    }

    function get_Srno() {
        return ['class' => 'yii2tech\csvgrid\SerialColumn',];
    }

    function filterAttributes($attr, $kv = [], $seperator = '->', $default = null) {
        $ret = [];
        foreach ($attr as $k => $a) {
            if (is_array($a)) {
                $ret[$k] = $this->filterAttributes($a, [], $seperator, $default);
            } else {
                if (is_array($default)) {
                    $_default = isset($default[is_int($k) ? $a : $k]) ? $default[is_int($k) ? $a : $k] : null;
                } else {
                    $_default = $default;
                }
                if (strpos($a, $seperator) === false) {
                    if ($this instanceof \yii\base\BaseObject && isset($this->$a))
                        $ret[is_int($k) ? $a : $k] = $this->$a;
                    else
                        $ret[is_int($k) ? $a : $k] = $_default;
                } else {
                    $keys = explode($seperator, $a);
//               U::l($keys );
                    $base = $this;
                    foreach ($keys as $_k) {
                        if ($base instanceof \yii\base\BaseObject && isset($base->$_k))
                            $base = $base->$_k;
                        else
                            $base = $_default;
                    }
                    $ret[is_int($k) ? $a : $k] = $base;
                }
            }
        }
        if ($kv) {
            foreach ($kv as $k => $v) {
                $ret[$k] = $v;
            }
        }
//        exit;
        return $ret;
    }

    function filterOperatorAssoc($assocClass, &$query, $not = false) {

        if (ViAccess::getUserType() > ViAccess::USER_TYPE_MSO || $this->operator_id) {
            if ($this->operator_id) {
                $opId = $this->operator_id;
            } else {
                if ($not) {
                    return;
                }
                $opId = ViAccess::getUserOperatorId();
            }
//            echo $opId;exit;
            $parentId = Operator::find()->getParentId($opId);
            $inactive = [];
            if ($this->all) {
                if ($this instanceof \app\models\broadcaster\BouqueSearch) {
                    $inactive = array_keys(\app\models\broadcaster\Bouque::find()->select(['id'])->andWhere(['status' => \app\models\Status::DISABLED])->indexBy('id')->all());
//                    print_r($inactive);exit;
                }
            }
            if (Operator::find()->isChildOperator($opId)) {
                if ($opId) {
                    $currentAssignedIds = $assocClass::find()->getAssignedToOperator($opId);

//                    \Yii::debug($currentAssignedIds, '__$currentAssignedIds __');
                    if ($currentAssignedIds !== true) {

                        $currentAssignedIds = array_keys($currentAssignedIds);
                        if (!empty($currentAssignedIds) && !empty($inactive)) {
                            $currentAssignedIds = array_merge($currentAssignedIds, $inactive);
                        }
//                        print_r($currentAssignedIds);exit;
                        if ($not) {
                            if (!empty($currentAssignedIds)) {
                                $query->andFilterWhere(['not', ['id' => $currentAssignedIds]]);
                            }
                        } else {
                            if (!empty($currentAssignedIds)) {
                                $query->andWhere(['id' => $currentAssignedIds]);
                            } else {
                                $query->where('0=4');
                            }
                        }
                    }
                }
                //else {
                if ($not) {
                    $assignedIds = $assocClass::find()->getAssignedToOperator($parentId);
                    if ($assignedIds !== true) {
                        if (is_array($assignedIds) && !empty($assignedIds)) {
                            $assignedIds = array_keys($assignedIds);
                            if (!empty($inactive)) {
                                $assignedIds = array_merge($assignedIds, $inactive);
                            }

                            $query->andWhere(['id' => $assignedIds]);
                        } else {
                            $query->where('0=2');
                        }
                    }
                }
//                }
            } else {
                $query->where('0=3');
            }
        }
    }

    function searchJson($attribute, &$query, $not = '') {
        if (!is_array($this->$attribute) && !empty($this->$attribute)) {
            $attributeValue = [$this->$attribute];
        } else {
            $attributeValue = $this->$attribute;
        }
        if (!empty($attributeValue)) {
            $condition = new \yii\db\Query;
            foreach ($attributeValue as $i => $v) {
                if (!empty($v)) {
                    $condition->orWhere(new \yii\db\Expression("JSON_CONTAINS($attribute, :$attribute$i)", [":$attribute$i" => $v]));
                }
            }
//           U::l($condition->where);exit;
            if ($not) {
                if (is_array($condition->where)) {
                    $query->andFilterWhere(['not', $condition->where]);
                } else {
                    $query->andFilterWhere(['not', $condition->where]);
                }
            } else {
                if (is_array($condition->where)) {
                    $query->andFilterWhere($condition->where);
                } else {
                    $query->andFilterWhere(['or', $condition->where]);
                }
            }
        }
    }

    function defaultSearchSettings($key, $overWriteData) {
        $def = [
            'cas_id' => ['data' => Url::toRoute('casvendor/list', true), 'type' => 'lookup', 'label' => 'CAS', 'multi' => true],
            'bouque_id' => ['data' => Url::toRoute('bouque/list', true), 'type' => 'lookup', 'label' => 'Bouquet', 'multi' => true],
            'operator_id' => ['data' => Url::toRoute('operator/list', true), 'type' => 'lookup', 'label' => 'Operator', 'multi' => true],
            'created_by' => ['data' => Url::toRoute('user/list', true), 'type' => 'lookup', 'label' => 'Created By', 'multi' => true],
            'updated_by' => ['data' => Url::toRoute('user/list', true), 'type' => 'lookup', 'label' => 'Updated By', 'multi' => true],
            'created_at' => ['data' => null, 'type' => 'date_range', 'label' => 'Added On', 'not' => false],
            'updated_at' => ['data' => null, 'type' => 'date_range', 'label' => 'Added On', 'not' => false],
            'scheme_id' => ['data' => Url::toRoute('scheme/list', true), 'type' => 'lookup', 'label' => 'Scheme', 'multi' => true],
        ];
        return array_key_exists($key, $def) ? array_merge($def[$key], $overWriteData) : false;
    }

    function searchSetting($token = null) {
        $attr = $this->attributes();
        $labels = $this->attributeLabels();
        $ret = [];
        foreach ($attr as $a) {
            if (strpos($a, 'TO') === false && strpos($a, 'FRM') === false && $a != 'id') {
                $label = isset($labels[$a]) ? $labels[$a] : \components\helper\ArrayHelper::getAttributeLabel($a);
                $dS = $this->defaultSearchSettings($a, ['label' => $label]);

                $ret[$a] = $dS ? $dS : [
                    'data' => $a == 'status' ? \app\modules\v1\controllers\ListController::getStatus() : (strpos($a, '_by') ? \yii\helpers\Url::toRoute('user/list', true) : null),
                    'type' => $a == 'status' ? 'enum' : (strpos($a, '_at') ? 'date' : (strpos($a, '_by') ? 'lookup' : 'string')),
                    'label' => isset($labels[$a]) ? $labels[$a] : \components\helper\ArrayHelper::getAttributeLabel($a)
                ];
            }
        }
        return $ret;
    }

    public static function ifNullDb($column, $else) {
        return "ifnull($column,$else)";
    }

    function defaultWith() {
        return [];
    }

    static function extraFieldsWithConf() {

        return [];
    }

    static function getParams($k, $d = '') {
        if (\app\models\ViAccess::isConsole()) {
            $fileds = \Yii::$app->variable->get('_get', []);
            return $fileds && array_key_exists($k, $fileds) ? $fileds[$k] : $d;
        } else {
            $fileds = \Yii::$app->variable->get('_get', []);

            return \Yii::$app->request->get($k, ($fileds && array_key_exists($k, $fileds) && !empty($fileds[$k])) ? $fileds[$k] : $d);
        }
    }

    static function getParamIsset($k) {

        if (\app\models\ViAccess::isConsole()) {
            $fileds = \Yii::$app->variable->get('_get', []);
//            var_dump($fileds);exit;
            return $fileds && array_key_exists($k, $fileds) && !empty($fileds[$k]);
        } else {
            $fileds = \Yii::$app->variable->get('_get', []);
            return isset(\Yii::$app->request->get()[$k]) || ($fileds && array_key_exists($k, $fileds) && !empty($fileds[$k]));
        }
    }

    /**
     * get lazy loading with 
     */
    public function getSearchWith() {
        return static::getSearchWithStatic($this->defaultWith());
    }

    public static function getSearchWithStatic($dwith = []) {
        $with = $dwith; //['mso', 'branch', 'distributor', 'createdby', 'updatedby']

        if (self::getParamIsset('all')) {
            foreach (static::extraFieldsWithConf() as $v) {
                if (is_array($v)) {
                    foreach ($v as $k) {
                        $with[$k] = $k;
                    }
                } else {
                    $with[$v] = $v;
                }
            }
        } else {
            if (self::getParamIsset('expand')) {
                $ex = self::getParams('expand');
                if (is_array($ex)) {
                    $expand = $ex;
                } else {
                    $expand = explode(',', self::getParams('expand'));
                }
//           U::l($expand);
//            exit;
                if ($expand) {
                    $withConf = static::extraFieldsWithConf();
//                    print_r($expand);
//                    print_r($withConf);
//                    exit;
                    foreach ($expand as $l) {
                        if (array_key_exists($l, $withConf)) {
                            if (is_array($withConf[$l])) {
                                foreach ($withConf[$l] as $k) {
                                    $with[$k] = $k;
                                }
                            } else {
                                $with[$withConf[$l]] = $withConf[$l];
                            }
                        }
                    }
                }
            }
            if (self::getParamIsset('extra_with')) {
                $expand = (array) self::getParams('extra_with');
//           U::l($expand);
//            exit;
                if ($expand) {
                    foreach ($expand as $l) {
                        if (isset($withConf[$l])) {
                            if (is_array($withConf[$l])) {
                                foreach ($withConf[$l] as $k) {
                                    $with[$k] = $k;
                                }
                            } else {
                                $with[$withConf[$l]] = $withConf[$l];
                            }
                        }
                    }
                }
            }
        }
        $with = array_unique(array_values($with));
//        \Yii::debug(['WITH JOIN', $with], 'WITH JOIN');

        U::l('WITH JOIN', $with);
//        exit;
        return $with;
    }

    public function afterValidate() {
        $this->validated = true;
        parent::afterValidate();
    }

    public function save($runValidation = true, $attributeNames = null) {

        $return = parent::save($this->validated ? false : $runValidation, $attributeNames);
        if ($return && !$this->hasErrors()) {
            return true;
        } else {
            return false;
        }
    }

    static function getReportNameAppendFilter() {
        return [];
    }

    function getFields($fields) {
        if ($rf = $this->getReportFields()) {
            $extra = $this->extraFields();
            $_fields = [];

            foreach ($rf as $k) {
//                if (strpos($k, '.') !== false) {
//                    $k = str_replace('.', '_', $k);
//                }
                if (array_key_exists($k, $fields)) {
                    $_fields[$k] = $fields[$k];
                } else if (array_key_exists($k, $extra)) {
                    $_fields[$k] = $extra[$k];
                } else if (in_array($k, $fields)) {
                    $_fields[$k] = $k;
                }
            }
//            U::l($rf);
//            print_r(array_keys($_fields));
//            exit;
            return $_fields;
        } else {
            return $fields;
        }
    }

    function getFilterExtraFields($extra) {
        if ($rf = $this->getReportFields()) {
            $_fields = [];
            foreach ($rf as $k) {
                if (array_key_exists($k, $extra)) {
                    $_fields[$k] = $extra[$k];
                }
            }
            return $_fields;
        } else {
            return $extra;
        }
    }

    public function attributeLabels() {
        if ($ret = $this->getAttributeLabels()) {
            return $ret;
        }
        return [];
    }

    function getIsExport() {
        return \Yii::$app->variable->get('isExport') == 1;
    }

    function addQuote($str, $add = false, $isCisco = false) {
        return \components\helper\ArrayHelper::addQuote($str, $add || $this->isExport, $isCisco);
    }

    function searchOrAlais(&$query) {
//        print_r($this->_alias);
//        exit;
        $mongo = $this instanceof \yii\mongodb\ActiveRecord;
//        print_r($this);exit;
        if ($this->_alias) {
            $condition = (new \yii\db\Query);
            foreach ($this->_alias as $k => $v) {
                if (!empty($this->$k) && empty($this->$v)) {
                    $val = $this->$k;
                    $this->$k = null;
                    $condition->Orwhere(\components\helper\ArrayHelper::filterMultiInQuery($query->alias . $k, $val, true, $mongo));
                    $condition->Orwhere(\components\helper\ArrayHelper::filterMultiInQuery($query->alias . $v, $val, true, $mongo));
                    //$condition->Orwhere(['like', $query->alias . $k, self::exactMatch($val), self::isLike($val)]);
                    //$condition->Orwhere(['like', $query->alias . $v, self::exactMatch($val), self::isLike($val)]);
                }
            }
//            print_r($condition->where);exit;
            if (!empty($condition->where)) {
                $query->andWhere($condition->where);
            }
//            print_r($query->where);exit;
        }
    }

    static function getmonthlyName($name, $delimiter = '_', $min_month_for_name = MIN_MONTH_FOR_NAME) {

        $month = \Yii::$app->variable->get('c_report_month');
        if (empty($month)) {
            $month = date('Ym');
        }
        $month = substr($month, 0, 6) + 0;
        \Yii::debug([$month, $min_month_for_name], '__MONYJA');
        if ($month >= $min_month_for_name) {
            $isMonthTable = true;
            return $name . $delimiter . $month;
        } else {
            $isMonthTable = false;
            return $name;
        }
    }

    static function isMonthlyTable($month, $var, $min_month_for_name = MIN_MONTH_FOR_NAME) {

        $month = \Yii::$app->variable->get('c_report_month');
        if (empty($month)) {
            $month = date('Ym');
        }
        $month = substr($month, 0, 6) + 0;
        if ($month >= $min_month_for_name) {
            $r = true;
        } else {
            $r = false;
        }
        \Yii::$app->variable->set($var, $r ? 1 : 0);
    }

    static function setMonth($month) {
        \Yii::$app->variable->set('c_report_month', $month);
    }

    static function getdailyName($name, $delimiter = '_') {

        $month = \Yii::$app->variable->get('c_report_date');
        if (empty($month)) {
            $month = date('Ymd');
        }
        return $name . $delimiter . $month;
    }

    static function setDate($month) {
        \Yii::$app->variable->set('c_report_date', $month);
    }

    static function getTableSplitDate($collectionname) {
        $p = \Yii::$app->params['split_tables_dates'];
        return isset($p[$collectionname]) ? $p[$collectionname] : false;
    }

}
