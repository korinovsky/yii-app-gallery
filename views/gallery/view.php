<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use zxbodya\yii2\galleryManager\GalleryManager;

/* @var $this yii\web\View */
/* @var $model app\models\Gallery */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Galleries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gallery-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'description:ntext',
            'group_name',
            'active:boolean',
            [
                'format' => 'html',
                'label' => 'Url',
                'value' => Html::a(Url::toRoute(['site/index', 'sid' => $model->sid], true), ['site/index', 'sid' => $model->sid]),
            ]
        ],
    ]) ?>

    <p><?= Html::a('Update thumbs', ['update-thumbs', 'id' => $model->id])?></p>

    <?= GalleryManager::widget(
        [
            'model' => $model,
            'behaviorName' => 'galleryBehavior',
            'apiRoute' => 'gallery/api',
        ]
    );
    ?>

</div>
