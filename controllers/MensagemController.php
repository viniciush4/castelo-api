<?php

namespace app\controllers;

use http\Url;
use Yii;
use yii\helpers\Json;
use yii\rest\ActiveController;
use app\models\Mensagem;
use yii\filters\auth\HttpBasicAuth;
use yii\base\Security;
use yii\web\UploadedFile;


class MensagemController extends ActiveController
{
    public $modelClass = 'app\models\Mensagem';


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);
        
        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];
        
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];
        $behaviors['authenticator']['class'] = HttpBasicAuth::className();

        return $behaviors;
    }


    public function actions()
    {
        $actions = parent::actions();
        // unset($actions['delete'], $actions['create']);
        return $actions;
    }


    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'create' || $action === 'update' || $action === 'delete')
        {
            if(!(\Yii::$app->user->identity->super))
            {
                throw new \yii\web\ForbiddenHttpException('Você não tem permissão para realizar esta ação.');
            }
        }
    }


    public function actionCreate()
    {
        $post = \Yii::$app->request->post();

        $mensagem = new Mensagem();

        foreach($post as $atributo => $valor){
            $mensagem->$atributo = $valor;
        }

        $audio = UploadedFile::getInstanceByName("audio");

        if (empty($audio)){
            throw new \Exception('É necessário fornecer um arquivo de áudio.');
        }

        $mensagem->url_audio = '../web/audios/'.$audio->name;
        $audio->saveAs($mensagem->url_audio);

        $mensagem->url_imagem = 'teste';

        if(!$mensagem->save()){
            throw new \Exception('Não foi possível salvar a mensagem. Erros: '.Json::encode($mensagem->getErrors()));
        }
    }
}
