<?php

namespace app\modules\v1\controllers;

use Yii;
use app\models\ViAccess;
use app\models\ViAccessSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;
use yii\helpers\Url;
use app\forms\LoginForm;
use app\components\Constants as C;

/**
 * ViAccessController implements the REST actions for ViAccess model.
 */
class ViAccessController extends BaseController {

    public $modelClass = "app\models\ViAccess";

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    /**
     * Displays a single ViAccess model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);

        if ($model) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }

    /**
     * Creates a new ViAccess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ViAccess(['scenario' => ViAccess::SCENARIO_CREATE]);
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate() && $model->save()) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$id], true));
            return $this->actionView($id);
        } else {
            // Validation error
            throw new HttpException(422, json_encode($model->errors));
        }

        return $model;
    }

    /**
     * Updates an existing ViAccess model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->actionView($id);
        $model->scenario = ViAccess::SCENARIO_UPDATE;

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate() && $model->save()) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
        } else {
            // Validation error
            throw new HttpException(422, json_encode($model->errors));
        }

        return $model;
    }

    /**
     * Deletes an existing ViAccess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {

        /*  if ($this->findModel($id)->delete()) {
          throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
          } */
        $model = $this->actionView($id);

        $model->status = \app\models\Status::DELETED;

        if ($model->save(false) === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(204);
        return "ok";
    }

    /**
     * Finds the ViAccess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ViAccess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ViAccess::find()->defaultScope(['self' => true])->andwhere(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }

    public function actionLoginHash() {
        $authType = LoginForm::LOGIN_BY_HASH;
        return $this->login($authType);
    }

    public function actionLoginToken() {
        $authType = LoginForm::LOGIN_BY_TOKEN;
        return $this->login($authType);
    }

    public function actionLogin() {
        $authType = LoginForm::LOGIN_BY_PASSWORD;
        return $this->login($authType);
    }

    public function login($authType) {
        $model = new LoginForm();
        $model->roles = array_keys(\app\components\Constants::LABEL_USER_TYPE);
        $model->load(Yii::$app->request->post());
        $model->loginType = $authType;
        if ($model->login()) {
            $user = $model->getUser();
            //for Admin APP
            if (!in_array($user->usertype, array_keys(\app\components\Constants::LABEL_USER_TYPE))) {
                throw new HttpException(422, json_encode(['password' => ['Invalid user type login']]));
            }
            $user->remark = $user->login_id . " Loggedin At " . date('Y-m-d H:i:s') . " from ip:" . Yii::$app->request->userIP;
            $user->generateAccessTokenAfterUpdatingClientInfo(true);
            $responseData = $this->formatUserDetails($user);
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return $responseData;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }

    private function formatUserDetails($user) {
        $optModel = \app\models\Operator::findOne(['id' => $user->operator_id]);
        $mso_id = $subscriber_id = 0;
        $access_right = [];
        $amount = 0;
        if (!empty($user)) {
            $data = [
                'id' => $user->id,
                'role' => $user->usertype,
                'name' => $user->name,
                'login_id' => $user->login_id,
                'email' => $user->email,
                'mobile_no' => $user->mobileno,
                'force_reset_password' => ($user->password_reset_token == ViAccess::FORCE_RESET_PASSWORD),
                'type' => $user->usertype,
                'last_login_at' => $user->last_login_at,
                'type_label' => ViAccess::getUserTypesLabel($user->usertype),
                'operator_id' => $user->operator_id,
                'balance' => $amount,
                //'setting' => $user->userSetting,
                'operator_lbl' => $user->operator ? $user->operator->name : null,
                'opt_mobile_no' => $user->operator ? $user->operator->mobile_no : null,
                'code' => $user->operator ? $user->operator->code : null,
                'contact_person' => $user->operator ? $user->operator->contact_person : null,
                'access_token' => $user->access_token,
                'auth_token' => ViAccess::getAuthHashToken(),
                'hash_token' => md5($user->login_id . $user->password_hash),
                'expiry' => $user->access_token_expired_at,
//                'permission' => $permission,
//                'app_permission' => $appPermission,
//                'app_config' => $appConfiguration,
//                'MRP_ENABLED' => MRP_ENABLED ? 1 : 0,
//                'cas' => \app\models\cas\Casvendor::find()->select(['id' => 'id', 'code', 'name', 'cas_type'])->andWhere(['status' => \app\models\Status::ACTIVE])->asarray()->all(),
            ];

//            if (OPERATOR_VERIFICATION_ENABLED) {
//                $data['is_verified'] = !empty($user->operator) ? $user->operator->isVerified : 1;
//            }
//
//            if (PAYMENT_GATEWAY_CONDITION == \components\Constants::PG_OPERATOR_WISE) {
//                $data['pg_setting'] = PAYMENT_GATEWAY_OPERATOR_LEVEL;
//            }
//            $data['dar'] = DUMMY_REGISTERATION_ENABLED;
//            if ($user->role == User::USER_TYPE_SUBSCRIBER) {
//                $data['pygt'] = $user->operator->customer_portal_config;
//            }
//            if (!empty($access_right)) {
//                $data['access_right'] = $access_right;
//            }
            return $data;
        } else {
            throw new HttpException(403, json_encode(['username' => 'Doesnot have any permission.<br>Please contact your administrator']));
        }
    }

}
