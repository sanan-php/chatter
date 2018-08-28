<?php

namespace Chat\Controllers;


use Chat\Core\Headers;
use Chat\Core\ServiceBinder;
use Chat\Entity\Favorite;
use Chat\Helpers\Url;
use Chat\Managers\FavoriteManager;

class FavoriteController extends BaseController
{
    /** @var FavoriteManager */
    private $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = ServiceBinder::bind(FavoriteManager::class);
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function getCreate()
    {
        $add = $this->request->post('add');
        if(!$this->tryAuth(false)) {
            Headers::set()->forbidden();
            $this->response->forbidden();
        }
        if(!$this->isPostQuery()) {
            Headers::set()->conflict();
            $this->response->jsonFromArray([
                'errorMess' => $this->l10n['messages']['conflict']
            ]);
        }
        $result = $this->manager->create($this->getCurrentUser()->getId(), $add);
        if(!$result) {
            Headers::set()->conflict();
            $this->response->jsonFromArray([
                'errorMess' => $this->l10n['favorites']['notCreated']
            ]);
        }
        $this->response->jsonFromArray([
            'success' => true,
            'content' => json_decode($result, true)
        ]);
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getAll()
    {
        $this->tryAuth(false);
        $userId = $this->getCurrentUser()->getId();
        $endPosition = (int) $this->request->get('endPosition');
        $startPosition = (int) $this->request->get('startPosition');
        $favorites = $this->manager->getAll($userId, $endPosition, $startPosition);
        $this->response->render('favorite:getAll', [
            'currentUser' => $this->getCurrentUser(),
            'favorites' => $favorites,
            'links' => [
                'profile' => Url::createLinkToAction('user','profile'),
                'logout' => Url::createLinkToAction('user','logout'),
                'chat' => Url::createLinkToAction('chat','private'),
            ],
            'labels' => $this->l10n['favorites']
        ]);
    }
}