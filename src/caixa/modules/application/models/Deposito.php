<?php

namespace caixa\modules\application\models;

use caixa\modules\application\models\Saldo;
use InvalidArgumentException;

class Deposito
{
    private $pdo;
    private $conta;

    public function __construct(\PDO $pdo, $conta = null)
    {
        if (is_null($conta)) {
            throw new InvalidArgumentException('Número da conta deve ser informado.');
        }

        $this->pdo = $pdo;
        $this->conta = $conta;
    }

    public function depositar($valor = null)
    {
        if (is_null($valor)) {
            throw new InvalidArgumentException('Erro. Valor nulo não pode ser depositado.');
        }

        if (!is_numeric($valor)) {
            throw new InvalidArgumentException('O valor a ser depositado deve ser do tipo numérico.');
        }

        $saldo = new Saldo($this->pdo, $this->conta);
        $saldoAnterior = $saldo->consultar();

        $novoSaldo = $saldoAnterior + $valor;

        $saldo->atualizar($novoSaldo);
    }
}
