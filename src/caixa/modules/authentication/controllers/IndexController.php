<?php

namespace caixa\modules\authentication\controllers;

use Piano\Config\Pdo;
use caixa\modules\authentication\models\Login;
use Piano\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $config = $this->getApplication()->getConfig()->get(getenv('APPLICATION_ENV'));
            $pdo = new Pdo($config);
            $login = new Login($pdo->get());

            $dados = $login->autenticar($_POST['numero-cartao'], $_POST['senha']);

            if (!$dados) {
                $_SESSION['message'] = 'Dados incorretos!';
                $this->redirect('/authentication/index/index');
            }

            $_SESSION['id']           = session_id();
            $_SESSION['id_user']      = $dados['id'];
            $_SESSION['numero_conta'] = $dados['numero_conta'];
            $_SESSION['senha']        = $dados['senha'];
            $_SESSION['nome_cliente'] = $dados['nome_cliente'];
            $_SESSION['saldo']        = $dados['saldo'];

            $this->redirect('/application/index/index');
        }

        $this->view->render('index');
    }

    public function logoutAction()
    {
        session_destroy();

        $this->redirect('/authentication/index/index');
    }
}
