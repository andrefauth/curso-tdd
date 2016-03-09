<?php

namespace caixa\modules\application\controllers;

use caixa\BaseController;
use caixa\modules\application\models\Saldo;
use caixa\modules\application\models\Deposito;
use caixa\modules\application\models\Debito;
use caixa\modules\application\models\Transferencia;
use caixa\modules\application\models\Caixa;
use MessageFormatter;

class IndexController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function indexAction()
    {
        // Alterar o nome deste método para 'menuAction'
        $this->view->render('index');
    }

    public function saldoAction()
    {
        // Instanciar o objeto de saldo passando o número da conta
        // como parâmetro do construtor.
        $saldo = new Saldo($this->pdo, $_SESSION['numero_conta']);

        $valorSaldo = $saldo->consultar();

        $this->view->render(
            'saldo',
            [
                'valorSaldo' => $valorSaldo
            ]
        );
    }

    public function depositoAction()
    {
        $this->view->render('deposito');
    }

    public function saqueAction()
    {
        $this->view->render('saque');
    }

    public function transferenciaAction()
    {
        $this->view->render('transferencia');
    }

    public function depositoEfetuarAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $valorDeposito = $_POST['valor-deposito'];

            $deposito = new Deposito($this->pdo, $_SESSION['numero_conta']);

            $deposito->depositar($valorDeposito);


            $caixa = new Caixa($this->pdo, $_SESSION['numero_conta']);
            $dadosConta = $caixa->obterDadosConta($_SESSION['numero_conta']);

            $config = $this->getApplication()->getConfig()->get();
            $novoSaldo = MessageFormatter::formatMessage($config['defaultLocale'], '{0, number, currency}', [$dadosConta['saldo']]);

            $_SESSION['message'] = 'Depósito efetuado com sucesso.<br>Saldo atual: ' . $novoSaldo;
        }

        $this->redirect('/application/index/deposito');
    }

    public function saqueEfetuarAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $valorSaque = $_POST['valor-saque'];

            $debito = new Debito($this->pdo, $_SESSION['numero_conta']);

            if (!$debito->podeDebitar($valorSaque)) {
                $_SESSION['message'] = 'Saque não permitido. Saldo insuficiente ou limite atingido.';
                $this->redirect('/application/index/saque');
            }

            $debito->debitar($valorSaque);

            $caixa = new Caixa($this->pdo, $_SESSION['numero_conta']);
            $dadosConta = $caixa->obterDadosConta($_SESSION['numero_conta']);

            $config = $this->getApplication()->getConfig()->get();
            $novoSaldo = MessageFormatter::formatMessage($config['defaultLocale'], '{0, number, currency}', [$dadosConta['saldo']]);

            $_SESSION['message'] = 'Saque efetuado com sucesso.<br>Saldo atual: ' . $novoSaldo;
        }

        $this->redirect('/application/index/saque');
    }

    public function transferenciaConfirmarAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $valorTransferir = $_POST['valor-transferir'];
            $contaDestino = $_POST['numero-conta'];

            $caixa = new Caixa($this->pdo, $_SESSION['numero_conta']);
            $dadosContaDestino = $caixa->obterDadosConta($contaDestino);
            $dadosConta = $caixa->obterDadosConta($_SESSION['numero_conta']);

            if (!$dadosContaDestino) {
                $_SESSION['message'] = sprintf('A conta %s não existe.', $contaDestino);
                $this->redirect('/application/index/transferencia');
            }
        }

        $this->view->render(
            'transferencia-confirmar',
            [
                'contaDestino' => $dadosContaDestino,
                'valorTransferir' => $valorTransferir,
                'saldoAtual' => $dadosConta['saldo'],
            ]
        );
    }

    public function transferenciaEfetuarAction()
    {
        $valorTransferencia = $this->getParam('valor');
        $contaDestino = $this->getParam('conta');

        $transferencia = new Transferencia($this->pdo, $_SESSION['numero_conta']);
        $transferencia->setValor($valorTransferencia);
        $transferencia->setContaDestino($contaDestino);
        $transferencia->efetuar();

        $caixa = new Caixa($this->pdo, $_SESSION['numero_conta']);
        $dadosConta = $caixa->obterDadosConta($_SESSION['numero_conta']);

        $config = $this->getApplication()->getConfig()->get();
        $novoSaldo = MessageFormatter::formatMessage($config['defaultLocale'], '{0, number, currency}', [$dadosConta['saldo']]);

        $_SESSION['message'] = "Transferência efetuada com sucesso.<br>Saldo atual: " . $novoSaldo;

        $this->redirect('/application/index/transferencia');
    }
}
