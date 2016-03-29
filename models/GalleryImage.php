<?php
/**
 * User: kg.korinovskiy
 * Date: 24.03.2016
 * Time: 11:11
 */

namespace app\models;


class GalleryImage extends \zxbodya\yii2\galleryManager\GalleryImage
{
    public $liked;
    /**
     * @var GalleryBehavior
     */
    protected $galleryBehavior;

    /**
     * @inheritdoc
     */
    function __construct(GalleryBehavior $galleryBehavior, array $props)
    {
        parent::__construct($galleryBehavior, $props);
        $this->liked = isset($props['liked']) ? intval($props['liked']) : 0;
    }

    /**
     * @param string $version
     * @return string
     */
    public function getFilePath($version)
    {
        return $this->galleryBehavior->getFilePath($this->id, $version);
    }

    public function likedUp() {
        $this->liked++;
        return $this->likedSet();
    }

    public function likedDown() {
        $this->liked--;
        return $this->likedSet();
    }

    public function likedSet() {
        return $this->galleryBehavior->updateImageLiked($this);
    }
}