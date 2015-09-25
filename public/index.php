<?php
use \Firebase\JWT\JWT;

/*header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET,POST,OPTIONS,DELETE,PUT");
header("Access-Control-Allow-Headers:*");
*/
require '../vendor/autoload.php';
require '../rb.php';
require '../HttpBasicAuth.php';

$appkey = "abc";
$appid = "1";
$dbpath = 'sqlite:'.dirname(dirname(__FILE__)).'/tmp/dbapi.db';

R::setup( $dbpath );

$app = new \Slim\Slim();


$app->view(new \JsonApiView());

$app->add(new \JsonApiMiddleware());

//$app->add(new Slim\Extras\Middleware\HttpDigestAuth(array('username'=>'r00t','password' => 'passw0rd')));
$app->add(new Slim\Extras\Middleware\HttpBasicAuth('kambing','gunung'));

$app->add(new \CorsSlim\CorsSlim());


/*
$app->add(new \Slim\Middleware\JwtAuthentication([
	"path" => "/api",
	"secure" => false,
    "secret" => "supersecretkeyyoushouldnotcommittogithub",
    "callback" => function ($options) use ($app) {
        $app->jwt = $options["decoded"];
    }
]));
*/

/*$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    "path" => "/api",
    "secure" => false,
    "users" => [
        "root" => "t00r",
        "user" => "passw0rd"
    ],
    "error" => function ($arguments) use ($app) {
        $response["status"] = "error";
        $response["message"] = $arguments["message"];
        $app->response->write(json_encode($response, JSON_UNESCAPED_SLASHES));
    }
]));
*/ 


// Define routes
/*$app->post('/authenticate',function () use($app,$appid,$appkey){
	echo $app->request->post('appid');
	echo "  ".$app->request->post('appkey');
	exit;
  if($app->request->post('appid') == $appid && $app->request->post('appkey') == $appkey){
  	 $key = "supersecretkeyyoushouldnotcommittogithub";
        $token = array(
            "id" => "1",
            "exp" => time() + (60 * 60 * 24)
        );
        $jwt = JWT::encode($token, $key);
  	$app->render(200,array('data'=>$jwt));
  }else{
  	$app->render(404,array('data'=>''));
  }
});
*/

$app->get('/api/mood', function () use ($app) {
	$f = (int) $app->request->get('f');
    $t = (int) $app->request->get('t');
    $sql = "SELECT m FROM mood WHERE  t >= $f and t <= $t ";
     $result = R::getAll( $sql );
     $app->render(200,array('data' => $result));
});

$app->post('/api/mood', function () use ($app) {
    $m = (int) $app->request->post('m');
    $t = (int) $app->request->post('t');
    
    $mood = R::dispense( 'mood' );
    $mood->m = $m;
    $mood->t = $t;

    $ok = R::store( $mood );

    $app->render(200,array(
                'data' => $ok,
            ));
});


// Run app
$app->run();