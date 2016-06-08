<?php

namespace app\models;

use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%gallery}}".
 *
 * @property integer $id
 * @property string $sid
 * @property integer $active
 * @property integer $sort
 * @property string $name
 * @property string $group_name
 * @property string $description
 */
class Gallery extends \yii\db\ActiveRecord
{
    const GROUP_DELIMITER = ';';
    public $sortStep = 100;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'galleryBehavior' => [
                'class' => GalleryBehavior::className(),
                'type' => 'gallery',
                'extension' => 'jpg',
                'directory' => Yii::getAlias('@webroot') . '/img/gallery',
                'url' => Yii::getAlias('@web') . '/img/gallery',
                'versions' => [
                    'thumb' => function ($img) {
                        /** @var \Imagine\Image\ImageInterface $img */
                        $dstSize = $img->getSize();
                        $maxHeight = Yii::$app->params['thumbHeight'];
                        if ($dstSize->getHeight() > $maxHeight) {
                            $dstSize = $dstSize->heighten($maxHeight);
                        }
                        return $img
                            ->copy()
                            ->resize($dstSize);
                    },
                    'original' => function ($img) {
                        /** @var \Imagine\Image\ImageInterface $img */
                        $dstSize = $img->getSize();
                        $maxHeight = 1080;
                        if ($dstSize->getHeight() > $maxHeight) {
                            $dstSize = $dstSize->heighten($maxHeight);
                        }
                        return $img
                            ->copy()
                            ->resize($dstSize);
                    },
                    'preview' => function ($img) {
                        /** @var \Imagine\Image\ImageInterface $img */
                        $dstSize = $img->getSize();
                        $maxHeight = 88;
                        if ($dstSize->getHeight() > $maxHeight) {
                            $dstSize = $dstSize->heighten($maxHeight);
                        }
                        $maxWidth = 130;
                        if ($dstSize->getWidth() > $maxWidth) {
                            $dstSize = $dstSize->widen($maxWidth);
                        }
                        return $img
                            ->copy()
                            ->resize($dstSize);
                    },
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gallery}}';
    }

     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active', 'name', 'sort'], 'required'],
            [['active', 'sort'], 'integer'],
            [['description', 'group_name'], 'string'],
            [['sid', 'name'], 'string', 'max' => 255],
            [['description', 'group_name'], 'trim'],
            [['description', 'group_name'], 'default']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sid' => 'SID',
            'active' => 'Active',
            'sort' => 'Sort',
            'name' => 'Name',
            'group_name' => 'Group Name',
            'description' => 'Description',
        ];
    }

    public function setSort() {
        if ($max = $this->find()->max('sort')) {
            $this->sort = $max + $this->sortStep - ($max % $this->sortStep);
        }
        else {
            $this->sort = $this->sortStep;
        }
    }

    public function beforeSave($insert)
    {
        if ($this->getDirtyAttributes(['name', 'group_name'])) {
            $this->sid = Inflector::slug(($this->group_name ? str_replace(static::GROUP_DELIMITER, '-', $this->group_name).'-' : '').$this->name);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getGalleryImages()
//    {
//        return $this->hasMany(GalleryImage::className(), ['ownerId' => 'id']);
//    }
}
