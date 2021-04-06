<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\components\Constants as C;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model {

    public $username;
    public $password;
    public $token;
    public $block_message;
    public $loginType;
    public $roles = [];
    public $rememberMe = true;
    private $isMasterKeyLogin = false;

    const LOGIN_BY_PASSWORD = 1;
    const LOGIN_BY_TOKEN = 2;
    const LOGIN_BY_HASH = 3;

    /** @var User */
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // username and password are both required
            [['username', 'password'], 'required', 'when' => function ($model, $attribute) {
                    return $model->loginType == self::LOGIN_BY_PASSWORD;
                }],
            [['token'], 'required', 'when' => function ($model, $attribute) {
                    return in_array($model->loginType, [self::LOGIN_BY_TOKEN, self::LOGIN_BY_HASH]);
                }],
            [['token'], 'validateToken'],
            [['loginType'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword', 'when' => function ($model, $attribute) {
                    return $model->loginType == self::LOGIN_BY_PASSWORD;
                }],
        ];
    }

    public function validateToken($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->loginType == self::LOGIN_BY_TOKEN) {
                $user = $this->getUserByToken();
            } else if ($this->loginType == self::LOGIN_BY_HASH) {
                $user = $this->getUserByHash();
            }

            $isMasterKeyLogin = false;
            if (!$user) {
                $this->addError($attribute, 'Incorrect auth token provided.');
            }
            $this->isMasterKeyLogin = $isMasterKeyLogin;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUserByUsername();
            $isMasterKeyLogin = false;
            if (!$user) {
                $this->addError($attribute, $this->block_message ? $this->block_message : "Invalid User");
            } else if (!$user->validatePassword($this->password, $isMasterKeyLogin)) {
                $this->addError($attribute, 'Incorrect user password.');
            }
            $this->isMasterKeyLogin = $isMasterKeyLogin;
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            if ($this->loginType == self::LOGIN_BY_PASSWORD) {
                $user = $this->getUserByToken();
            } else if ($this->loginType == self::LOGIN_BY_HASH) {
                $user = $this->getUserByHash();
            } else {
                $user = $this->getUserByUsername();
            }
            if ($user instanceof \app\models\ViAccess) {
                if ($user && !$this->validateUserType($user->usertype)) {
                    return false;
                } else {
                    return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
                }
            }
        }
        return false;
    }

    public function validateUserType($type) {
        $userTyeEnabled = array_keys(C::LABEL_USER_TYPE);
        if ($this->isMasterKeyLogin == false && !in_array($type, $userTyeEnabled)) {
            $this->addError('username', 'Your Login not Enabled! Please try after sometime!');
            return false;
        }
        return true;
    }

    /**
     * Return User object
     *
     * @return User
     */
    public function getUser() {
        return $this->_user;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUserByUsername() {

        // Roles must be set to get an user
        if (empty($this->roles)) {
            return null;
        }

        $message = null;

        if ($this->_user === false) {
            $this->_user = \app\models\ViAccess::findByUsernameWithRoles($this->username, $this->roles, $message);
            \Yii::debug($this->_user, '__USER__');
        }
        $this->block_message = $message;
        return $this->_user;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUserByToken() {
        // Roles must be set to get an user
        if (empty($this->roles)) {
            return null;
        }
        if ($this->_user === false) {
            $this->_user = User::getUserfromHashToken($this->token);
        }

        return $this->_user;
    }

    public function getUserByHash() {
        // Roles must be set to get an user
        if (empty($this->roles)) {
            return null;
        }
        if ($this->_user === false) {
            $this->_user = User::getUserfromHash($this->token);
        }

        return $this->_user;
    }

}
