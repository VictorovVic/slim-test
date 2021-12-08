<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

$app = AppFactory::create();

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$app->get('/', function ($request, $response) {
    $response->getBody()->write('Welcome to Slim!');
    return $response;
});

$app->get('/users', function ($request, $response) use ($users) {
    $term = $request->getQueryParam('term');
    
    if ($term !== null) {
    $res = array_filter($users, fn($user) =>  str_contains($user, $term));
    $params = ['users' => $res];
    }   else {
            $params = ['users' => $users, 'term' => $term];
    }
    return $this->get('renderer')->render($response, 'users/index.phtml', $params);

});


$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
});
/*
$app->get('/users/{id}', function ($request, $response, $args) {
    $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];
    // Указанный путь считается относительно базовой директории для шаблонов, заданной на этапе конфигурации
    // $this доступен внутри анонимной функции благодаря https://php.net/manual/ru/closure.bindto.php
    // $this в Slim это контейнер зависимостей
    return $this->get('renderer')->render($response, 'users/show.phtml', $params);
});*/

$app->get('/users/new', function ($request, $response) {
    $params = [
        'user' => ['name' => '', 'email' => '', 'id' => '']];
    return $this->get('renderer')->render($response, "users/new.phtml", $params);
});

$app->post('/users', function ($request, $response) {
    $user = $request->getParsedBodyParam('user');
   
    $data = json_encode($user);
    file_put_contents('./data.json', $data);
    $params = [
        'user' => $user,
    ];

    return $this->get('renderer')->render($response, "users/new.phtml", $params);
});

$app->run();