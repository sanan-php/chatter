<?php

namespace Chat\Core;

use Chat\Entity\Base\Item;
use Chat\Helpers\Logger;
use Predis\Client;

class Db
{
	private $serializer;
	private $db;

	public function __construct()
	{
		try {
			$client = new Client([
				'scheme' => 'tcp',
				'host' => '127.0.0.1',
				'port' => 6379,
			]);
			$this->db = $client;
		} catch (\Exception $e) {
			die($e->getMessage());
		}
		$this->serializer = \JMS\Serializer\SerializerBuilder::create()->build();

	}

	public static function init()
	{
		return new self();
	}

	/**
	 * @param string $entity
	 * @param Item $item
	 * @return object|bool
	 */
	public function writeData(string $entity, Item $item)
	{
		if(!class_exists("Chat\\Entity\\$entity")) {
			Logger::write('Объект не найден : ' . __LINE__ . ';' . __CLASS__);
			return false;
		}
		if($item->getId() === null) {
			Logger::write('Объект не опознан : ' . __LINE__ . ';' . __CLASS__);
			return false;
		}
		$id = $item->getId();
		if ($item->getGroupId() !== null) {
			$id = $item->getGroupId();
		}
		if($this->isExists($entity, $id)) {
			return $this->getContentFromDb($entity, $id);
		}
		$result = $this->setContentInDb($entity, $id, $item);
		if($result === 0) {
			Logger::write('Не удалось создать объект: ' . $result . ';' . __LINE__ . ';' . __CLASS__);
			return false;
		}

		return $item;
	}

	public function update(string $entity, string $id, $content)
	{
		if($this->isExists($entity, $id)) {
			$result = $this->putContentInDb($entity, $id, $content);
			if(!$result) {
				Logger::write('Не удалось обновить объект: ' . __LINE__ . ';' . __CLASS__);

				return false;
			}
			return true;
		}
		Logger::write('Не удалось обновить объект: ' . __LINE__ . ';' . __CLASS__);

		return false;
	}

	public function getAll(string $entity, $limit = 30, $offset = 0)
	{
		$findItemInterfaces = $this->db->hgetall($entity);
		if(!\count($findItemInterfaces) === 0) {
			Logger::write('Объекты не найдены : ' . __LINE__ . ';' . __CLASS__);
			return false;
		}
		$values = '['.implode(',',array_values($findItemInterfaces)).']';
		$items = $this->serializer->deserialize(
			$values,
			"array<Chat\\Entity\\{$entity}>",
			'json'
		);
		if($limit === 0) {
			return $items;
		}

		return \array_slice($items, $offset, $limit);
	}

	public function getItem($entity, $item)
	{
		if(!$this->isExists($entity,$item)) {
			Logger::write('Объект не существует : ' . __LINE__ . ';' . __CLASS__);
			return false;
		}
		$content = $this->getContentFromDb($entity, $item);
		if(empty($content)) {
			Logger::write('Объект не найден : ' . __LINE__ . ';' . __CLASS__);
			return false;
		}

		return $content;
	}

	public function getGroup($entity, $group, $limit = 30, $offset = 0)
	{
		$content = $this->getContentFromDb($entity, $group);
		if(empty($content)) {
			Logger::write('Объект с такими параметрами не найден : ' . __LINE__ . ';' . __CLASS__);
			return [];
		}
		if ($limit === 0) {
			return $content;
		}

		return \array_slice($content, $offset, $limit);
	}

	public function isExists(string $entity, string $id)
	{
		return $this->db->hexists($entity, $id);
	}

	public function getContentFromDb(string $entity, string $id)
	{
		switch($entity) :
			case 'Messages':
				$type = "array<Chat\\Entity\\$entity>";
				break;
			default:
				$type = "Chat\\Entity\\$entity";
				break;
			endswitch;
		$content = $this->db->hget($entity, $id);

		return $this->serializer->deserialize($content, $type,ContentTypes::SAVED_DATA_TYPE);
	}

	private function setContentInDb(string $entity, string $id, $content)
	{
		return $this->db->hset($entity, $id, $this->serializer->serialize($content,ContentTypes::SAVED_DATA_TYPE));
	}

	private function putContentInDb(string $entity, string $id, $content)
	{
		$res = $this->db->hdel($entity,[$id,$this->serializer->serialize($content,ContentTypes::SAVED_DATA_TYPE)]);

		return $this->db->hset($entity, $id, $this->serializer->serialize($content,ContentTypes::SAVED_DATA_TYPE));
	}
}