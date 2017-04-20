<?php

require_once '../include/DbHandler.php';
require_once '../include/DbClotheHandler.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';



require_once '../models/User.php';
require_once '../models/UserLogin.php';

//require '../vendor/autoload.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

/**
 * Authentification avec l'API_KEY
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers

    $theapp = new \Slim\Slim();
    $headers = $theapp->request->headers;

   // $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['x-access-token'])) {
        $db = new DbHandler();

        // get the api key
        $api_key = $headers['x-access-token'];

        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response['status'] = "error";
            $response['message'] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user = $db->getUserId($api_key);
            if ($user != NULL){
                $user_id = $user;
            }

        }
    } else {
        // api key is missing in header
        $response['status'] = "error";
        $response['message'] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}
/*-----------------------------------------------------USER--------------------------------------------------------------*/
/**
 * User Registration
 * url - /register
 * method - POST
 * params - name, email, password
 */
$app->post('/register', function() use ($app) {
            $response = array();

            // reading post params
            //$allPostVars = $app->request->post();
            $content = trim(file_get_contents("php://input"));
          //Attempt to decode the incoming RAW post data from JSON.
            $allPostVars = json_decode($content, true);

            //$allPostVars = json_decode($allPostVars,1);

            $user = new User($allPostVars['user_pseudo'], $allPostVars['user_password'], $allPostVars['user_fisrtName'], $allPostVars['user_lastName'], $allPostVars['user_mail'], $allPostVars['user_country']);

            // validating email address
            validateEmail($user->getUser_mail());

            $db = new DbHandler();
            $res = $db->createUser($user);

            if (is_string($res)) {
                $response['status'] = "success";
                $response["api_key"] = $res;
                echoRespnse(200, $response);
            } else if ($res === 2) {
                $response['status'] = "error";
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(400, $response);
            } else if ($res === 3) {
                $response['status'] = "error";
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(400, $response);
            }
        });

/**
 * User Login
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/login', function() use ($app) {
            // check for required params
            //verifyRequiredParams(array('email', 'password'));
            $response = array();

            $content = trim(file_get_contents("php://input"));
            $allPostVars = json_decode($content, true);

            $userLogin = new UserLogin($allPostVars['user_mail'], $allPostVars['user_password']);

            $db = new DbHandler();
            // check for correct email and password
            if ($db->checkLogin($userLogin)) {
                // get the user by email
                $res = $db->getUserByEmail($userLogin);
                if ($res != NULL) {
                    $response['api_key'] = $res;
                    echoRespnse(200, $response);
                } else {
                    // unknown error occurred

                    echoRespnse(400);
                }
            } else {
                // user credentials are wrong
                echoRespnse(400);
            }
        });

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
 function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}


/*----------------------------------------------------CLOTHE---------------------------------------------------------*/
/**
 * create clothe
 * url - /addClothe
 * method - POST
 * params - clothe
 */
$app->post('/addClothe', 'authenticate', function() use ($app) {
            $response = array();

            // reading post params
            $content = trim(file_get_contents("php://input"));
            //Attempt to decode the incoming RAW post data from JSON.
            $allPostVars = json_decode($content, true);


            $clothe= new Clothe($allPostVars['cloth_name'], $allPostVars['cloth_color'],$allPostVars['cloth_reference'],$allPostVars['cloth_urlImage'],$allPostVars['cloth_category'],$allPostVars['cloth_brand'],$allPostVars['cloth_material']);

            $db = new DbClotheHandler();
            $res = $db->createClothe($clothe);

            if ($res == CLOTHE_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "Clothe created successfully";

            } else {
                $response["error"] = true;
                $response["message"] = "Failed to create clothe. Please try again";
            }
            echoRespnse(201, $response);
        });

/**
 * Liste des habits de l'utilisateur
 * method GET
 * url /getClothe
 */
$app->get('/getClothe', 'authenticate', function(){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();

        // fetching all user tasks
        $result = $db->viewAllClothe($user_id);

        if($result){
            //$response['status'] = "success";
            $response["listClothe"] = array();
            foreach ($result as $value){
                $tmp = array();
                $tmp["cloth_name"] = $value->getClothName();
                $tmp["cloth_color"] = $value->getClothColor();
                $tmp["cloth_reference"] = $value->getClothReference();
                $tmp["cloth_urlImage"] = $value->getClothUrlImage();
                $tmp["cloth_category"] = $value->getClothCategory();
                $tmp["cloth_brand"] = $value->getClothBrand();
                $tmp["cloth_material"] = $value->getClothMaterial();
                array_push($response["listClothe"], $tmp);
            }

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($response);
            $db = null;
        }else {
            $app->response()->setStatus(401);
            throw new PDOException('No records found');
        }
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo '{"status":"error", "message":"'. $e->getMessage() .'"}';
    }
});


/*----------------------------------------------------CLOTHES---------------------------------------------------------*/
$app->get('/getClothes', 'authenticate', function(){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();

        // fetching all user tasks
        $result = $db->viewAllClothes($user_id);
        var_dump($result);


        if($result){

            $response["listClothes"] = array();
            foreach ($result as $value){
                $tmp1 = array();
                $tmp1["urlImage"] = $value->getUrlImage();
                $tmp1["listClothe"] = array();
                foreach ($value as $value2){
                    $tmp = array();
                    $tmp["cloth_name"] = $value2->getClothName();
                    $tmp["cloth_color"] = $value2->getClothColor();
                    $tmp["cloth_reference"] = $value2->getClothReference();
                    $tmp["cloth_urlImage"] = $value2->getClothUrlImage();
                    $tmp["cloth_category"] = $value2->getClothCategory();
                    $tmp["cloth_brand"] = $value2->getClothBrand();
                    $tmp["cloth_material"] = $value2->getClothMaterial();
                    array_push($tmp1["listClothe"], $tmp);
                }
                $tmp1["score"] = $value->getScore();
                array_push($response["listClothes"], $tmp1);
            }

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($response);
            $db = null;
        }else {
            $app->response()->setStatus(401);
            throw new PDOException('No records found');
        }
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo '{"status":"error", "message":"'. $e->getMessage() .'"}';
    }
});




$app->run();
