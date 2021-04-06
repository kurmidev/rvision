<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

/**
 * Description of BaseController
 *
 * @author chandrap
 */
class BaseController extends \yii\rest\Controller {

    public $_searchClass;

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::className(),
        ];
        $behaviors['authenticator']['except'] = ['login','options'];
        return $behaviors;
    }

    public function getSearchClass() {
        if ($this->_searchClass) {
            return $this->_searchClass;
        } else {
            return $this->modelClass . 'Search';
        }
    }

    public function actionIndex() {
        $searchClass = $this->getSearchClass();
        $queryObj = (new $searchClass);
        if (\Yii::$app instanceof \yii\web\Application && \Yii::$app->request->get('sort')) {
            $pk = current((array) $queryObj->primaryKey());
            $_GET['sort'] .= ',' . $pk;
        }

        $query = $queryObj->search(\Yii::$app->request->get('filter'), \Yii::$app->request->get('notfilter'), ['action' => 'index']
        );

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => $this->getPagination(),
            'sort' => [
                'defaultOrder' => $this->getdefaultOrder(),
                'enableMultiSort' => true
            ],
        ]);
//        print_r($return->getSort());exit;
    }

    public function actionOptions($id = null) {
        return "ok";
    }

    function getPagination() {
        return self::_getPagination();
    }

    static function _getPagination() {
        return [
            'pageSizeLimit' => [\Yii::$app->params['page_size']['min'], \Yii::$app->params['page_size']['max']],
            'defaultPageSize' => \Yii::$app->params['page_size']['default']
        ];
    }

    function getdefaultOrder() {
        return [
            'id' => SORT_DESC
        ];
    }

}
