<?php

/*
Validar os dados informados (Caixa::validarEntrada(string))
Consultar o saldo atual ($saldo->consultar())
Somar o valor do saldo atual com o valor informado para depósito. ($this->somar($saldoAtual, $valorDeposito):float)
Gravar no banco de dados o novo saldo calculado. ($saldo->atualizar(float $saldo))
Consultar novamente o saldo e exibir na tela o novo saldo. ($saldo->consultar())
*/

class DepositoTest extends PHPUnit_Extensions_Database_TestCase
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
        $deposito = new \caixa\modules\application\models\Deposito();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Número da conta deve ser informado.
     */
    public function instanciaDeveLancarInvalidArgumentExceptionCasoONumeroDaContaNaoSejaInformado()
    {
        $conn = $this->getConnection()->getConnection();
        $deposito = new \caixa\modules\application\models\Deposito($conn);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /Erro. Valor nulo não pode ser depositado.|O valor a ser depositado deve ser do tipo numérico./
     * @dataProvider valoresDepositarTeste
     */
    public function deveLancarInvalidArgumentExceptionAoTentarDepositarValorInvalido($valor)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $deposito = new \caixa\modules\application\models\Deposito($conn, $conta);

        $deposito->depositar($valor);
    }

    public function valoresDepositarTeste()
    {
        return [
            [null],
            ['abc'],
            ['abc123'],
            [''],
        ];
    }

    /**
     * @test
     */
    public function deveDepositarCorretamenteSomandoOValorDeDepositoAoValorDoSaldo()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $saldo = new \caixa\modules\application\models\Saldo($conn, $conta);
        $saldoAnterior = $saldo->consultar();

        $deposito = new \caixa\modules\application\models\Deposito($conn, $conta);
        $valorDeposito = 500;
        $deposito->depositar($valorDeposito);

        $valorSaldoComDeposito = $saldo->consultar();

        $this->assertEquals($saldoAnterior + $valorDeposito, $valorSaldoComDeposito);
    }
}
