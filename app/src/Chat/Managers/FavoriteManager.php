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
	 * @param string $favoriteId
	 * @return bool|mixed|string
	 * @throws \Exception
	 */
	public function create(User $forUser, string $favoriteId)
	{
		if(!$this->userManager->getById($favoriteId)) {
			return false;
		}
		$serialized = $this->serializer->serialize($this->userManager->getById($favoriteId), 'json');
		if(self::isFavorite($forUser->getId(),$favoriteId)) {
			return false;
		}
		$favorite = new Favorite($forUser->getId(), $serialized);
		$favorite->setId($this->generateItemId($favorite::getEntityName(), $forUser->getId()));
		$result = $this->db->setList($favorite::getEntityName(), $forUser->getId(), $favorite);
		if(!$result) {
			return false;
		}
		
		return $this->serializer->serialize($result,'json');
	}

	public function delete(User $forUser, string $favorite)
	{
		if(!$this->userManager->getById($favorite)) {
			return false;
		}
		/** @var Favorite[] $favorites */
		$favorites = $this->db->getList(Favorite::getEntityName(),$forUser->getId());
		foreach ($favorites as $item) {
			/** @var User $find */
			$find = $this->serializer->deserialize($item->getFavorite(), User::class, 'json');
			if($favorite !== $find->getId()) {
				continue;
			}
			$favorite = $this->serializer->serialize($item,'json');
		}
		
		return $this->db->delListItem(Favorite::getEntityName(), $forUser->getId(), $favorite);
	}

	public function getAll(string $forUser, int $endPosition = 30, int $startPosition = 0)
	{
		/** @var Favorite[] $favorites */
		$favorites = $this->db->getList(Favorite::getEntityName(), $forUser, $endPosition, $startPosition);
		/** @var User[] $results */
		$results = [];
		foreach ($favorites as $favorite) {
			$results[] = $this->serializer->deserialize($favorite->getFavorite(), User::class, 'json');
		}
		
		return $results;
	}

	public static function isFavorite(string $userId, string $favoriteId)
	{
		$currentManager = new self();
		if(!$currentManager->userManager->getById($userId)) {
			return false;
		}
		if(!$currentManager->userManager->getById($favoriteId)) {
			return false;
		}
		/** @var User[] $favorites */
		$favorites = $currentManager->getAll($userId,0);
		foreach ($favorites as $favorite) {
			if($favorite->getId() !== $favoriteId) {
				continue;
			}
			return true;
		}
		
		return false;
	}
}