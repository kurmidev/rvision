<?php

namespace app\traits;

use Yii;
use components\helper\DateHelper;

trait ValidationTrait {

    public function testValidation($attr, $params) {
        $this->addError($attr, 'INvalid');
    }

    public function compositerequired($attr, $params) {
        $this->addError($attr, $params['message']);
        $valid = false;
        foreach ($params['oneOf'] as $required)
            if ($this->$required !== null && trim($this->$required) !== '')
                $valid = true;

        if (!$valid) {
            $this->addError($attr, $params['message']);
            foreach ($params['oneOf'] as $required)
                $this->addError($required, 'Err');
        }
    }

    public function customValidator($attribute, $params) {
        $funct = $params['function'];
        $message = isset($params['message']) ? $params['message'] : 'Invalid value for {attribute}';
        
        if ($funct($this->$attribute, $params) == false) {
            $this->addError($attribute, str_replace('{attribute}', $attribute, $message));
        }
    }

    public function is1Darray($attribute, $params) {
        $message = isset($params['message']) ? $params['message'] : 'Invalid value for {attribute} 1D array expected';
        if (!\components\helper\ArrayHelper::is1DIndexed($this->$attribute)) {
            $this->addError($attribute, str_replace('{attribute}', $attribute, $message));
        }
    }

    public function isIntor1Darray($attribute, $params) {
        $message = isset($params['message']) ? $params['message'] : 'Invalid value for {attribute} Integer or 1D array expected';
        if (!\components\helper\ArrayHelper::is1DIndexed($this->$attribute) && !is_numeric($this->$attribute)) {
            $this->addError($attribute, str_replace('{attribute}', $attribute, $message));
        }
    }

    public function customUnique($attribute, $params) {
        $message = isset($params['message']) ? $params['message'] : "$attribute already exists for {id}";
        $idLabel = isset($params['id']) ? $params['id'] : "id";
        $onlyWhenValueChanged = array_key_exists('onlyWhenValueChanged', $params) ? $params['onlyWhenValueChanged'] : false;
        $glue = isset($params['glue']) ? $params['glue'] : "/";
        $targetClass = isset($params['targetClass']) ? $params['targetClass'] : self::className();
        $check = true;
        if ($onlyWhenValueChanged && $this->isNewRecord == false) {
            $check = false;
            $dirty = $this->getDirtyAttributes();
            if (array_key_exists($attribute, $dirty) && $dirty[$attribute] != $this->$attribute) {
                $check = true;
            }
        }
        if ($check) {
            \components\helper\Utils::l('queries', $targetClass::find()->where([$attribute => $this->$attribute])->andFilterWhere(['not', ['id' => $this->id]])->rawSql);
            $model = $targetClass::find()->where([$attribute => $this->$attribute])->andFilterWhere(['not', ['id' => $this->id]])->one();
            if ($model) {
                $id = '';
                if (is_array($idLabel)) {
                    foreach ($idLabel as $k => $v) {
                        $message = str_replace($k, $model->$v, $message);
                        $id .= $model->$v . $glue;
                    }
                    $id = trim($id, $glue);
                } else {
                    $id = $model->$idLabel;
                }
                $msg = str_replace(['{attribute}', '{value}', '{id}'], [$attribute, $this->$attribute, $id], $message);
                $this->addError($attribute, $msg);
            }
        }
    }

    public function compareDate($attribute, $params) {
        $compareValue = isset($params['compareValue']) ? $params['compareValue'] : false;
        $min = isset($params['min']) ? $params['min'] : false;
        $max = isset($params['max']) ? $params['max'] : false;
        $compareOperator = isset($params['compareOperator']) ? $params['compareOperator'] : '=';
        $message = isset($params['message']) ? $params['message'] : false;
        $input = DateHelper::formatDate($this->$attribute);
        $failed = false;
        if ($compareValue) {
            if (!DateHelper::compareDate($input, $compareValue, $compareOperator)) {
                $message = $message ? $message : "{attribute} need to be $compareOperator $compareValue";
                $failed = true;
            }
        } else {
            if ($min && $max) {
                if (!DateHelper::compareDate($input, $min, '>=') || !DateHelper::compareDate($input, $max, '<=')) {
                    $message = $message ? $message : "{attribute} need to be between $min and  $max";
                    $failed = true;
                }
            } else
            if ($min) {
                if (!DateHelper::compareDate($input, $min, '>=')) {
                    $message = $message ? $message : "{attribute} need to be >= $min";
                    $failed = true;
                }
//                exit;
            } else
            if ($max) {
                if (!DateHelper::compareDate($input, $max, '')) {
                    $message = $message ? $message : "{attribute} need to be >= $max";
                    $failed = true;
                }
            }
        }

        if ($failed) {
            $this->addError($attribute, str_replace(['{attribute}', '{value}'], [$attribute, $this->$attribute], $message));
        }
    }

