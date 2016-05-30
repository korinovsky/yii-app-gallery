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

if (!function_exists('text2url')) {
    function descbeautify($text) {
        return nl2br(preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', htmlspecialchars($text)));
    }
}

/* @var \app\models\GalleryBehavior $gallery */
$gallery = $model->getBehavior('galleryBehavior');

?>
<div class="site-gallery">
    <div class="title<?= ($many = count($models) > 1) ? '' : ' no-events' ?>">
        <? if ($many): ?>
            <h1><?= Html::a('<i class="glyphicon glyphicon-menu-hamburger"></i>'.$model->name, null/*, $model->description ? ['title' => $model->description] : []*/) ?></h1>
            <ul>
                <? foreach ($models as $m): if ($m->id != $model->id): ?>
                    <li><?= Html::a('<i class="glyphicon glyphicon-chevron-right"></i>'.$m->name, ['site/index', 'sid' => $m->sid]/*, $m->description ? ['title' => $m->description] : []*/) ?></li>
                <? endif; endforeach; ?>
            </ul>
        <? else: ?>
            <h1 ><?= $model->name ?></h1>
        <? endif; ?>
    </div>
    <?
    if ($model->description) {
        echo Html::tag('div', Html::tag('p', descbeautify($desc = trim($model->description))).
            (($t = count($desca = explode("\n", $desc)) > 1 || mb_strlen($desca[0]) > 50) ? Html::a((mb_strlen($desca[0]) > 50 ? mb_substr($desca[0], 0, 30) : $desca[0]).'…', null) : ''), ['class' => 'desc'.($t ? '' : ' no-events')]);
    }
    $like = [];
    $fotorama = \metalguardian\fotorama\Fotorama::begin([
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
    ]);
    foreach($gallery->getImages(true) as $image)
    {
        /* @var \app\models\GalleryImage $image */
        $is = getimagesize($image->getFilePath('thumb'));
        if ($image->liked > 0) {
            $like['i'.$image->id] = $image->liked;
        }
        echo Html::a('<img src="'.$image->getUrl('thumb').($is ? '" '.$is[3] : '"').'>', $image->getUrl('original'), ['data' => [
            'caption' => $image->name.($image->description ? ($image->name ? ' – ' : '').descbeautify($image->description) : ''),
            'id' => 'i'.$image->id,
        ]]);
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