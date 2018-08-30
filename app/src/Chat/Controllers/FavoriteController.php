<?php

namespace Chat\Controllers;


use Chat\Core\Headers;
use Chat\Core\ServiceBinder;
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
     * @throws \Exception
     */
    public function getCreate()
    {
        $add = $this->request->post('add');
        if(!$this->tryAuth(false)) {
            Headers::set()->forbidden();
            $this->response->jsonFromArray([
                'error' => $this->l10n['main']['forbidden'],
            ]);
        }
        if(!$this->isPostQuery()) {
            Headers::set()->conflict();
            $this->response->jsonFromArray([
                'errorMess' => $this->l10n['main']['conflict']
            ]);
        }
        $result = $this->manager->create($this->getCurrentUser(), $add);
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

    public function getDelete()
    {
        $add = $this->request->post('add');
        if(!$this->tryAuth(false)) {
            Headers::set()->forbidden();
            $this->response->jsonFromArray([
                'error' => $this->l10n['main']['forbidden'],
            ]);
        }
        if(!$this->isPostQuery()) {
            Headers::set()->conflict();
            $this->response->jsonFromArray([
                'errorMess' => $this->l10n['main']['conflict']
            ]);
        }
        $result = $this->manager->delete($this->getCurrentUser()->getId(), $add);
        if(!$result) {
            Headers::set()->conflict();
            $this->response->jsonFromArray([
                'errorMess' => $this->l10n['favorites']['notDeleted']
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
            'links' => $this->mainLinks(),
            'labels' => array_merge($this->l10n['main'],$this->l10n['favorites'])
        ]);
    }
}