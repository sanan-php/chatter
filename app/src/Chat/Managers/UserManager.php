<?php

namespace Chat\Managers;

use Chat\Core\Reference;
use Chat\Core\SavingTimes;
use Chat\DTO\UserDto;
use Chat\Entity\User;
use Chat\Helpers\Logger;

class UserManager extends AbstractManager
{

    /**
     * @param UserDto $userDto
     * @return bool
     * @throws \Exception
     */
	public function create(UserDto $userDto)
	{
		if (!$this->isEmail($userDto->getEmail())) {
			Logger::write('Неверный емейл : ' . $userDto->getEmail() . ';' . __LINE__ . ';' . __CLASS__);
			return false;
		}
		$user = new User();
		$user->setId(md5(time().random_int(1111,9999)));
		$user->setName($userDto->getName());
		$user->setEmail($userDto->getEmail());
		$user->setPass($this->passHash($userDto->getPass()));

		return $this->db->setItem($user::getEntityName(), $user->getId(), $user);
	}

	/**
	 * @param User $user
	 * @param string $name
	 * @param integer $sex
	 * @param array $pic
	 * @return bool
	 */
	public function update(User $user, string $name, int $sex = 0, array $pic = [])
	{
		if ($sex === 0 && \count($pic) < 2) {
			return false;
		}
		if ($pic['size'] > 0) {
			$allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
			$fileTmp = $pic['tmp_name'];
			$fileName = $pic['name'];
			$imgExt = strtolower(end(explode('.', $fileName)));
			$data = file_get_contents($fileTmp);
			list($width, $height, $type, $attr) = getimagesize($fileTmp);
			if (!\in_array($imgExt, $allowedExt, false)) {
				Logger::write('Формат изображения не допустим к загрузке; User: ' . $user->getEmail() . ';' . __LINE__ . ';' . __CLASS__);
				return false;
			}
			if ($width > 160 || $height > 160) {
				Logger::write('Размеры изображения превышают допустимые 160х160; User: ' . $user->getEmail() . ';' . __LINE__ . ';' . __CLASS__);
				return false;
			}
			$base64pic = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$user->setPic($base64pic);
		}
		if (\in_array($sex,[1,2],false)){
			$user->setSex($sex);
		}
		if ($name !== $user->getName()) {
			$user->setName($name);
		}

		return $this->db->putItem('User',$user->getId(), $user);
	}

	public function authorize(User $user)
	{
		$hash = $this->hashGen($user->getId(), $user->getEmail());
		setcookie(Reference::UID_COOKIE,$user->getId(),strtotime(SavingTimes::AUTH),'/',HOST);
		setcookie(Reference::HASH_COOKIE,$hash,strtotime(SavingTimes::AUTH),'/',HOST);
	}

	public function checkUser(string $login, string $pass)
	{
		$user = $this->getByLogin($login);
		if (!$user) {
			return false;
		}
		if (!\in_array($user->getPass(), [$this->passHash($pass), $this->passHash($pass,true)],false)) {
			Logger::write('Неверный пароль: ' . $login . ';' . __LINE__ . ';' . __CLASS__);
			return false;
		}
		$this->authorize($user);

		return true;
	}

	public function logout()
	{
		setcookie(Reference::UID_COOKIE, '',time()-3000,'/',HOST);
		setcookie(Reference::HASH_COOKIE, '', time()-3000,'/',HOST);
	}

	public function getById(string $id)
	{
		return $this->db->getItem(User::getEntityName(), $id);
	}

	public function checkAuth(string $id, string $hash)
	{
		/** @var User $user */
		$user = $this->db->getItem(User::getEntityName(), $id);
		if (!$user) {
			Logger::write('Пользователь не найден: ' . $id . ';' . __LINE__ . ';' . __CLASS__);
			return false;
		}

		return ($this->hashGen($id, $user->getEmail()) === $hash);
	}

	public function clearAuth()
	{
		setcookie(Reference::UID_COOKIE,'',time()-3000,'/', HOST);
		setcookie(Reference::HASH_COOKIE,'',time()-3000,'/', HOST);
	}

	public function passHash(string $pass, $old = false)
	{
		if ($old === true) {
			return sha1(md5($pass.SALT));
		}
		
		return hash('sha256', $pass.SALT);
	}

	public function hashGen(string $id, string $email)
	{
		return sha1(base64_encode(AUTH_KEY . ":{$id}:{$email}"));
	}

	/**
	 * @param $login
	 * @return User|bool
	 */
	public function getByLogin($login)
	{
		/** @var User[] $user */
		$users = $this->getAll(0);
		if (!\count($users)) {
			Logger::write('Ошибка перебора пользователей;' . __LINE__ . ';' . __CLASS__);

			return false;
		}
		foreach ($users as $user) {
			if ($user->getEmail() !== $login) {
				continue;
			}

			return $user;
		}
		Logger::write('Пользователь не найден: ' . $login . ';' . __LINE__ . ';' . __CLASS__);

		return false;
	}

	/**
	 * @param int $limit
	 * @param int $offset
	 * @param bool $shuffle
	 * @return User[]
	 */
	public function getAll(int $limit = 30, $offset = 0, $shuffle = false)
	{
		return $this->db->getValues('User', $limit, $offset, $shuffle);
	}

	protected function isEmail(string $email)
	{
		return filter_var($email,FILTER_VALIDATE_EMAIL);
	}
}
