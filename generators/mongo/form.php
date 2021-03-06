<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\mongodb\gii\model\Generator */

echo $form->field($generator, 'collectionName');
echo $form->field($generator, 'databaseName');
echo $form->field($generator, 'attributeList');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');

echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
echo $form->field($generator, 'db');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $this->registerJsFile('../mygii.js', ['position'=> yii\web\View::POS_END,'depends' => [yii\web\JqueryAsset::className()]]);