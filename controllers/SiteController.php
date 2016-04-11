<?php

namespace app\controllers;

use app\models\Gallery;
use app\models\GalleryImage;
use app\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'like' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex($sid = null)
    {
        if (!(
        $models = Gallery::find()
            ->andWhere([
                'active' => 1
            ])
            ->orderBy([
                'sort' => SORT_ASC,
                'id' => SORT_ASC,
            ])->all()
        )) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        if ($sid) {
            $model = Gallery::findOne(['sid' => $sid, 'active' => 1]);
        }
        if (!isset($model)) {
            return $this->redirect(['index', 'sid' => $models[0]->sid, '#' => 'menu']);
        }
        $this->layout = 'gallery';
        return $this->render('index', [
            'model' => Gallery::findOne($model->id),
            'models' => $models,
        ]);
    }

    public function actionLike($gid)
    {
        if ($id = intval(preg_replace('/\D/', '', Yii::$app->request->post('id')))) {
            if ($image = Gallery::findOne($gid)->getBehavior('galleryBehavior')->getImage($id)) {
                /* @var GalleryImage $image */
                $result = 0;
                $liked = Yii::$app->request->cookies->getValue('liked', []);
                if (Yii::$app->request->post('liked') !== 'true') {
                    if ($image->liked > 0 && ($result = $image->LikedDown()) && ($key = array_search($id, $liked)) !== false) {
                        unset($liked[$key]);
                    }
                }
                else {
                    if (($result = $image->LikedUp()) && !in_array($id, $liked)) {
                        $liked[] = $id;
                        sort($liked);
                    }
                }
                if ($result) {
                    Yii::$app->response->cookies->add(new \yii\web\Cookie([
                        'name' => 'liked',
                        'value' => $liked,
                       // 'expire' => null,
                    ]));
                }
            }
        }
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['gallery/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
