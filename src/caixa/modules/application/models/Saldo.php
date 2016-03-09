<?php

namespace caixa\modules\application\models;

use InvalidArgumentException;

class Saldo
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

    public function consultar()
    {
        $statement = $this->pdo->prepare("select saldo from conta where numero_conta = :conta");
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->bindValue(':conta', $this->conta);

        $statement->execute();
        $dados = $statement->fetch();

        return (double) $dados['saldo'];
    }

    public function atualizar($saldo = null)
    {
        if (is_null($saldo)) {
            throw new InvalidArgumentException('Erro. Valor nulo não pode atualizar saldo.');
        }

        if (!is_numeric($saldo)) {
            throw new InvalidArgumentException('O saldo deve ser do tipo numérico.');
        }

        $statement = $this->pdo->prepare("update conta set saldo = :saldo where numero_conta = :conta");
        $statement->bindValue(':saldo', $saldo);
        $statement->bindValue(':conta', $this->conta);
        $statement->execute();
    }
}
