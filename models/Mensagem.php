<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mensagem".
 *
 * @property int $id
 * @property string $texto_base
 * @property string $url_audio
 * @property string $url_imagem
 * @property string $data
 */
class Mensagem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mensagem';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['texto_base', 'data'], 'required'],
            [['data'], 'safe'],
            [['texto_base'], 'string', 'max' => 100],
            [['url_audio', 'url_imagem'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'texto_base' => 'Texto Base',
            'url_audio' => 'Url Audio',
            'url_imagem' => 'Url Imagem',
            'data' => 'Data',
        ];
    }
}
