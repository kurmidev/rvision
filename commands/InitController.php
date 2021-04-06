<?php

namespace app\commands;

use app\models\ViAccess;
use app\components\Constants as C;
use app\models\Operator;
use app\models\Branch;

class InitController extends BaseController {

    public function _createAdmin($usertype, $username, $password) {
        $model = ViAccess::findOne(['usertype' => $usertype]);
        if (!$model instanceof ViAccess) {
            $model = new ViAccess(["scenario" => ViAccess::SCENARIO_CREATE]);
            $model->name = $username;
            $model->login_id = $username;
            $model->password = $password;
            $model->usertype = $usertype;
            $model->mobileno = "9399393930";
            $model->email = $username . "@rvision.com";
            $model->status = C::STATUS_ACTIVE;
            if ($model->validate() && $model->save()) {
                echo "User created successfully with username {$model->login_id} and password {$model->password}." . PHP_EOL;
            } else {
                print_r($model->errors);
            }
        } else {
            echo "User already exits with username {$model->login_id} and {$model->id}!" . PHP_EOL;
        }
    }

    public function actionCreateAdmin() {
        $model = ViAccess::findOne(['usertype' => C::USER_TYPE_CONSOLE]);
        if (!$model instanceof ViAccess) {
            $query = "insert into vi_access(name,login_id,password,usertype,mobileno,status,created_on,created_by)";
            $query .= "values('console','console','console','" . C::USER_TYPE_CONSOLE . "','9128736450','" . C::STATUS_ACTIVE . "','" . date("Y-m-d H:i:s") . "',-1)";
            \Yii::$app->db->createCommand($query)->execute();
        }
        $this->stdout("Please provide details of sadmin" . PHP_EOL, \yii\helpers\Console::BOLD);
        $username = $this->prompt("Please enter sadmin username", ["required" => true]);
        $password = $this->prompt("Please enter sadmin password", ["required" => true]);
        if (!empty($username) && !empty($password)) {
            $this->_createAdmin(C::USER_TYPE_SADMIN, trim($username), trim($password));
        }
    }

    public function _createBranch() {
        $branch = new Branch(['scenario' => Branch::SCENARIO_CREATE]);
        $branch->name = $this->prompt("Enter Branch name : ", ['required' => TRUE]);
        $branch->address = $this->prompt("Enter Branch Address : ", ['required' => TRUE]);
        $branch->phoneno = $this->prompt("Enter Branch Phone no : ", ['required' => TRUE]);
        $branch->branch_incharge = $this->prompt("Enter Branch Incharge : ", ['required' => TRUE]);
        $branch->mobileno = $this->prompt("Enter Branch Mobile no : ", ['required' => TRUE]);
        $branch->status = C::STATUS_ACTIVE;
        if ($branch->validate() && $branch->save()) {
            echo "Branch {$branch->name} created successfully" . PHP_EOL;
            $this->_createMso($branch);
        }
    }

    public function _createMso(Branch $branch) {
        $operator = Operator::findOne(['operator_type' => C::USER_TYPE_MSO]);
        if (!$operator instanceof Operator) {
            $operator = new Operator(['scenario' => Operator::SCENARIO_CREATE]);
            $operator->name = $this->prompt("Enter MSO name : ", ['required' => TRUE]);
            $operator->contact_person = $this->prompt("Enter Contact Person : ", ['required' => TRUE]);
            $operator->mobileno = $this->prompt("Enter mobile no : ", ['required' => TRUE]);
            $operator->phoneno = $this->prompt("Enter phone no : ", ['required' => TRUE]);
            $operator->address = $this->prompt("Enter address : ", ['required' => TRUE]);
            $operator->email = $this->prompt("Enter email : ", ['required' => false]);
            $operator->login_id = $this->prompt("Enter login id : ", ['required' => TRUE]);
            $operator->password = $this->prompt("Enter password : ", ['required' => TRUE]);
            $operator->branch_id = $branch->id;
            $operator->status = C::STATUS_ACTIVE;
            $operator->operator_type = C::USER_TYPE_MSO;
            if ($operator->validate() && $operator->save()) {
                echo "MSO added successfully." . PHP_EOL;
                $this->_createUser($operator);
            }
        }
    }

    public function _createUser(Operator $operator) {
        $model = ViAccess::findOne(['login_id' => $operator->login_id]);
        if (!$model instanceof ViAccess) {
            $model = new ViAccess(["scenario" => ViAccess::SCENARIO_CREATE]);
            $model->name = $operator->name;
            $model->login_id = $operator->login_id;
            $model->password = $operator->password;
            $model->usertype = $operator->operator_type;
            $model->mobileno = $operator->mobileno;
            $model->email = $operator->email;
            $model->status = C::STATUS_ACTIVE;
            $model->operator_id = $operator->id;
            if ($model->validate() && $model->save()) {
                echo "Login Id registered successfully!";
            }
        } else {
            echo "Login Id already exist!";
        }
    }

    public function actionCreateMso() {
        $model = ViAccess::findOne(['usertype' => C::USER_TYPE_MSO]);

        if (!$model instanceof ViAccess) {
            $this->_createBranch();
        } else {
            echo "User MSO already exist with username {$model->login_id} and password {$model->password}." . PHP_EOL;
        }
    }

}
