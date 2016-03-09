<?php

/*
Deve ser iniciado com o número da conta.
Consultar saldo e retorná-lo. (consultar():float)
Atualizar o saldo. (atualizar(float))
*/

class SaldoTest extends PHPUnit_Extensions_Database_TestCase
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
        $saldo = new \caixa\modules\application\models\Saldo();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Número da conta deve ser informado.
     */
    public function instanciaDeveLancarInvalidArgumentExceptionCasoONumeroDaContaNaoSejaInformado()
    {
        $conn = $this->getConnection()->getConnection();
        $saldo = new \caixa\modules\application\models\Saldo($conn);
    }

    /**
     * @test
     */
    public function oSaldoDaContaDeveSerRetornadoCorretamente()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $saldo = new \caixa\modules\application\models\Saldo($conn, $conta);

        $valorSaldo = $saldo->consultar();

        $this->assertInternalType('double', $valorSaldo);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /Erro. Valor nulo não pode atualizar saldo.|O saldo deve ser do tipo numérico./
     * @dataProvider retornaValoresAtualizar
     */
    public function valorParaAtualizarSaldoDeveSerDoTipoIntegerOuDouble($valorAtualizar)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $saldo = new \caixa\modules\application\models\Saldo($conn, $conta);

        $saldo->atualizar($valorAtualizar);
    }

    /**
     * @test
     */
    public function saldoDeveSerAtualizadoCorretamente()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $saldo = new \caixa\modules\application\models\Saldo($conn, $conta);

        $saldo->atualizar(50);

        $this->assertEquals('50', $saldo->consultar());
    }

    public function retornaValoresAtualizar()
    {
        return [
            [null],
            ['abc'],
            ['abc123'],
        ];
    }
}
