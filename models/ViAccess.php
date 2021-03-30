<?php

namespace app\models;

use Yii;
use Firebase\JWT\JWT;

/**
 * This is the model class for table "VI_ACCESS".
 *
 * @property int $id
 * @property string $name
 * @property string $login_id
 * @property string|null $password
 * @property string $usertype
 * @property string|null $mobileno
 * @property int $status
 * @property string $created_on
 * @property string|null $updated_on
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $operator_id
 * @property string|null $remark
 * @property int $deleted
 * @property int|null $sms_id
 * @property string|null $authkey
 * @property int|null $access_token_expired_at
 * @property string|null $password_hash
 * @property string|null $password_reset_token
 * @property string|null $email
 * @property string|null $unconfirmed_email
 * @property int|null $confirmed_at
 * @property string|null $registration_ip
 * @property int|null $last_login_at
 * @property string|null $last_login_ip
 * @property int|null $blocked_at
 * @property int|null $role
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class ViAccess extends BaseModel implements \yii\web\IdentityInterface {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'VI_ACCESS';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'login_id', 'usertype', 'created_on', 'created_by'], 'required'],
            [['status', 'created_by', 'updated_by', 'operator_id', 'deleted', 'sms_id', 'access_token_expired_at', 'confirmed_at', 'last_login_at', 'blocked_at', 'role', 'created_at', 'updated_at'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'email', 'unconfirmed_email'], 'string', 'max' => 100],
            [['login_id'], 'string', 'max' => 50],
            [['password', 'authkey', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['usertype'], 'string', 'max' => 30],
            [['mobileno'], 'string', 'max' => 15],
            [['remark'], 'string', 'max' => 250],
            [['registration_ip', 'last_login_ip'], 'string', 'max' => 20],
            [['name', 'operator_id'], 'unique', 'targetAttribute' => ['name', 'operator_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'login_id' => 'Login ID',
            'password' => 'Password',
            'usertype' => 'Usertype',
            'mobileno' => 'Mobileno',
            'status' => 'Status',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'operator_id' => 'Operator ID',
            'remark' => 'Remark',
            'deleted' => 'Deleted',
            'sms_id' => 'Sms ID',
            'authkey' => 'Authkey',
            'access_token_expired_at' => 'Access Token Expired At',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'unconfirmed_email' => 'Unconfirmed Email',
            'confirmed_at' => 'Confirmed At',
            'registration_ip' => 'Registration Ip',
            'last_login_at' => 'Last Login At',
            'last_login_ip' => 'Last Login Ip',
            'blocked_at' => 'Blocked At',
            'role' => 'Role',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function encodeJwt() {
        
    }

    public static function decodeJwt() {
        
    }

    /**
     * {@inheritdoc}
     * @return ViAccessQuery the active query used by this AR class.
     */
    public static function find() {
        return new ViAccessQuery(get_called_class());
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
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
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
        return $this->authKey;
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
        return $this->password === $password;
    }

}
