# Curso TDD.

### Requisitos

Vagrant e Virtualbox

```sh
$ git clone https://github.com/agenciasys/curso-tdd.git
$ cd curso-tdd/vagrant
$ vagrant box add workshop_tdd https://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box
$ vagrant up
```

### Executando os testes:

```sh
$ cd curso-tdd/vagrant
$ vagrant ssh
$ cd /www/curso-tdd
$ ./vendor/bin/phpunit
```

Htdocs
- localhost:8080

Projeto
- localhost:8989
