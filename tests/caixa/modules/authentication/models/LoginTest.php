<?php

// Autenticação:
//      Autenticar
//          Verificar tamanho do conteúdo dos campos.
//          Verificar se os dados da conta existem no banco para permitir acesso.
//              Caso existam:
//                  Capturar os dados do cliente. (nome, saldo)
//                  Redirecionar para a tela inicial com o menu de opções.
//                  Exibir uma saudação (Bem vindo [nome do cliente])
//              Caso não existam:
//                  Exibir a mensagem (Dados incorretos!)

use caixa\dataAccess\Pdo;
use caixa\modules\authentication\models\Login;

class LoginTest extends PHPUnit_Extensions_Database_TestCase
{
    private $conn;

    public function getConnection()
    {
        if (!$this->conn) {
            $config = parse_ini_file('tests/caixa/bootstrap/db.ini');
            $pdo = new \Piano\Config\Pdo($config);

            $this->conn = $this->createDefaultDBConnection($pdo->get(), $config['dbName']);
        }

        return $this->conn;
    }

    public function getDataSet()
    {
        return $this->createXMLDataSet('tests/caixa/bootstrap/conta.xml');
    }

    /**
     * @test
     */
    public function metodoAutenticarDeveRetornarDadosDoUsuarioParaFazerLogin()
    {
        $conn = $this->getConnection()->getConnection();

        $login = new Login($conn);

        $numeroCartao = 12345;
        $senha = 12345;

        $dados = $login->autenticar($numeroCartao, $senha);

        $this->assertInternalType('array', $dados);
        $this->assertFalse(empty($dados), 'O array de dados deve conter dados do usuário.');
    }

    /**
     * @test
     */
    public function metodoAutenticarDeveRetornarFalseCasoOsDadosDoUsuarioNaoExistam()
    {
        $conn = $this->getConnection()->getConnection();

        $login = new Login($conn);

        $numeroCartao = 'blablabla';
        $senha = 'blablabla';

        $dados = $login->autenticar($numeroCartao, $senha);

        $this->assertFalse($dados);
    }
}