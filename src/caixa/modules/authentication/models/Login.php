<?php

namespace caixa\modules\authentication\models;

class Login
{
    private $pdo;

    public function __construct(\Pdo $pdo)
    {
        $this->pdo = $pdo;
    }

    public function autenticar($numeroCartao, $senha)
    {
        $statement = $this->pdo->prepare("select * from conta where numero_conta = :numeroCartao and senha = :senha");

        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->bindValue(':numeroCartao', $numeroCartao);
        $statement->bindValue(':senha', $senha);

        if (!$statement->execute()) {
            return false;
        }

        return $statement->fetch();
    }
}
