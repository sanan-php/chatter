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
     * @param string $forUser
     * @param string $favorite
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function create(string $forUser, string $favorite)
    {
        if(!$this->userManager->getById($forUser)) {
            return false;
        }
        if(!$this->userManager->getById($favorite)) {
            return false;
        }
        $favorite = $this->serializer->serialize($this->userManager->getById($favorite), 'json');
        $favorite = new Favorite($forUser, $favorite);
        $favorite->setId($this->generateItemId('Favorite', $forUser));
        $result = $this->db->setList($favorite::getEntityName(), $forUser, $favorite);
        if(!$result) {
            return false;
        }

        return $this->serializer->serialize($result,'json');
    }

    public function getAll(string $forUser, int $endPosition = 30, int $startPosition = 0)
    {
        $result = $this->db->getList(Favorite::getEntityName(), $forUser, $endPosition, $startPosition);
    }
}