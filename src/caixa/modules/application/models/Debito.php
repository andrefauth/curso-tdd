<?php

namespace caixa\modules\application\models;

use caixa\modules\application\models\Saldo;
use InvalidArgumentException;

class Debito
{
    private $pdo;
    private $conta;
    private $limiteDebito = 500;

    public function __construct(\PDO $pdo, $conta = null)
    {
        if (is_null($conta)) {
            throw new InvalidArgumentException('Número da conta deve ser informado.');
        }

        $this->pdo = $pdo;
        $this->conta = $conta;
    }

    public function podeDebitar($valor = null)
    {
        if (is_null($valor)) {
            throw new InvalidArgumentException('Erro. Valor nulo não pode ser debitado.');
        }

        if (!is_numeric($valor)) {
            throw new InvalidArgumentException('O valor a ser debitado deve ser do tipo numérico.');
        }

        $saldo = new Saldo($this->pdo, $this->conta);
        $saldoAtual = $saldo->consultar();

        if ($valor <= $this->limiteDebito && $saldoAtual >= $valor) {
            return true;
        }

        return false;
    }

    public function debitar($valor)
    {
        $this->podeDebitar($valor);

        $saldo = new Saldo($this->pdo, $this->conta);
        $saldoAnterior = $saldo->consultar();

        $novoSaldo = $saldoAnterior - $valor;

        $saldo->atualizar($novoSaldo);
    }
}
