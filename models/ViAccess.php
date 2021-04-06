<?php

namespace app\models;

use Yii;
use app\components\Constants as C;
use Firebase\JWT\JWT;

/**
 * This is the model class for table "VI_ACCESS".
 *
 * @property integer $id
 * @property string $name
 * @property string $login_id
 * @property string $password
 * @property string $usertype
 * @property string $mobileno
 * @property integer $status
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $operator_id
 * @property string $remark
 * @property integer $deleted
 * @property string $authkey
 * @property integer $access_token_expired_at
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $unconfirmed_email
 * @property integer $confirmed_at
 * @property string $registration_ip
 * @property integer $last_login_at
 * @property string $last_login_ip
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 */
class ViAccess extends \app\models\BaseModel implements \yii\web\IdentityInterface {

    const FORCE_RESET_PASSWORD = 'force_reset_password';

    public $access_token;
    public $access;
    protected static $decodedToken;
    public $extra_data;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'VI_ACCESS';
    }

    public function scenarios() {

        return [
            self::SCENARIO_DEFAULT => ['*'], // Also tried without this line
            self::SCENARIO_CREATE => ['name', 'login_id', 'password', 'usertype', 'mobileno', 'status', 'operator_id', 'remark', 'deleted', 'authkey', 'access_token_expired_at', 'password_hash', 'password_reset_token', 'email', 'last_login_at', 'last_login_ip'],
            self::SCENARIO_CONSOLE => ['id', 'name', 'login_id', 'password', 'usertype', 'mobileno', 'status', 'created_on', 'updated_on', 'created_by', 'updated_by', 'operator_id', 'remark', 'deleted', 'authkey', 'access_token_expired_at', 'password_hash', 'password_reset_token', 'email', 'last_login_at', 'last_login_ip', 'created_at', 'updated_at'],
            self::SCENARIO_UPDATE => ['name', 'login_id', 'password', 'usertype', 'mobileno', 'status', 'operator_id', 'remark', 'deleted', 'authkey', 'access_token_expired_at', 'password_hash', 'password_reset_token', 'email', 'last_login_at', 'last_login_ip'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if ($insert) {
            if (empty($this->authkey)) {
                $this->generateAuthKey();
            }
            if (empty($this->password_hash)) {
                $this->setPassword($this->password);
            }
            if (empty($this->registration_ip) && !static::isConsole()) {
                $this->registration_ip = Yii::$app->request->userIP;
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'login_id', 'usertype',], 'required'],
            [['status', 'created_by', 'updated_by', 'operator_id', 'deleted', 'usertype', 'access_token_expired_at', 'last_login_at', 'created_at', 'updated_at'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'email'], 'string', 'max' => 100],
            [['login_id'], 'string', 'max' => 50],
            [['password', 'authkey', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['mobileno'], 'string', 'max' => 15],
            [['remark'], 'string', 'max' => 250],
            [['registration_ip', 'last_login_ip'], 'string', 'max' => 20],
            [['name', 'operator_id'], 'unique', 'targetAttribute' => ['name', 'operator_id'], 'message' => 'The combination of Name and Operator ID has already been taken.'],
            [['login_id'], 'unique'],
        ];
    }

    /**
     * with
     * @return type
     */
    function defaultWith() {
        return [];
    }

    static function extraFieldsWithConf() {
        $retun = parent::extraFieldsWithConf();
        $retun['operator_lbl'] = 'operator';
        return $retun;
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id',
            'name',
            'login_id',
            'password',
            'usertype',
            'mobileno',
            'status',
            'operator_id',
            'remark',
            'deleted',
            'authkey',
            'access_token_expired_at',
            'password_hash',
            'password_reset_token',
            'email',
            'registration_ip',
            'last_login_at',
            'last_login_ip',
            'created_on',
            'updated_on',
            'created_by',
            'updated_by',
        ];

        $fields = array_merge(parent::fields(), $fields);
        return $this->getFields($fields);
    }

    public function getOperator() {
        return $this->hasOne(Operator::class, ['id' => "operator_id"]);
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        $fields = parent::extraFields();

        $fields['operator_lbl'] = function () {
            return $this->operator ? $this->operator->name : null;
        };
        return $this->getFilterExtraFields($fields);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        $secret = static::getSecretKey();
        try {
            $decoded = JWT::decode($token, $secret, [static::getAlgo()]);
        } catch (\Exception $e) {
            return false;
        }
        static::$decodedToken = (array) $decoded;

        if (!isset(static::$decodedToken['jti'])) {
            return false;
        }
        $exp = static::$decodedToken['exp'];
        if ($exp < time()) {
            return null;
        }
        $id = static::$decodedToken['jti'];

        $session_id = isset(static::$decodedToken['data']->session_id) ? static::$decodedToken['data']->session_id : '';
        $auth_key = isset(static::$decodedToken['data']->auth_key) ? static::$decodedToken['data']->auth_key : '';
        return static::findByJTI($id, $session_id, $auth_key);
    }

    /**
     * Finds User model using static method findOne
     * Override this method in model if you need to complicate id-management
     * @param  string $id if of user to search
     * @return mixed       User model
     */
    public static function findByJTI($id, $session_id, $auth_key) {
        /** @var User $user */
        $user = static::find()->where(['id' => $id, "status" => C::STATUS_ACTIVE])
                        ->andWhere(['>', 'access_token_expired_at', date("Y-m-d H:i:s")])->one();

        if ($user !== null && ($user->getIsBlocked() == true || $auth_key != $user->authKey)) {
            return null;
        }
        if ($user) {
            $user->extra_data = isset(static::$decodedToken['extra_data']) ? static::$decodedToken['extra_data'] : [];
        }

        return $user;
    }

    public function getIsBlocked() {
        return $this->status == C::STATUS_BLOCKED;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this->authkey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        if (empty($this->password_hash))
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);

        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->authkey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public static function getUserTypesLabel($ut) {
        $ret = '';
        if (isset(C::LABEL_USER_TYPE[$ut])) {
            $ret = Yii::t('app', C::LABEL_USER_TYPE[$ut]);
        }
        return $ret;
    }

    public static function getAuthHashToken() {
        return base64_encode(Yii::$app->user->getIdentity()->authkey . '|' . dechex(Yii::$app->user->getIdentity()->id));
    }

    /**
     * Finds user by username
     *
     * @param string $usernamet
     * @param array $roles
     * @return static|null
     */
    public static function findByUsernameWithRoles($username, $roles, &$message = false) {
        /** @var User $user */
        $user = static::find()->where([
//'status' => Status::ACTIVE,
                ])->andWhere(['OR', ['login_id' => $username], ['mobileno' => $username,
                        'usertype' => C::USER_TYPE_SUBSCRIBER]])->one();
        if (empty($user)) {
            $message = 'User not found';
            $user = null;
        } elseif ($user->status != C::STATUS_ACTIVE) {
            $message = $user->block_message ? $user->block_message : "User is " . Status::getLabel($user->status);
            $user = null;
        } elseif (!$user->getIsConfirmed()) {
            $message = "User is in pending state";
            $user = null;
        }
        if ($user == null) {
            return null;
        }
        return $user;
    }

    /**
     * @return bool Whether the user is confirmed or not.
     */
    public function getIsConfirmed() {
        return true;
// return $this->confirmed_at != null;
    }

    /**
     * Generate access token
     *  This function will be called every on request to refresh access token.
     *
     * @param bool $forceRegenerate whether regenerate access token even if not expired
     *
     * @return bool whether the access token is generated or not
     */
    public function generateAccessTokenAfterUpdatingClientInfo($forceRegenerate = false) {
// update client login, ip
        if (!static::isConsole()) {
            $this->last_login_ip = Yii::$app->request->userIP;
            $this->last_login_at = date('Y-m-d H:i:s');
        }
//        $this->password_reset_token = null;
// check time is expired or not
        if ($forceRegenerate == true || $this->access_token_expired_at == null || (time() > strtotime($this->access_token_expired_at))) {
            if ($forceRegenerate) {
//                $this->password_reset_token = null;
            }
// generate access token
            $this->generateAccessToken();
        }
        $this->save(false);
        return true;
    }

    public function generateAccessToken() {
// generate access token
//        $this->access_token = Yii::$app->security->generateRandomString();
        $tokens = $this->getJWT();
        $this->access_token = $tokens[0];   // Token
        $this->access_token_expired_at = date("Y-m-d H:i:s", $tokens[1]['exp']); // Expire
    }

    /**
     * Encodes model data to create custom JWT with model.id set in it
     * @return array encoded JWT
     */
    public function getJWT() {
// Collect all the data
        $secret = static::getSecretKey();
        $currentTime = time();
        $expire = $currentTime + LOGIN_EXPIRY_TIME; // 1 day
        $request = Yii::$app->request;
        $hostInfo = '';
        if ($request instanceof WebRequest) {
            $hostInfo = $request->hostInfo;
        }

// Merge token with presets not to miss any params in custom
// configuration
        $token = array_merge([
            'iat' => $currentTime, // Issued at: timestamp of token issuing.
            'iss' => $hostInfo, // Issuer: A string containing the name or identifier of the issuer application. Can be a domain name and can be used to discard tokens from other applications.
            'aud' => $hostInfo,
            'nbf' => $currentTime, // Not Before: Timestamp of when the token should start being considered valid. Should be equal to or greater than iat. In this case, the token will begin to be valid 10 seconds
            'exp' => $expire, // Expire: Timestamp of when the token should cease to be valid. Should be greater than iat and nbf. In this case, the token will expire 60 seconds after being issued.
            'extra_data' => [], // Expire: Timestamp of when the token should cease to be valid. Should be greater than iat and nbf. In this case, the token will expire 60 seconds after being issued.
            'data' => [
                'username' => $this->login_id,
                'roleLabel' => C::LABEL_USER_TYPE[$this->usertype],
                'lastLoginAt' => $this->last_login_at,
                'auth_key' => $this->authkey
            ]
                ], []);
// Set up id
        $token['jti'] = $this->getJTI();    // JSON Token ID: A unique string, could be used to validate a token, but goes against not having a centralized issuer authority.
        return [JWT::encode($token, $secret, static::getAlgo()), $token];
    }

    protected static function getSecretKey() {
        return Yii::$app->params['jwtSecretCode'];
    }

    /**
     * Returns some 'id' to encode to token. By default is current model id.
     * If you override this method, be sure that findByJTI is updated too
     * @return integer any unique integer identifier of user
     */
    public function getJTI() {
        return $this->getId();
    }

    public static function getUserFromSessionAuthHashToken() {
        $token = Yii::$app->session->get('token');
        if ($token) {
            $created = Yii::$app->session->get('CREATED');
            if (time() - $created < (60 * 60)) {
                return static::getUserfromHashToken($token);
            }
        }
        return false;
    }

    public static function getUserfromHashToken($token) {
        $decode = base64_decode($token);
        if (strpos($decode, '|')) {
            list($authKey, $idHex) = explode('|', $decode);
            $id = hexdec($idHex);
            return static::findByAuthKey($id, $authKey);
        } else {
            return false;
        }
    }

    public static function getUserfromHash($token) {
        return static::findByHash($token);
    }

    /**
     * Getter for encryption algorytm used in JWT generation and decoding
     * Override this method to set up other algorytm.
     * @return string needed algorytm
     */
    public static function getAlgo() {
        return 'HS256';
    }

    public static function findByAuthKey($id, $authkey) {

        /** @var User $user */
        $user = static::find()->where([
                            '=', 'id', $id
                        ])
                        ->andWhere([
                            '=', 'status', Status::ACTIVE
                        ])
                        ->andWhere([
                            '=', 'auth_key', $authkey
                        ])
                        ->andWhere([
                            '>', 'access_token_expired_at', new Expression('NOW()')
                        ])->one();

        if ($user !== null &&
                ($user->getIsBlocked() == true || $user->getIsConfirmed() == false)) {
            return null;
        }

        return $user;
    }

    public static function findByHash($authkey) {

        /** @var User $user */
        $user = static::find()
                        ->andWhere([
                            '=', 'status', Status::ACTIVE
                        ])
                        ->andWhere([
                            '=', 'md5(CONCAT(username,password_hash))', $authkey
                        ])
                        ->andWhere([
                            '>', 'access_token_expired_at', new Expression('NOW()')
                        ])->one();

        if ($user !== null &&
                ($user->getIsBlocked() == true || $user->getIsConfirmed() == false)) {
            return null;
        }
        return $user;
    }

    public static function isConsole() {
        return \Yii::$app instanceof \yii\console\Application;
    }

    public static function isWeb() {
        return \Yii::$app instanceof \yii\web\Application;
    }

    public static function getCurrentUserId() {
        if (self::isUserIdentityAvaliable()) {
            return \Yii::$app->user->getIdentity(false)->id;
        } else if (self::isConsole()) {
            $user = self::findOne(['usertype' => C::USER_TYPE_CONSOLE]);
            if ($user instanceof ViAccess) {
                return $user->id;
            }
        }
        return 0;
    }

    public function setIdentity($identity) {
        parent::setIdentity($identity);
    }

    public static function isUserIdentityAvaliable() {
        return self::isConsole() ? false : (\Yii::$app->user->getIdentity(false) !== null);
    }

}
