<?php

namespace app\models;

use Yii;
use app\common\exceptions\ActiveRecordNotFoundException;
use app\common\utils\TimeUtils;
use Firebase\JWT\JWT;
use yii\db\ActiveRecord;
use yii\filters\RateLimitInterface;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "credential".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $created_at
 */
class Credential extends ActiveRecord implements IdentityInterface, RateLimitInterface
{

    public $rateLimit = 1;
    public $allowance;
    public $allowance_updated_at;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credential';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Api Key',
            'password' => 'Api Secret',
            'created_at' => 'Created At',
        ];
    }

    public function beforeSave($insert): bool
    {

        if ($insert) {
            $this->created_at = TimeUtils::now();
        }

        return parent::beforeSave($insert);
    }

    public static function getBasicAuth($token){

        if (!preg_match('/^Basic\s+(.*?)$/', $token, $matches)) {
            throw new UnauthorizedHttpException();
        }

        $token = explode('Basic ', $token)[1];
        $token = base64_decode($token);
        $token_parts = explode(':', $token);

        return ['username' => $token_parts[0], 'password' => $token_parts[1]];
    }

    public static function basicAuth(string $username, string $password): array
    {

        $credential = Credential::findOne(['username' => $username]);

        if (empty($credential)) {
            throw new ActiveRecordNotFoundException(Credential::class);
        }

        if (!Yii::$app->getSecurity()->validatePassword($password, $credential->password)) {
            throw new UnauthorizedHttpException();
        }

        $exp = strtotime('+1 month');

        $payload = [
            'sub' => $credential->id,
            //token has 1 month validation
            'exp' => $exp
        ];

        $jwt = JWT::encode($payload, JWT_TOKEN);

        return [
            'jwt' => $jwt,
            'exp' => $exp
        ];

    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne([ 'id' => $id ]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     * @throws ActiveRecordNotFoundException
     * @throws UnauthorizedHttpException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (!preg_match('/^Bearer\s+(.*?)$/', $token, $matches)) {
            throw new UnauthorizedHttpException();
        }

        $token = $matches[1] ?? null;

        $jwt = (array) JWT::decode($token, JWT_TOKEN, ['HS256']);

        $sub = $jwt['sub'] ?? null;
        $exp = $jwt['exp'] ?? null;

        if(time() > $exp){
            throw new UnauthorizedHttpException();
        }

        $credential = Credential::findOne(['id' => $sub]);

        if(empty($credential)){
            throw new ActiveRecordNotFoundException(Credential::class, $sub);
        }

        return $credential;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    /**
     * Returns the maximum number of allowed requests and the window size.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     * and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, 60];
    }

    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }

    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param int $allowance the number of allowed requests remaining.
     * @param int $timestamp the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }
}