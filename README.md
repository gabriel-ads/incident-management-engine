# Incident Management Engine

Incident Management é um backend com o foco principal em uma API RESTful para gerenciar incidentes.

## O que você vai encontrar no projeto:

- Lavarel Sail
- CRUD User
- CRUD Incidentes
- Autenticação + JWT
- Job/Queue
- Websockets
- Swagger
- Testes

# Requisitos para rodar o projeto (Clique para baixar)
- [PHP](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/)
- [Docker](https://www.docker.com/products/docker-desktop/)
- [WSL](https://learn.microsoft.com/pt-br/windows/wsl/install)

# Como baixar e rodar o projeto
Após clonar o projeto, dentro de sua pasta, rode o comando:
```sh
composer install
```
Crie uma copia da env.example e a configure conforme seu banco e host para websocket. Deixei o meu pusher caso precise:
```sh
cp .env.example .env
```
Gere a chave de segurança do Laravel:
```sh
php artisan key:generate
```
Com o docker rodando em sua maquina, execute o comando:
```sh
/vendor/bin/sail up
```
.Após configurar sua env e subir o projeto, rode as migrações para criar as tabelas no seu banco de dados:
```sh
./vendor/bin/sail artisan migrate
```
Agora é hora de popular seu banco de dados com as primeiras informações:
```sh
./vendor/bin/sail artisan db:seed
```
Por fim, gere a chave JWT para conseguir usar o mesmo;
```sh
./vendor/bin/sail artisan jwt:secret
```

A API tem a seu host em:
- 127.0.0.1

Para visualizar o swagger e saber como usar as endpoints, acesse:
- http://127.0.0.1/api/documentation

Caso não consiga acessar a documentação tente gerar ela novamente:
```sh
./vendor/bin/sail artisan l5-swagger:generate
```
