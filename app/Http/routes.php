<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group([
	'prefix'	=> 'services/clients',
	'namespace' => 'App\Http\Controllers'
], function() use ($app) {
	$app->get('', 'ClientsController@index');
	$app->get('{id}', 'ClientsController@show');
	$app->post('', 'ClientsController@store');
	$app->put('{id}', 'ClientsController@update');
	$app->delete('{id}', 'ClientsController@destroy');
});

$app->group([
	'prefix'	=> 'services/clients/{client}/addresses',
	'namespace' => 'App\Http\Controllers'
], function() use ($app) {
	$app->get('', 'AddressesController@index');
	$app->get('{id}', 'AddressesController@show');
	$app->post('', 'AddressesController@store');
	$app->put('{id}', 'AddressesController@update');
	$app->delete('{id}', 'AddressesController@destroy');
});

// Route webservice TCU
$app->get('tcu', function () use ($app) {
    // Gerando servidor cliente SOAP
    $client = new Zend\Soap\Client('http://contas.tcu.gov.br/debito/CalculoDebito?wsdl');

    echo "Informações do Servidor:";
    print_r($client->getOptions());

    echo "Funções:";
    print_r($client->getFunctions());

    echo "Tipos:";
    print_r($client->getTypes());

    echo "Resultado:";
    print_r($client->obterSaldoAtualizado([
    	'parcelas' => [
			'parcela' => [
				'data' => '1995-01-01',
				'valor' => 35,
				'tipo' => 'D'
			]
		],
		'aplicaJuros' => true,
		'dataAtualizacao' => '2016-12-31'
    ]));
});

$uri = 'http://localhost:8000';
$app->get('son-soap.wsdl', function () use ($uri) {
    $autoDiscover = new \Zend\Soap\AutoDiscover();
    $autoDiscover->setUri("$uri/server");
    $autoDiscover->setServiceName('SONSOAP');
    $autoDiscover->addFunction('soma');
    $autoDiscover->handle();
});

$app->post('server', function () use ($uri) {
    $server = new \Zend\Soap\Server("$uri/son-soap.wsdl", [
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
    $server->setUri("$uri/server");
    return $server
        ->setReturnResponse(true)
        ->addFunction('soma')
        ->handle();
});

$app->get('soap-test', function () use ($uri) {
    $client = new \Zend\Soap\Client("$uri/son-soap.wsdl", [
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
    print_r($client->soma(100, 200));
});

/**
* Soma os números e retorna um resultado
* @param int $num1
* @param int $num2
* @return int
*/
function soma($num1,$num2)
{	
	return $num1 + $num2;
}