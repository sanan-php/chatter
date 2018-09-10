<?php
namespace Chat\Common\Helpers;

class Finder
{
	public static function findOn($entity, $sort = 0)
	{
		$list = scandir(ROOT . '/data/' . $entity, $sort);
		if (!$list) {
			return false;
		}
		if ($sort === 0) {
			unset($list[0],$list[1]);
		} else {
            unset($list[\count($list)-1], $list[\count($list)-1]);
		}
		$entities = [];
		foreach ($list as $id => $filename) {
			if (!is_file(ROOT . '/data/' . $entity . DIRECTORY_SEPARATOR . $filename)) {
				unset($list[$id]);
				continue;
			}
			$entities[] = (int) str_replace('.json','',$filename);
		}

		return $entities;
	}
}