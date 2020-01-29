<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario".
 *
 * @property int $id
 * @property string $nome
 * @property string $login
 * @property string $senha
 * @property int $super
 * @property string $access_token
 */
class Usuario extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'login', 'senha', 'super'], 'required'],
            [['nome', 'login', 'senha'], 'string', 'max' => 50],
            [['super'], 'integer', 'max' => 1],
            [['access_token'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'login' => 'Login',
            'senha' => 'Senha',
            'super' => 'Super',
            'access_token' => 'Access Token'
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if(Yii::$app->controller->action->id !== 'login'){
            $this->senha = sha1($this->senha);
        }

        return true;
    }
    
    public function fields()
    {
        $fields = parent::fields();

        // remove os atributos sensíveis
        unset($fields['login'], $fields['senha']);

        return $fields;
    }

    public function extraFields()
    {
        return [];
    }

    /**
    * @inheritdoc
    */
    public static function findIdentity($id)
    {
        $usuario = Usuario::find()->where(['id' => $id])->one();

        if ($usuario) {
            return new static($usuario);
        }

        return null;
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $usuario = Usuario::find()->where(['login' => $username])->one();

        if ($usuario) {
            return new static($usuario);
        }

        return null;
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return null;
    }
    
    /**
     * Validate password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->senha === sha1($password);
    }
}
