<?php

$routes = [
    'default' => [
        'route' => '/',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'index',
    ],
    'login' => [
        'route' => '/login',
        'module' => 'authentication',
        'controller' => 'index',
        'action' => 'index',
    ],
    'logout' => [
        'route' => '/logout',
        'module' => 'authentication',
        'controller' => 'index',
        'action' => 'logout',
    ],
    'saldo.index.index' => [
        'route' => '/saldo/visualizar',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'saldo',
    ],
    'deposito.index.index' => [
        'route' => '/deposito',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'deposito',
    ],
    'deposito.index.efetuar' => [
        'route' => '/deposito/efetuar',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'depositoEfetuar',
    ],
    'saque.index.index' => [
        'route' => '/saque',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'saque',
    ],
    'saque.index.efetuar' => [
        'route' => '/saque/efetuar',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'saqueEfetuar',
    ],
    'transferencia.index.index' => [
        'route' => '/transferencia',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'transferencia',
    ],
    'transferencia.index.confirmar' => [
        'route' => '/transferencia/confirmar',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'transferenciaConfirmar',
    ],
    'transferencia.index.efetuar' => [
        'route' => '/transferencia/efetuar/:valor/:conta',
        'module' => 'application',
        'controller' => 'index',
        'action' => 'transferenciaEfetuar',
        [
            ':valor' => '[0-9.]+',
            ':conta' => '\d+',
        ]
    ],
];