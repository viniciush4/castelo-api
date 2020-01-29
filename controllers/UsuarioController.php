<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use app\models\Usuario;
use yii\filters\auth\HttpBasicAuth;
use yii\base\Security;


class UsuarioController extends ActiveController
{
    public $modelClass = 'app\models\Usuario';


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
        $behaviors['authenticator']['except'] = ['login'];
        $behaviors['authenticator']['class'] = HttpBasicAuth::className();

        return $behaviors;
    }


    public function actions()
    {
        $actions = parent::actions();
        // unset($actions['view']);
        return $actions;
    }


    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'view' || $action === 'update' || $action === 'delete')
        {
            if($model->id !== \Yii::$app->user->identity->id && !(\Yii::$app->user->identity->super))
            {
                throw new \yii\web\ForbiddenHttpException('Você não tem permissão para realizar esta ação.');
            }
        }

        if ($action === 'index')
        {
            if(!(\Yii::$app->user->identity->super))
            {
                throw new \yii\web\ForbiddenHttpException('Você não tem permissão para realizar esta ação.');
            }
        }
    }


    public function actionLogin()
    {
        $post = Yii::$app->request->post();

        $usuario = Usuario::findOne(['login'=>$post['login'], 'senha'=>sha1($post['senha'])]);

        if($usuario){
            $usuario->access_token = (new Security())->generateRandomString();
            $usuario->save();
            return $usuario;
        }
        else{
            throw new \Exception('Login ou senha inválidos');
        }
    }

}
