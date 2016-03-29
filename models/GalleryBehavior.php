<?php
/**
 * User: kg.korinovskiy
 * Date: 24.03.2016
 * Time: 11:07
 */

namespace app\models;

use Yii;

class GalleryBehavior extends \zxbodya\yii2\galleryManager\GalleryBehavior
{
    /**
     * @return GalleryImage
     */
    public function getImage($id)
    {
        if (
        $imageData = (new \yii\db\Query())
            ->select(['id', 'name', 'description', 'rank', 'liked'])
            ->from($this->tableName)
            ->where(['type' => $this->type, 'ownerId' => $this->getGalleryId(), 'id' => $id])
            ->one()
        ) {
            return new GalleryImage($this, $imageData);
        }
        return null;
    }
    
    /**
     * @return GalleryImage[]
     */
    public function getImages()
    {
        if ($this->_images === null) {
            $query = new \yii\db\Query();

            $imagesData = $query
                ->select(['id', 'name', 'description', 'rank', 'liked'])
                ->from($this->tableName)
                ->where(['type' => $this->type, 'ownerId' => $this->getGalleryId()])
                ->orderBy(['rank' => 'asc'])
                ->all();

            $this->_images = [];
            foreach ($imagesData as $imageData) {
                $this->_images[] = new GalleryImage($this, $imageData);
            }
        }

        return $this->_images;
    }

    /**
     * @param GalleryImage $image
     * @return int number of rows affected by the execution.
     * @throws \yii\db\Exception
     */
    public function updateImageLiked(GalleryImage $image)
    {
        return Yii::$app->db->createCommand()
            ->update(
                $this->tableName,
                ['liked' => $image->liked],
                ['id' => $image->id]
            )->execute();
    }
}