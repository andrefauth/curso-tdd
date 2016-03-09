<?php

/*
Validar os dados informados (Caixa::validarEntrada(string))
Capturar o saldo da conta origem (atual) ($saldo->consultar())
Obter valor a ser transferido.
Obter a conta destino.
     Verificar se a conta destino existe.
     Caso positivo:
         - Exibir mensagem mostrando o nome do favorecido.
     Caso negativo:
         - Exibir mensagem informando que a conta informada não existe.
Fazer a verificação do valor a ser transferido (Debito::podeDebitar(float))
     Caso positivo:
         - Efetua a transferência ($this->efetuar())
             Debitar valor da conta atual.
             Depositar o valor na conta destino.
     Caso negativo:
         - Exibir saldo atual.
         - Exibir limite para débito.
*/

class TransferenciaTest extends PHPUnit_Extensions_Database_TestCase
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
     * @expectedException PHPUnit_Framework_Error
     */
    public function instanciaDeveLancarInvalidArgumentExceptionSeOObjetoDeConexaoForOmitido()
    {
        $transferencia = new \caixa\modules\application\models\Transferencia();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Número da conta deve ser informado.
     */
    public function instanciaDeveLancarInvalidArgumentExceptionCasoONumeroDaContaNaoSejaInformado()
    {
        $conn = $this->getConnection()->getConnection();
        $transferencia = new \caixa\modules\application\models\Transferencia($conn);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage O valor para efetuar a transferência deve ser do tipo numérico.
     * @dataProvider valoresTeste
     */
    public function deveLancarInvalidArgumentExceptionCasoOvalorParaTransferenciaNaoSejaValido($valorTransferencia)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;
        $transferencia = new \caixa\modules\application\models\Transferencia($conn, $conta);

        $this->assertTrue(method_exists($transferencia, 'setValor'), 'Método setValor() deve existir.');

        $contaDestino = 123;
        $sucesso = $transferencia->setValor($valorTransferencia);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Conta inválida. A conta deve conter somente números.
     * @dataProvider contasTeste
     */
    public function deveLancarInvalidArgumentExceptionCasoONumeroDaContaNaoSejaValido($contaDestino)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;
        $transferencia = new \caixa\modules\application\models\Transferencia($conn, $conta);

        $this->assertTrue(method_exists($transferencia, 'setContaDestino'), 'Método setContaDestino() deve existir.');

        $valorTransferencia = 50;
        $sucesso = $transferencia->setContaDestino($contaDestino);
    }

    /**
     * @test
     */
    public function deveEfetuarATransferenciaCasoOsDadosInformadosSejamValidos()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;
        $contaDestino = 123;
        $valorTransferencia = 50;

        $saldoContaPrincipal = new \caixa\modules\application\models\Saldo($conn, $conta);
        $saldoAnterior = $saldoContaPrincipal->consultar();

        $saldoContaDestino = new \caixa\modules\application\models\Saldo($conn, $contaDestino);
        $saldoAnteriorContaDestino = $saldoContaDestino->consultar();

        $transferencia = new \caixa\modules\application\models\Transferencia($conn, $conta);

        $this->assertTrue(method_exists($transferencia, 'efetuar'), 'Método efetuar() deve existir.');

        $transferencia->setValor($valorTransferencia);
        $transferencia->setContaDestino($contaDestino);
        $transferencia->efetuar();

        $saldoAtual = $saldoContaPrincipal->consultar();
        $saldoAtualContaDestino = $saldoContaDestino->consultar();

        $this->assertEquals($saldoAnterior - $valorTransferencia, $saldoAtual, 'Conta principal não atualizou o valor corretamente.');
        $this->assertEquals($saldoAnteriorContaDestino + $valorTransferencia, $saldoAtualContaDestino, 'Conta destino não atualizou o valor corretamente.');
    }

    public function valoresTeste()
    {
        return [
            [null],
            ['abc'],
            ['abc123'],
        ];
    }

    public function contasTeste()
    {
        return [
            [null],
            ['abc'],
            ['abc123'],
        ];
    }
}
