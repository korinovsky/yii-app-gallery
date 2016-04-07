<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GallerySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Galleries';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gallery-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Gallery', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model) {
                    return Html::a($model->name, ['gallery/view', 'id' => $model->id]);
                },
            ],
            'description:ntext',
            'active:boolean',
            [
                'attribute' => 'sort',
                'headerOptions' => [
                    'class' => 'sort-numerical'
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{toggle} {update} {delete}',
                'buttons' => [
                    'toggle' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-off"></span>', $url, [
                            'data' => [
                                'method' => 'post',
                            ],
                            'title' => $model->active  ? 'Выключить' : 'Включить',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
