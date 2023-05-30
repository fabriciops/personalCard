# APIs REST com PHP 7 e Slim Framework

* Autor: Fabrício Pinheiro dos Santos
* E-mail: fabriciopps19@gmail.com
* Github: [https://github.com/fabriciops](https://github.com/fabriciops)
* Site: [https://psdesigneweb.com.br/](https://psdesigneweb.com.br/)


## CONFIGURAÇÃO

Copie o arquivo `env.example.php` para `env.php` e preencha com as informações necessárias.
Rode no terminal `composer install` ou `php composer.phar install` dependendo de como você usa o composer.
Use os códigos no diretório `sql/` para criar o seu banco de dados trabalhando MySQL.

Observação: projeto/src/slimConfiguration.php ('DISPLAY_ERRORS_DETAILS'). Essa opção determina se os detalhes dos erros serão exibidos ou não.


# Documentação dos principais arquivos da aplicação

## projeto/src:

O arquivo **slimConfiguration.php**  define a configuração do framework Slim e cria um contêiner de injeção de dependência usando o padrão de projeto IoC (Inversion of Control). Aqui está uma explicação do seu propósito e do tipo de prática utilizada:

O arquivo define uma função chamada slimConfiguration que retorna um objeto do tipo Psr\Container\ContainerInterface. Essa função é responsável por configurar o Slim e criar o contêiner de injeção de dependência.

Dentro da função slimConfiguration, é definido um array de configuração com a opção displayErrorDetails que é recuperada do ambiente através da função geten **('DISPLAY_ERRORS_DETAILS')**. Essa opção determina se os detalhes dos erros serão exibidos ou não.

Em seguida, é criado um objeto Slim\Container passando o array de configuração como parâmetro. Esse objeto representa o contêiner de injeção de dependência do Slim.

O contêiner é configurado com diferentes definições de classes e suas dependências. Por exemplo:

A classe **PostagemController** é associada a uma função anônima que recebe o contêiner como parâmetro. Essa função cria uma instância de **PostagemService** obtida do contêiner e a passa para o construtor de **PostagemController**. Dessa forma, o contêiner será capaz de resolver as dependências quando um objeto PostagemController for solicitado.

A classe **PostagemService** é associada a uma função anônima que recebe o contêiner como parâmetro. Essa função cria uma instância de **PostagemDAO** obtida do contêiner e a passa para o construtor de **PostagemService**. Assim, o contêiner será capaz de resolver as dependências quando um objeto **PostagemService** for solicitado.

A classe **PostagemDAO** é associada a uma função anônima simples que retorna uma nova instância de **PostagemDAO** diretamente.
Por fim, o contêiner é retornado pela função slimConfiguration.

Em resumo, esse arquivo serve para configurar o framework Slim e criar um contêiner de injeção de dependência. A prática utilizada é a inversão de controle, onde as dependências das classes são resolvidas pelo contêiner, permitindo a fácil troca de implementações e facilitando o teste e a manutenção do código.

## projeto/routes:

O uso do padrão IoC nesse arquivo de configuração de rota proporciona flexibilidade, separação de responsabilidades, testabilidade, reutilização de código e organização do código, tornando o desenvolvimento e a manutenção do aplicativo mais eficientes.
***Os Endpoints estão todos dentro da pasta collection. Basta upar em seu postman ou ferramenta de requisição para utilizá-lo***
## app/controllers

**AuthController**

O método login recebe uma requisição HTTP contendo as informações de email e senha do usuário. Ele verifica se o usuário existe no banco de dados e se a senha fornecida corresponde à senha armazenada no banco de dados. Se as condições forem atendidas, um token de autenticação e um token de atualização são gerados e salvos no banco de dados. O token de autenticação é retornado na resposta.

O método logout remove o token de autenticação do banco de dados, efetuando assim o logout do usuário.

O método activate é responsável por ativar o cadastro de um usuário. Ele recebe um código de ativação como parâmetro na URL e verifica se o código é válido. Se for válido, o status da conta do usuário é alterado para "active".

O método refreshToken é utilizado para atualizar o token de autenticação. Ele recebe um token de atualização e uma data de expiração como parâmetros. O token de atualização é decodificado, verificado e usado para obter as informações do usuário. Em seguida, um novo token de autenticação e um novo token de atualização são gerados, salvos no banco de dados e retornados na resposta.

***Estrutura do controller e os services***

Controlador (PostagemController):
O controlador é responsável por receber as requisições HTTP, processá-las e retornar as respostas adequadas. Ele lida com a lógica específica das rotas da API, direcionando as solicitações para os métodos apropriados. No código fornecido, o PostagemController possui os seguintes métodos:
getPostagemById: Retorna uma única postagem com base no ID fornecido.
helloWorld: Retorna uma resposta de "Hello, World!".
getPostagens: Retorna uma lista paginada de postagens.
insertPostagem: Insere uma nova postagem.
updatePostagem: Atualiza uma postagem existente.
deletePostagem: Exclui uma postagem existente.
Esses métodos lidam com a lógica de negócios específica de cada operação relacionada a postagens. Eles interagem com o serviço PostagemService para realizar as operações necessárias.

Serviço (PostagemService):
O serviço é responsável por abstrair a lógica de negócios da aplicação. Ele contém métodos que implementam as regras de negócios específicas para as postagens. No código fornecido, o PostagemService possui os seguintes métodos:
getPaginatedPostagens: Retorna uma lista paginada de postagens.
createPostagem: Cria uma nova postagem.
getPostagemById: Retorna uma única postagem com base no ID fornecido.
updatePostagem: Atualiza uma postagem existente.
deletePostagem: Exclui uma postagem existente.
O serviço se comunica com a camada de acesso a dados (DAO) PostagemDAO para interagir com o banco de dados e realizar as operações de leitura/gravação.

A importância dessa estrutura é separar a lógica de negócios da lógica de apresentação (controlador) e do acesso a dados (DAO). Isso melhora a modularidade, legibilidade, testabilidade e reutilização do código. Além disso, o uso de um serviço permite centralizar a lógica de negócios em um único local, facilitando a manutenção e a escalabilidade da aplicação.

No código fornecido, o controlador (PostagemController) recebe as requisições HTTP, chama os métodos apropriados do serviço (PostagemService) e retorna as respostas adequadas. O serviço, por sua vez, interage com o DAO (PostagemDAO) para realizar as operações de leitura/gravação no banco de dados.



**PostagemController**

A classe possui uma dependência PostagemService que é injetada no construtor através da injeção de dependência por meio de um contêiner de inversão de controle (IoC).
A classe também utiliza a biblioteca Monolog para fazer o registro de logs.
No construtor, o objeto PostagemService é injetado e atribuído à propriedade $postagemService.
Um objeto Logger é criado com o nome "debug" e um manipulador StreamHandler é adicionado para registrar logs em um arquivo de log ***/logs/debug.log***

O método getPostagemById é responsável por recuperar uma postagem com base em seu ID.
Ele recebe três parâmetros: um objeto Request, um objeto Response e um array de argumentos.
O ID da postagem é extraído dos argumentos.

O serviço PostagemService é usado para obter a postagem pelo ID.
Os dados da postagem são formatados em um array associativo chamado $data, que contém informações como o ID, título, texto, data de postagem, ID do usuário e informações do usuário associado.

A resposta é configurada com o corpo contendo os dados da postagem serializados em formato JSON.
O cabeçalho de resposta é definido como "application/json".
O status de resposta é definido como 200 (OK).

## PHPunit

Em meu ambiente estou utilizando para filtrar os teste a extensão ***Testing*** que filtra todos as classes contidas em meu diretório de testes. Uma de suas 
vantagens é que ele já vem com um debug interativo que achei interessante em usar no slim devido a pouca experiência com o framework, ajudou bastante.

Em seu terminal Navegue até o diretório de tests que fica na raiz do projeto.
projeto/tests/PostagemControllerTest.php

Execute o comando abaixo para executar os testes:
***phpunit PostagemControllerTest.php***

Isso iniciará a execução dos testes definidos na classe PostagemControllerTest. O PHPUnit executará cada método de teste (métodos que começam com "test") e fornecerá informações sobre o resultado dos testes.

Certifique-se de ter configurado corretamente o ambiente de teste, incluindo todas as dependências necessárias e os arquivos de autoload, para que os testes possam ser executados sem erros.