    public function ValidateDynamicModel($attribute, $params) {
        $input = $this->$attribute;
        $isMulti = isset($params['isMulti']) ? $params['isMulti'] : false;

        if (!empty($input)) {
            if ($isMulti) {
                if (!\components\helper\ArrayHelper::isIndexed($input)) {
                    $this->addError($attribute, "$attribute need to be an array of objects");
                    return;
                }
            }
            if (!\components\helper\ArrayHelper::isTraversable($input)) {
                $this->addError($attribute, "$attribute need to be an array");
                return;
            }
            $ValidationModel = isset($params['ValidationModel']) ? $params['ValidationModel'] : false;
            /* $ValidationModel = \yii\base\DynamicModel::validateData($format_keys, [
              [['name', 'email'], 'string', 'max' => 128],
              ['email', 'email'],
              ]); */
            if ($ValidationModel) {
                if (!($ValidationModel instanceof \yii\base\DynamicModel)) {
                    $this->addError($attribute, "Invalid ValidationModel passed has to be instance of \yii\base\DynamicModel");
                    return;
                }
            } else {
                $this->addError($attribute, "Invalid ValidationModel passed has to be instance of \yii\base\DynamicModel");
                return;
            }
            $format = $ValidationModel->attributes();

            if (!$isMulti) {
                $input = [$input];
            }
            foreach ($input as $i => $data) {
                $model = $ValidationModel;
                if (is_array($data)) {
//                    $missingKeys = array_diff($format,array_keys($data));
                    $missingKeys = [];
                    if (!empty($missingKeys)) {
                        $this->addError($attribute, "Folowing keys missing " . implode(',', $missingKeys) . ' at ' . $i . " index of $attribute input");
                        return false;
                    } else {
                        if ($model) {
                            $model->load($data, '');
                            if (!$model->validate()) {
                                $this->addError($attribute, $model->errors);
                                return false;
                            }
                        }
                    }
                } else {
                    $this->addError($attribute, "Has to be any array of objects with keys " . implode(',', $format));
                }
            }
        } else {
            if (isset($params['allowEmpty']) && $params['allowEmpty'] == false) {

                $this->addError($attribute, "Has to be any array of objects  cannot be Empty");
            }
        }
    }

    public function customValidatorIsSubset($attribute, $params) {

        if (isset($params['function'])) {
            $funct = $params['function'];
            $message = isset($params['message']) ? $params['message'] : 'Invalid value for {attribute}';
            if ($funct($this->$attribute, $params) == false) {
                $this->addError($attribute, str_replace('{attribute}', $attribute, $message));
            }
        }
        $message = isset($params['message']) ? $params['message'] : 'Out of1 range input for {attribute}';
        if (!\yii\helpers\ArrayHelper::isTraversable($this->$attribute)) {
            $this->addError($attribute, str_replace('{attribute}', $attribute, $message . ' Array input expected'));
        } else
        if (\components\helper\ArrayHelper::isSubset($this->$attribute, $params['range']) == false) {
            $this->addError($attribute, str_replace('{attribute}', $attribute, $message));
        }
    }

    function validateLogo($attribute, $param) {

        $arryFormat = ['name', 'type', 'ext', 'data'];
        $optional = [];
        if (isset($param['optional'])) {
            $optional = $param['optional'];
        }
        $extra_required = [];
        if (isset($param['extra_required'])) {
            $extra_required = $param['extra_required'];
        }
        sort($arryFormat);
        if (!\components\helper\ArrayHelper::isTraversable($this->$attribute)) {
            $this->addError($attribute, \Yii::t('app', 'Needs to be an array'));
        } else
        if (!empty($this->$attribute)) {
            $data = $this->$attribute;
            $keys = array_keys($data);
            sort($keys);
            if ($keys != $arryFormat) {
                $this->addError($attribute, Yii::t('app', 'Invalid ' . $attribute . ' Json keys ' . implode(',', $arryFormat)));
            } else {
                if (isset($param['extension'])) {
                    if (!in_array($data['ext'], $param['extension'])) {
                        $this->addError($attribute, Yii::t('app', 'Invalid ' . $attribute . ' Extension only ' . implode(',', $param['extension']) . " are allowed"));
                    }
                }
                foreach ($data as $k => $v) {
                    if (!in_array($k, $optional) && empty($v)) {
                        $this->addError($attribute, Yii::t('app', 'Invalid ' . $attribute . ' ' . $k));
                    }
                }
            }
            if ($extra_required) {
                foreach ($extra_required as $k => $in) {
                    if (!isset($data[$k])) {
                        $this->addError($attribute, Yii::t('app', $k . ' is required in ' . $attribute));
                    } elseif (!in_array($data[$k], $in)) {
                        $this->addError($attribute, Yii::t('app', 'Invalid ' . $k . ' value in ' . $attribute));
                    }
                }
            }
        }
    }

    public function validateStbBrands($attribute, $params) {
        $allowEmpty = isset($params['allowEmpty']) && $params['allowEmpty'] == true ? true : FALSE;

        if ($this->$attribute !== false) {
            if (!\components\helper\ArrayHelper::is1DIndexed($this->$attribute)) {
                $this->addError($attribute, $attribute . ' Needs to an 1d array');
                return;
            }
            $count = count($this->$attribute);
        } else {
            if (!$allowEmpty) {
                $this->addError($attribute, 'Please select atleast one brand');
            }
            return;
        }
        if ($count > 0) {
            $ids = $this->$attribute;
            foreach ($ids as $id) {
                $model = \app\models\inventory\Brand::find()->select(['id', 'isHD', 'name'])->getSTB()->andWhere(['id' => $id])->asArray()->one();
                if (empty($model)) {
                    $this->addError($attribute, "Invalid Brand ID ($id) posted");
                } elseif ($this->isHD != $model['isHD'] && $model['isHD'] == 0) {
                    $brandHD = $model['isHD'] == 1 ? 'SD StbBrand cannot be added HD ' . \components\Constants::BouqueLabel : 'SD StbBrand cannot be added HD ' . \components\Constants::BouqueLabel;
                    $this->addError($attribute, "Invalid StbBrand ID (" . $id . "-" . $model['name'] . "]) $brandHD");
                }
            }
        }
    }

}
