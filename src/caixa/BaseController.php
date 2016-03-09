<?php

namespace caixa;

use Piano\Mvc\Controller;
use Piano\Config\Pdo;
use MessageFormatter;

class BaseController extends Controller
{
    protected $pdo;

    protected function initialize()
    {
        $module     = $this->getApplication()->getModuleName();
        $controller = strtolower(preg_replace('/Controller$/', '', $this->getApplication()->getControllerName()));
        $action     = $this->getApplication()->getActionName();
        $location   = $module . '/' . $controller . '/' . $action;

        $this->configurarConexaoPdo();
        $this->adicionarFormatacaoMoeda();

        if (empty($_SESSION)) {
            $this->redirect('/authentication/index/index');
        }
    }

    private function configurarConexaoPdo()
    {
        $dbConfig = $this->getApplication()->getConfig()->get('development');
        $pdo = new Pdo($dbConfig);

        $this->pdo = $pdo->get();
    }

    private function adicionarFormatacaoMoeda()
    {
        $config = $this->getApplication()->getConfig()->get();

        $this->view->addVar('formatarMoeda', function($valor) use ($config) {
            return MessageFormatter::formatMessage($config['defaultLocale'], '{0, number, currency}', [$valor]);
        });
    }
}
