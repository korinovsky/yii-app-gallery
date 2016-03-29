<?php
/**
 * User: kg.korinovskiy
 * Date: 22.03.2016
 * Time: 10:20
 */

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Gallery */
/* @var $models app\models\Gallery[] */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;

if ($model->description) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->description]);
}

/* @var \app\models\GalleryBehavior $gallery */
$gallery = $model->getBehavior('galleryBehavior');

?>
<div class="site-gallery">
    <div class="title">
        <? if (count($models) > 1): ?>
            <h1><?= Html::a('<i class="glyphicon glyphicon-menu-hamburger"></i>'.$model->name, null, $model->description ? ['title' => $model->description] : []) ?></h1>
            <ul>
                <? foreach ($models as $m): if ($m->id != $model->id): ?>
                    <li><?= Html::a('<i class="glyphicon glyphicon-chevron-right"></i>'.$m->name, ['site/index', 'sid' => $m->sid], $m->description ? ['title' => $m->description] : []) ?></li>
                <? endif; endforeach; ?>
            </ul>
        <? else: ?>
            <h1><?= $model->name ?></h1>
        <? endif; ?>
    </div>
    <?
    $like = [];
    $fotorama = \metalguardian\fotorama\Fotorama::begin(
        [
            'options' => [
                'fit' => 'scaledown',
                'nav' => 'thumbs',
                'width' => '100%',
                'height' => '100%',
                'hash' => 1,
                'allowfullscreen' => 'native',
                'thumbheight' => Yii::$app->params['thumbHeight'],
                'clicktransition' => 'crossfade',
                'keyboard' => [
                    'space' => 1,
                ],
            ],
        ]
    );
    foreach($gallery->getImages() as $image)
    {
        /* @var \app\models\GalleryImage $image */
        $is = getimagesize($image->getFilePath('thumb'));
        if ($image->liked > 0) {
            $like['i'.$image->id] = $image->liked;
        }
        ?>
        <a href="<?= $image->getUrl('original') ?>" id="i<?= $image->id ?>"><img src="<?= $image->getUrl('thumb') ?>"<?= $is ? ' '.$is[3] : '' ?>></a>
        <?
    }
    $liked = Yii::$app->request->cookies->getValue('liked', []);
    foreach ($liked as &$elem) {
        $elem = 'i'.$elem;
        if (isset($like[$elem])) {
            $like[$elem] -= 1;
        }
    }
    sort($liked);
    $this->registerJs("yii.like = ".json_encode($like).";\nyii.liked = ".json_encode($liked).";\nyii.likedUrl = ".json_encode(Url::toRoute(['like', 'gid' => $model->id])).";");
    $fotorama->end();
    ?>
</div>