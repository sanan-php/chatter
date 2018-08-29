<?php

namespace Chat\Controllers;

use Chat\Core\Headers;
use Chat\DTO\UserDto;
use Chat\Entity\User;
use Chat\Helpers\Url;

class UserController extends BaseController
{

    /**
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
     * @throws \Exception
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
				'email' => $login
			];
			if(empty($name) || empty($login) || empty($pass)) {
				$params['error'] = $this->l10n['regPage']['error'].' Не все поля заполнены.';
				$this->response->render('user:reg', $params);
			}
			if(\strlen($pass) < 8) {
                $params['error'] = $this->l10n['regPage']['error'].' Пароль содержит меньше 8 знаков.';
                $this->response->render('user:reg', $params);
            }
			$dto = new UserDto($name, $login, $pass, $this->geo->get_value('city'));
			/** @var User $user */
			$user = $this->userManager->create($dto);
			if(!$user) {
			    $params['error'] = $this->l10n['regPage']['error'] . ' К сожалению, не удалось Вас зарегистрировать';
				$this->response->render('user:reg', $params);
			}
			$this->userManager->authorize($user);
		}
		$this->response->render('user:reg',[
			'labels' => $this->l10n['regPage'],
            'links' => [
                'reg' => Url::createLinkToAction('user','registration'),
                'login' => Url::createLinkToAction('user','login'),
            ],
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
				$this->response->render('user:login', $params);
			}
			$this->response->redirect(Url::createLinkToAction('user','profile'));
		}
		$this->response->render('user:login', $params);
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
		$this->response->render('user:allUsers', [
			'currentUser' => $this->getCurrentUser(),
			'users' => $this->userManager->getAll($limit, $offset, true),
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
			'rand' => false,
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
				$this->response->render('user:profile', $params);
			}
			if(!$this->userManager->update($this->getCurrentUser(), $name, $sex, $pic)) {
				$params['error'] = $this->l10n['profile']['fileUploadError'];
				$this->response->render('user:profile', $params);
			}
			$this->response->redirect(Url::createLinkToAction('user','all'));
		}
		$this->response->render('user:profile', $params);
	}

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
	public function getSearch()
    {
        $param = $this->request->post('queryString');
        $isAjax = $this->request->post('isAjax');
        if(!$this->tryAuth(false)) {
            Headers::set()->forbidden();
            $this->response->forbidden();
        }
        $limit = (int) $this->request->get('limit');
        $offset = (int) $this->request->get('offset');
        $allUsers = $this->userManager->getAll($limit,$offset);
        $list = [];
        foreach ($allUsers as $user) {
            if(!\in_array($param,[$user->getName(),$user->getEmail()])) {
                continue;
            }
            $list[] = $user;
        }
        if($isAjax) {
            $this->response->jsonFromArray([
               'find' => $list
            ]);
        }
        $this->response->render('user:searchResults', [
            'currentUser' => $this->getCurrentUser(),
            'users' => $list,
            'links' => [
                'profile' => Url::createLinkToAction('user','profile'),
                'logout' => Url::createLinkToAction('user','logout'),
                'chat' => Url::createLinkToAction('chat','private'),
            ],
            'labels' => $this->l10n['allUsers']
        ]);
    }
}