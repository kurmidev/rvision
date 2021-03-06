<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'zeroToNullSearchFields');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'defaultcontrollerPath')->hiddenInput();
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $this->registerJsFile('../mygii.js', ['position'=> yii\web\View::POS_END,'depends' => [yii\web\JqueryAsset::className()]]);