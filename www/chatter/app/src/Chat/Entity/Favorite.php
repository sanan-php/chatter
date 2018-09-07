<?php

namespace Chat\Entity;

use Chat\Entity\Base\Item;
use JMS\Serializer\Annotation as Serializer;

class Favorite extends Item
{
    /**
     * @Serializer\Type("string")
     */
    private $favoriteOf;
    /**
     * @Serializer\Type("string")
     */
    private $favorite;

    /**
     * @Serializer\Type("string")
     */
    private static $entity = 'Favorite';

    public static function getEntityName(): string
    {
        return self::$entity;
    }

    public function __construct(string $favoriteOf, string $favorite)
    {
        $this->favoriteOf = $favoriteOf;
        $this->favorite = $favorite;
    }

    /**
     * @return mixed
     */
    public function getFavoriteOf()
    {
        return $this->favoriteOf;
    }

    /**
     * @return mixed
     */
    public function getFavorite()
    {
        return $this->favorite;
    }
}