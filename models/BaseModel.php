<?php

namespace app\models;

class BaseModel extends \yii\db\ActiveRecord {

    /** @inheritdoc */
    public function behaviors() {
        // TimestampBehavior also provides a method named touch() that allows you to assign the current timestamp to the specified attribute(s) and save them to the database. For example,
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_by',
                'updatedAtAttribute' => 'updated_by',
                'value' => function () {
                    return User::getCurrentUserId();
                }
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
        return \yii\helpers\ArrayHelper::merge(parent::extraFields(),
                        [
                            "created_by_lbl" => !empty($this->updatedBy) ? $this->updatedBy->name :
                            !empty($this->updatedBy) ? $this->updatedBy->name : ""
                        ],
                        [
                            "created_at_lbl" => !empty($this->updated_at) ? $this->updated_at : (!empty($this->created_at) ? $this->created_at : "")
                        ]
        );
    }

    public function fields() {
        return \yii\helpers\ArrayHelper::merge(parent::fields(), array_keys($this->attributes));
    }

    
}
