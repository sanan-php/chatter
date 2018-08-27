<?php

namespace Chat\Controllers;

use Chat\Core\Headers;
use Chat\DTO\UserDto;
use Chat\Entity\User;
use Chat\Helpers\Logger;
use Chat\Helpers\Url;

class UserController extends BaseController
{
	/**
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function getRegistration()
	{
		if($this->tryAuth(false)) {
			$this->response->redirect(Url::createLinkToAction('user','profile'));
		}
		if($this->isPostQuery()) {
			$name = $this->request->post('name');
			$login = $this->request->post('login');
			$pass = $this->request->post('password');
			$params = [
				'labels' => $this->l10n['regPage'],
				'links' => [
					'reg' => Url::createLinkToAction('user','registration'),
					'login' => Url::createLinkToAction('user','login'),
				],
				'name' => $name,
				'email' => $login,
				'error' => $this->l10n['regPage']['error']
			];
			if(empty($name) || empty($login) || empty($pass)) {
				Logger::write('Не все поля заполнены');
				$this->response->render('reg', $params);
			}
			$dto = new UserDto($name, $login, $pass);
			/** @var User $user */
			$user = $this->userManager->create($dto);
			if(!$user) {
				$this->response->render('reg', $params);
			}
			$this->userManager->authorize($user);
		}
		$this->response->render('reg',[
			'labels' => $this->l10n['regPage'],
			'name' => '',
			'email' => '',
		]);
	}

	/**
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function getLogin()
	{
		if($this->tryAuth(false)) {
			$this->response->redirect(Url::createLinkToAction('user','profile'));
		}
		$params = [
			'links' => [
				'login' => Url::createLinkToAction('user','login'),
				'reg' => Url::createLinkToAction('user','registration')
			],
			'labels' => $this->l10n['authPage'],
		];
		if($this->isPostQuery()) {
			$login = $this->request->post('login');
			$pass = $this->request->post('password');
			if(!$this->userManager->checkUser($login, $pass)) {
				$params['login'] = $login;
				$params['error'] = $this->l10n['authPage']['error'];
				$this->response->render('login', $params);
			}
			$this->response->redirect(Url::createLinkToAction('user','profile'));
		}
		$this->response->render('login', $params);
	}

	public function getLogout()
	{
		$this->userManager->logout();
		$this->response->redirect(Url::createLinkToAction('user','login'));
	}

	/**
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function getAll()
	{
		if(!$this->tryAuth(false)) {
			Headers::set()->redirect(Url::createLinkToAction('user','login'));
		}
		$limit = (int) $this->request->get('limit');
		$offset = (int) $this->request->get('offset');
		$this->response->render('allUsers', [
			'currentUser' => $this->getCurrentUser(),
			'users' => $this->userManager->getAll($limit, $offset),
			'links' => [
				'profile' => Url::createLinkToAction('user','profile'),
				'logout' => Url::createLinkToAction('user','logout'),
				'chat' => Url::createLinkToAction('chat','private'),
			],
			'labels' => $this->l10n['allUsers']
		]);
	}

	/**
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 * @throws \Exception
	 */
	public function getProfile()
	{
		$this->tryAuth();
		$params = [
			'rand' => random_int(111,99999),
			'labels' => $this->l10n['profile'],
			'user' => $this->getCurrentUser(),
			'links' => [
				'allUsers' => Url::createLinkToAction('user','all'),
				'logout' => Url::createLinkToAction('user','logout'),
				'profile' => Url::createLinkToAction('user','profile')
			]
		];
		if($this->isPostQuery()) {
			$name = (string) $this->request->post('name');
			$pic = $this->request->file('user-pic');
			$sex = (int) $this->request->post('sex');
			if(!$pic) {
				$params['error'] = $this->l10n['profile']['fileIsUndefined'];
				$this->response->render('profile', $params);
			}
			if(!$this->userManager->update($this->getCurrentUser(), $name, $sex, $pic)) {
				$params['error'] = $this->l10n['profile']['fileUploadError'];
				$this->response->render('profile', $params);
			}
			$this->response->redirect(Url::createLinkToAction('user','all'));
		}
		$this->response->render('profile', $params);
	}
}