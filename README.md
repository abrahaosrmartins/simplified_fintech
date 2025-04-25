# Simplified Fintech

Plataforma de pagamentos simplificada. Nela é possível depositar e realizar transferências de dinheiro entre usuários.

Veja a [Dcumentação de regras de negócio, também com critérios de aceite e cenários de teste.](https://excalidraw.com/#json=dFLqLRH6eEITSGQ5CmzGi,aOqY3eu3_2BWsKd_hI7edw)

### Principais ferramentas:

- Laravel
- Laravel Telescope
- PHPUnit
- Nginx
- Mysql
- Sqlite (Banco de testes)
- Redis
- Docker

### Inicializando o projeto

Após clonar o projeto, crie um `.env` e defina o valor de `XDEBUG_CLIENT_HOST=<seu ip local>`:

```
cp .env.example .env
```

O projeto já vem com xdebug instalado via Dockerfile. Para criar o arquivo de configuração já com as opções otimizadas. Basta executar o script:

```
docker/php/init_xdebug.sh
```

Caso tenha algum problema de permissão, basta rodar o comando abaixo e tentar executar o .sh novamente:

```
sudo chmod 777 -R docker/php/init_xdebug.sh
```
Para a utilização de *breakpoints* no cli, abra o arquivo criado e modifique:
```
xdebug.start_with_request=yes
```

Agora basta garantir que a sua IDE esteja configurada para o *debugger*. Para isso, vá até Executar -> Adicionar Configuração -> Digite PHP e confirme.

Garanta que o arquivo `launch.json` tenha o seguinte conteúdo:

```json
{
    "version": "0.2.0",
    "configurations": [

        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9005,
            "hostname": "0.0.0.0",
            "pathMappings": {
                "/var/www": "${workspaceFolder}"
            },
        },
        {
            "name": "Launch currently open script",
            "type": "php",
            "request": "launch",
            "program": "${file}",
            "cwd": "${fileDirname}",
            "port": 0,
            "runtimeArgs": [
                "-dxdebug.start_with_request=yes"
            ],
            "env": {
                "XDEBUG_MODE": "debug,develop",
                "XDEBUG_CONFIG": "client_port=${port}"
            }
        },
        {
            "name": "Launch Built-in web server",
            "type": "php",
            "request": "launch",
            "runtimeArgs": [
                "-dxdebug.mode=debug",
                "-dxdebug.start_with_request=yes",
                "-S",
                "localhost:0"
            ],
            "program": "",
            "cwd": "${workspaceRoot}",
            "port": 9005,
            "serverReadyAction": {
                "pattern": "Development Server \\(http://localhost:([0-9]+)\\) started",
                "uriFormat": "http://localhost:%s",
                "action": "openExternally"
            }
        }
    ]
}
```

Pronto! Agora é só *buildar* o projeto usando docker-compose

```
docker compose up -d --build
```

Seu projeto estará rodando no link `localhost:8080`

### Após as configurações iniciais

Para acessar o *container* da aplicação:

```
docker exec -it simplified_fintech_app bash
```

Dentro do *container* da aplicação você pode usar o *composer* e os comandos artisan do Framework a vontade.

#### Para visualizar acessar o Laravel Telescope
*Logs, requisições, exceptions, cache e muito mais com Laravel*

```
localhost:8080/telescope
```

#### Para executar a fila de eventos pós criação da transação:
```
php artisan queue:work
```
