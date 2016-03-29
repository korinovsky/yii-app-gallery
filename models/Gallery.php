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
 * @property string $name
 * @property string $description
 */
class Gallery extends \yii\db\ActiveRecord
{
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
//                        $maxWidth = 100;
//                        if ($dstSize->getWidth() > $maxWidth) {
//                            $dstSize = $dstSize->widen($maxWidth);
//                        }
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
            [['active', 'name'], 'required'],
            [['active'], 'integer'],
            [['description'], 'string'],
            [['sid', 'name'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->getDirtyAttributes(['name'])) {
            $this->sid = Inflector::slug($this->name);
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
