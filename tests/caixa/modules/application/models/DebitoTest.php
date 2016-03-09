<?php

/*
Validar os dados informados (Caixa::validarEntrada(string))
Consultar o saldo atual ($saldo->consultar())
Calcular o limite disponível para débito. (podeDebitar(float):bool)
 - O limite para débito é de no máximo 500
 - O valor do limite deve ser igual ao valor do saldo desde que saldo <= 500
Exibir o saldo atual e o limite disponível para débito.
Subtrair o valor do débito. ($this->debitar($saldoAtual, $valorDebito):float)
Gravar no banco de dados o novo saldo calculado. ($saldo->atualizar(float $saldo))
Consultar novamente o saldo e exibir na tela o novo saldo. ($saldo->consultar())
*/

class DebitoTest extends PHPUnit_Extensions_Database_TestCase
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
        $debito = new \caixa\modules\application\models\Debito();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Número da conta deve ser informado.
     */
    public function instanciaDeveLancarInvalidArgumentExceptionCasoONumeroDaContaNaoSejaInformado()
    {
        $conn = $this->getConnection()->getConnection();
        $debito = new \caixa\modules\application\models\Debito($conn);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /Erro. Valor nulo não pode ser debitado.|O valor a ser debitado deve ser do tip-o numérico./
     * @dataProvider valoresDebitarTeste
     */
    public function deveLancarInvalidArgumentExceptionAoTentarDebitarValorInvalido($valor)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $debito = new \caixa\modules\application\models\Debito($conn, $conta);

        $debito->podeDebitar();
    }

    public function valoresDebitarTeste()
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
    public function deveRetornarFalseSeOValorASerDebitadoForMaisAltoQueOLimiteDisponivel()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $debito = new \caixa\modules\application\models\Debito($conn, $conta);

        $podeDebitar = $debito->podeDebitar(600);

        $this->assertFalse($podeDebitar);
    }

    /**
     * @test
     */
    public function deveRetornarFalseSeOValorASerDebitadoForMaisAltoQueOSaldoDisponivel()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $debito = new \caixa\modules\application\models\Debito($conn, $conta);

        $podeDebitar = $debito->podeDebitar(1500);

        $this->assertFalse($podeDebitar);
    }

    /**
     * @test
     */
    public function deveDebitarSeOsValoresInformadosForemValidos()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;

        $saldo = new \caixa\modules\application\models\Saldo($conn, $conta);
        $saldoAnterior = $saldo->consultar();

        $debito = new \caixa\modules\application\models\Debito($conn, $conta);

        $this->assertTrue(method_exists($debito, 'debitar'), 'Método debitar() deve existir.');

        $valorDebitar = 50;
        $debito->debitar($valorDebitar);
        $valorSaldoComDebito = $saldo->consultar();

        $this->assertEquals($saldoAnterior - $valorDebitar, $valorSaldoComDebito);
    }
}
