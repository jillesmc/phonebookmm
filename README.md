# PhoneBook

Projeto full stack de agenda telefônica

## Como começar

Faça uma cópia ou clone o repositório e siga as instruções abaixo para executar o projeto.

Você poderá executar esta aplicação com o 

 [Servidor PHP embutido](#execução-servidor-php-embutido)  ou  [Container docker](#execução-container-docker) 


### Execução servidor PHP embutido

É necessário ter um servidor MySql instalado local ou remoto.

Você deverá fazer a importação do banco de dados utilizando o arquivo ***.database/phonebook.sql***, ele criará a estrutura base de tabelas para o sistema.

Conforme necessidade, altere as informações de conexão no arquivo ***database.php*** localizado em ***application/app/database.php***

Você pode utilizar o comando abaixo para executar o servidor da aplicação:
```
php -S 0.0.0.0:80 -t application/public -c $(CURDIR)/php.ini
```
Ou, caso tenha a ferramenta *make* instalada poderá utilizar apenas:
```
make serve
```
Instale as dependências utilizando o [composer](https://getcomposer.org/download/)
```
cd application && composer install
```

A aplicação estará disponível em [http://localhost](http://localhost)

O painel administrativo estará disponível em [http://localhost/admin](http://localhost/admin)

#### Dados de acesso ao administrativo
usuário: admin@madeiramadeira.com.br
senha: contratado

### Execução container docker

É necessário que você tenha o *docker* e o *docker-compose*  instalados. 

O arquivo de configuração do banco de dados já está com as informações corretas para execução em *container docker.*

Entretanto será necessário inserir uma entrada no arquivo de hosts para que seja possível a execução com HTTPS.

```
127.0.0.1		phonebook.com.br
```
Também será necessário importar a autoridade certificadora do certificado auto assinado para o seu navegador. No Google Chrome você deve fazer a importação pelo menu *configurações -> gerenciar certificados -> autoridades -> importar*. Faça a importação do arquivo que está em ***.docker/certs/local/rootCA.pem***

Execute pela primeira vez com o comando:
```
docker-compose up --build -d
```
Execute o composer para instalar as dependências de autoload e JWT
```
docker run --rm -ti --volume $PWD/application:/app composer install
```
Se você utilizar o docker composer acima, deverá mudar as permissões dos arquivos criados para seu usuário:
```
sudo chown -R $USER application/
```

*Você também pode usar o composer que tem instalado localmente caso não queira utilizar imagem docker.*

Em seguida faça a importação do banco de dados
```
mysql -h 127.0.0.1 -u root phonebook < .database/phonebook.sql 
```

Para as próximas execuções você poderá utilizar apenas
```
docker-compose up -d
```
A aplicação estará disponível em [http://phonebook.com.br](http://phonebook.com.br)

O painel administrativo estará disponível em [http://phonebook.com.br/admin](http://phonebook.com.br/admin)
#### Dados de acesso ao administrativo
usuário: admin@madeiramadeira.com.br
senha: contratado

### Possíveis problemas

Caso tenha um servidor apache ou mysql em execução, provavelmente eles já estarão fazendo uso das portas 80, 443 e 3306. 

Sendo assim, para execução do servidor PHP embutido, recomendo que pare a execução do servidor apache que já está fazendo uso da porta 80 antes de iniciar a execução deste projeto.

Para execução do servidor docker, recomendo que pare a execução do servidor apache, bem como do servidor mysql para liberação da porta 80, 443 e 3306.


## Feito com

* [Twitter Bootstrap 4](https://getbootstrap.com/docs/4.1/getting-started/introduction/) - Usado na criação do layout

* [Open Ionic Icons](https://useiconic.com/open) - Conjunto de ícones
 
* [jQuery](https://api.jquery.com/) - Usado na manipulação em Javascript

* [JWT](https://jwt.io/) - Usado para autenticação com base em tokens

* [Firebase PHP JWT](https://github.com/firebase/php-jwt) - Biblioteca que implementa o padrão JWT


## Autor

* **Jilles Moraes Cardoso** - *Full Stack Developer* - [jillesmc](https://github.com/jillesmc)