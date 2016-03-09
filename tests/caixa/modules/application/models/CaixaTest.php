<?php

/*
Acesso aos dados da conta:
Número do cartão
    Tipo text
    Somente números
senha
    Tipo password
    Somente números
Botões:
    Entrar
    Limpar

Criar método para validar input do usuário. (validarEntrada(string):bool)
*/

class CaixaTest extends PHPUnit_Extensions_Database_TestCase
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
        $caixa = new \caixa\modules\application\models\Caixa();
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Número da conta deve ser informado.
     */
    public function instanciaDeveLancarInvalidArgumentExceptionCasoONumeroDaContaNaoSejaInformado()
    {
        $conn = $this->getConnection()->getConnection();
        $caixa = new \caixa\modules\application\models\Caixa($conn);
    }

    /**
     * @test
     * @dataProvider numerosParaTestar
     */
    public function numeroDoCartaoDeveSerSomenteNumero($entrada)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;
        $caixa = new caixa\modules\application\models\Caixa($conn, $conta);
        $this->assertTrue($caixa->validarEntrada($entrada));
    }

    /**
     * @test
     * @dataProvider contasParaTestar
     */
    public function deveRetornarBooleanInformandoSeAContaExisteOuNao($retornoEsperado, $contaTeste)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;
        $caixa = new caixa\modules\application\models\Caixa($conn, $conta);

        $existe = $caixa->contaExiste($contaTeste);

        $this->assertSame($retornoEsperado, $existe);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Conta inválida.
     * @dataProvider contasInvalidasParaTestar
     */
    public function deveLancarInvalidArgumentExceptionSeONumeroDaContaNaoForValido($contaTeste)
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;
        $caixa = new caixa\modules\application\models\Caixa($conn, $conta);

        $this->assertTrue(method_exists($caixa, 'obterDadosConta'), 'O método obterDadosConta() deve existir.');

        $dados = $caixa->obterDadosConta($contaTeste);
    }

    /**
     * @test
     */
    public function deveRetornarOsDadosDaContaInformada()
    {
        $conn = $this->getConnection()->getConnection();
        $conta = 12345;
        $caixa = new caixa\modules\application\models\Caixa($conn, $conta);

        $this->assertTrue(method_exists($caixa, 'obterDadosConta'), 'O método obterDadosConta() deve existir.');

        $contaConsultar = 123;
        $dados = $caixa->obterDadosConta($contaConsultar);

        $this->assertInternalType('array', $dados, 'O retorno deveria ser um array');
        $this->assertArrayHasKey('numero_conta', $dados);
        $this->assertEquals($contaConsultar, $dados['numero_conta']);
    }

    public function numerosParaTestar()
    {
        return [
            ['1'],
            ['125'],
            ['65198462132'],
            ['30.58'],
            ['4.678,45'],
        ];
    }

    public function contasParaTestar()
    {
        return [
            [true, 12345],
            [true, 123],
            [false, 123987],
        ];
    }

    public function contasInvalidasParaTestar()
    {
        return [
            [null],
            ['abc'],
            ['abc123'],
        ];
    }
}
