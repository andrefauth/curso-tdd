<?php

namespace caixa\modules\application\models;

use InvalidArgumentException;

class Caixa
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

    public function contaExiste($conta)
    {
        $statement = $this->pdo->prepare('select count(id) as qtd from conta where numero_conta = :conta');
        $statement->bindParam(':conta', $conta);
        $statement->execute();

        if ($statement->fetchColumn() == 0) {
            return false;
        }

        return true;
    }

    public function obterDadosConta($conta)
    {
        if (!is_numeric($conta)) {
            throw new InvalidArgumentException('Conta inválida.');
        }

        $statement = $this->pdo->prepare('select * from conta where numero_conta = :conta');
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->bindValue(':conta', $conta);

        if (!$statement->execute()) {
            return false;
        }

        return $statement->fetch();
    }

    public function validarEntrada($entrada)
    {
        // Tentando fazer a ER funcionar.
        // http://www.phpliveregex.com/
        // var_dump(preg_match('/^[0-9.,]{1,}$/', $entrada));
        // exit();

        if (preg_match('/^[0-9.,]{1,}$/', $entrada)) {
            return true;
        }

        return false;
    }
}
