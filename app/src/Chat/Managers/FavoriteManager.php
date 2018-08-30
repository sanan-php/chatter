<?php

namespace Chat\Managers;

use Chat\Core\ServiceBinder;
use Chat\Entity\Favorite;
use Chat\Entity\User;

class FavoriteManager extends AbstractManager
{
    /** @var UserManager */
    private $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = ServiceBinder::bind(UserManager::class);
    }

    /**
     * @param User $forUser
     * @param string $favorite
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function create(User $forUser, string $favorite)
    {
        if(!$this->userManager->getById($favorite)) {
            return false;
        }
        $favorite = $this->serializer->serialize($this->userManager->getById($favorite), 'json');
        $favorite = new Favorite($forUser, $favorite);
        $favorite->setId($this->generateItemId($favorite::getEntityName(), $forUser->getId()));
        $result = $this->db->setList($favorite::getEntityName(), $forUser->getId(), $favorite);
        if(!$result) {
            return false;
        }

        return $this->serializer->serialize($result,'json');
    }

    public function delete(string $forUser, string $favorite)
    {
        if(!$this->userManager->getById($forUser)) {
            return false;
        }
        if(!$this->userManager->getById($favorite)) {
            return false;
        }

        return $this->db->delListItem(Favorite::getEntityName(), $forUser, $favorite);
    }

    public function getAll(string $forUser, int $endPosition = 30, int $startPosition = 0)
    {
        return $this->db->getList(Favorite::getEntityName(), $forUser, $endPosition, $startPosition);
    }

    public static function isFavorite(User $user, string $favoriteId)
    {
        $currentManager = new self();
        if(!$currentManager->userManager->getById($favoriteId)) {
            return false;
        }
        /** @var Favorite[] $favorites */
        $favorites = $currentManager->getAll($user->getId(),0);
        foreach ($favorites as $favorite) {
            if($favorite->getId() !== $favoriteId) {
                continue;
            }

            return true;
        }

        return false;
    }
}