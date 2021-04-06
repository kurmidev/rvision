<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use app\traits\ModelTrait;
use app\traits\ValidationTrait;
use app\traits\CacheTrait;
use yii\behaviors\BlameableBehavior;
use yii\db\BaseActiveRecord;
use app\components\Constants as C;

class BaseModel extends \yii\db\ActiveRecord {

    use ModelTrait;
    use ValidationTrait;
    use CacheTrait;

    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_CONSOLE = 'console';
    const SCENARIO_UPDATE = 'update';

    public $thisalias = null;

    /** @inheritdoc */
    public function behaviors() {
        return [
//            [
//                'class' => BlameableBehavior::className(),
//                'createdByAttribute' => 'created_by',
//                'updatedByAttribute' => 'updated_by',
//                "value" => function () {
//                    return ViAccess::getCurrentUserId();
//                }
//            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_by',
                'updatedAtAttribute' => 'updated_by',
                'value' => function () {
                    return ViAccess::getCurrentUserId();
                }
            ],
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_on'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => 'updated_on',
                ],
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ]
        ];
    }

    public function getCreatedBy() {
        return $this->hasOne(ViAccess::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy() {
        return $this->hasOne(ViAccess::class, ['id' => 'updated_by']);
    }

    public function extraFields() {
        $fields = parent::extraFields();
        $fields['created_by_lbl'] = function () {
            return !empty($this->updatedBy) ? $this->updatedBy->name :
            !empty($this->updatedBy) ? $this->updatedBy->name : "";
        };

        $fields['created_on_lbl'] = function () {
            return !empty($this->updated_on) ? date("Y-m-d H:i:s", strtotime($this->updated_on)) :
            (!empty($this->created_on) ? date("Y-m-d H:i:s", strtotime($this->created_on)) : "");
        };

        if ($this->hasAttribute('status')) {
            $fields['status_lbl'] = function () {
                return !empty(C::LABEL_STATUS[$this->status]) ? C::LABEL_STATUS[$this->status] : $this->status;
            };
        }

        return $fields;
    }

    public function fields() {
        return \yii\helpers\ArrayHelper::merge(parent::fields(), array_keys($this->attributes));
    }

    public static function getClassName() {
        $path = explode('\\', static::className());
        return array_pop($path);
    }

    public static function getQueryClass() {
        return static::className() . 'Query';
    }

    public static function aliasKey() {
        return 'ALIAS_' . static::tableName();
    }

    public static function alias() {
        $alias = \Yii::$app->variable->get(self::aliasKey());
        if ($alias) {
            return $alias;
        }
        return static::tableName();
    }

    public static function setAlias($alias) {
        $alias = \Yii::$app->variable->set(self::aliasKey(), $alias);
    }

    public static function find() {
        $qClass = static::getQueryClass();
        return (new $qClass(get_called_class()))->applyCache()->setAlias(static::alias());
    }

    public function generateCode($prefix, $is_fy = false) {
        return CodeSequence::genCode($prefix, $is_fy);
    }

}
