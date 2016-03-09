<?php

namespace caixa\modules\application\models;

use caixa\modules\application\models\Debito;
use caixa\modules\application\models\Deposito;
use caixa\modules\application\models\Saldo;
use InvalidArgumentException;
use RuntimeException;

class Transferencia
{
    private $pdo;
    private $conta;
    private $valor;
    private $contaDestino;

    public function __construct(\PDO $pdo, $conta = null)
    {
        if (is_null($conta)) {
            throw new InvalidArgumentException('Número da conta deve ser informado.');
        }

        $this->pdo = $pdo;
        $this->conta = $conta;
    }

    public function setValor($valor)
    {
        if (!is_numeric($valor)) {
            throw new InvalidArgumentException('O valor para efetuar a transferência deve ser do tipo numérico.');
        }

        $this->valor = $valor;
    }

    public function setContaDestino($contaDestino)
    {
        if (!is_numeric($contaDestino)) {
            throw new InvalidArgumentException('Conta inválida. A conta deve conter somente números.');
        }

        $this->contaDestino = $contaDestino;
    }

    public function efetuar()
    {
        $debito = new Debito($this->pdo, $this->conta);
        $debito->debitar($this->valor);

        $deposito = new Deposito($this->pdo, $this->contaDestino);
        $deposito->depositar($this->valor);
    }
}
