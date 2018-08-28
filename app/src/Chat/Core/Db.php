<?php

namespace Chat\Core;

use Chat\Helpers\Logger;
use RedisClient\ClientFactory;

class Db
{
	private $serializer;
	private $db;

	public function __construct()
	{
		try {
			$client = ClientFactory::create([
                'server' => '127.0.0.1:6379',
                'timeout' => 2,
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

	public function getItem($entity, string $item)
	{

		if(!$this->db->hexists($entity, $item)) {
            Logger::write('Объект не существует : ' . $entity . ';' . $item . ';' . __LINE__ . ';' . __CLASS__);
            return false;
        }

		return $this->serializer->deserialize($this->db->hget($entity, $item),"Chat\\Entity\\$entity",'json');
	}

    public function setItem(string $entity, string $id, $content)
    {
        $result = $this->db->hset($entity, $id, $this->serializer->serialize($content,'json'));
        if($result === 0) {
            return false;
        }
        $this->db->publish($entity,$id);

        return $content;
    }

    public function putItem(string $entity, string $id, $content)
    {
        $old = $this->db->hdel($entity, [$id]);
        $updated = $this->db->hset($entity, $id, $this->serializer->serialize($content,'json'));

        return ($old === 1 && $updated === 1);
    }

    public function getValues(string $entity, $limit = 30, $offset = 0, $shuffle = false)
    {
        $values = $this->db->hvals($entity);
        if(!\count($values)) {
            return false;
        }
        if($shuffle) {
            shuffle($values);
        }
        $data= '['.implode(',',$values).']';
        $items = $this->serializer->deserialize(
            $data,
            "array<Chat\\Entity\\{$entity}>",
            'json'
        );

        return $items;
    }

	public function getList($entity, $group, $endPosition = 30, $startPosition = 0)
	{
	    if($endPosition === 0){
	        $endPosition = 30;
        }
        $length = $this->db->llen($entity . ':' . $group);
        if($length > $endPosition) {
            $startPosition = $length - $endPosition;
            $endPosition = $length;
        }
		$content = $this->db->lrange($entity . ':'. $group, $startPosition, $endPosition);
		if(!\count($content)) {
			return false;
		}
        $data= '['.implode(',',$content).']';
        $items = $this->serializer->deserialize(
            $data,
            "array<Chat\\Entity\\{$entity}>",
            'json'
        );

		return $items;
	}

	public function setList(string $entity, string $group, $content)
	{
		$result = $this->db->lpush($entity.':'.$group, $this->serializer->serialize($content,ContentTypes::SAVED_DATA_TYPE));
		if($result === 0) {
		    return false;
        }
        $this->db->publish($entity, $group);

        return $content;
	}
}