<?php

require_once '../include/DbHandler.php';
require_once '../include/DbClotheHandler.php';
require_once '../include/DbPostHandler.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';


require_once '../models/User.php';
require_once '../models/UserLogin.php';
require_once '../models/Clothe.php';
require_once '../models/Clothes.php';
require_once '../models/Category.php';
require_once '../models/Brand.php';
require_once '../models/Material.php';

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
           try{
               global $user_id;
               $response = array();

               $content = trim(file_get_contents("php://input"));
               $allPostVars = json_decode($content, true);


               $clothe= new Clothe($allPostVars['cloth_id'],$allPostVars['cloth_name'], $allPostVars['cloth_color'],$allPostVars['cloth_reference'],$allPostVars['cloth_urlImage'],$allPostVars['cloth_category'],$allPostVars['cloth_brand'],$allPostVars['cloth_material'],$user_id);

               $db = new DbClotheHandler();
               $res = $db->createClothe($clothe);


                if ($res == true ){
                    $tmp = new Clothe();
                    $tmp->setClothId($res);

                    $app->response->setStatus(200);
                    $app->response()->headers->set('Content-Type', 'application/json');
                    echo json_encode($tmp);
                }else{
                    $app->response->setStatus(400);
                    $app->response()->headers->set('Content-Type', 'application/json');
                    echo json_encode (json_decode ("{}"));
                }

           }catch(PDOException $e) {
               $app->response()->setStatus(404);
               $app->response()->headers->set('Content-Type', 'application/json');
               echo json_encode (json_decode ("{}"));
           }


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
            $response["listClothe"] = array();

            foreach ($result as $value){
                $tmp = array();
                $tmp["cloth_id"] = $value->getClothId();
                $tmp["cloth_name"] = $value->getClothName();
                $tmp["cloth_color"] = $value->getClothColor();
                $tmp["cloth_reference"] = $value->getClothReference();
                $tmp["cloth_urlImage"] = $value->getClothUrlImage();

                $tmp["cloth_category"] = new Category();
                $tmp["cloth_category"]->id = $value->getClothCategory()->getId();
                $tmp["cloth_category"]->libelle = $value->getClothCategory()->getLibelle();

                $tmp["cloth_brand"] = new Brand();
                $tmp["cloth_brand"]->id = $value->getClothBrand()->getId();
                $tmp["cloth_brand"]->libelle = $value->getClothBrand()->getLibelle();

                $tmp["cloth_material"] = new Material();
                $tmp["cloth_material"]->id = $value->getClothMaterial()->getId();
                $tmp["cloth_material"]->libelle = $value->getClothMaterial()->getLibelle();

                array_push($response["listClothe"], $tmp);
            }

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($response);
            $db = null;
        }else {
            $app->response()->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }
    }catch(PDOException $e) {
        $app->response()->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }
});

/**
 * create clothe
 * url - /deleteClothe
 * method - POST
 * params - clothe
 */
$app->post('/deleteClothe', 'authenticate', function() use ($app) {
    $response = array();
    global $user_id;
    // reading post params
    $content = trim(file_get_contents("php://input"));
    $allPostVars = json_decode($content, true);


    $clothe= new Clothe($allPostVars['cloth_id'],$allPostVars['cloth_name'], $allPostVars['cloth_color'],$allPostVars['cloth_reference'],$allPostVars['cloth_urlImage'],$allPostVars['cloth_category'],$allPostVars['cloth_brand'],$allPostVars['cloth_material'],$user_id);

    $db = new DbClotheHandler();
    $res = $db->deleteClothe($clothe);

    if ($res === 0) {
        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    } else {
        $app->response->setStatus(400);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }

});

/**
 * update clothe
 * url - /updateClothe
 * method - POST
 * params - clothe
 */
$app->post('/updateClothe', 'authenticate', function () use ($app) {
    try{
        global $user_id;
        //$response = array();

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);


        $clothe= new Clothe($allPostVars['cloth_id'],$allPostVars['cloth_name'], $allPostVars['cloth_color'],$allPostVars['cloth_reference'],$allPostVars['cloth_urlImage'],$allPostVars['cloth_category'],$allPostVars['cloth_brand'],$allPostVars['cloth_material'],$user_id);

        $db = new DbClotheHandler();
        $res = $db->updateClothe($clothe);


        if ($res == true ){
            $tmp = new Clothe();
            $tmp->setClothId($res);

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($tmp);
        }else{
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }

    }catch(PDOException $e) {
        $app->response()->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }

});

/*----------------------------------------------------CLOTHES---------------------------------------------------------*/
/**
 * create clothe
 * url - /addClothe
 * method - POST
 * params - clothe
 */
$app->post('/addClothes', 'authenticate', function() use ($app) {
    try{
        $clotheArray = array();
        global $user_id;
        $response = array();

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);


        $clothes = new Clothes(0,$allPostVars['urlImage'], $allPostVars['listClothe'],$allPostVars['score'], $user_id);

        foreach ($allPostVars['listClothe'] as $valueC){

            var_dump($valueC['cloth_id']);
            $test = array_values($valueC);
            array_push($clotheArray,$test[0]);
        }

        $db = new DbClotheHandler();
        //$res = $db->createClothes($clothes, $clotheArray);

        if ($res == true ){
            $clothesResponse = new Clothes();
            $clothesResponse->setClothesId($res);

            //var_dump($clothesResponse);

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo '{"id":'. $clothesResponse->getClothesId() .'}';
        }else{
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }

    }catch(PDOException $e) {
        $app->response()->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        //echo json_encode (json_decode ("{}"));
        echo '{"error":'. $e->getMessage() .'}';
    }


});
/**
 * Liste tenues de l'utilisateur
 * method GET
 * url /getClothes
 */
$app->get('/getClothes', 'authenticate', function(){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();

        // fetching all user tasks
        $result = $db->viewAllClothes($user_id);

        if($result){

            $response["listClothes"] = array();
            foreach ($result as $value){
                $tmp1 = array();
                $tmp1["id"] = $value->getClothesId();
                $tmp1["urlImage"] = $value->getUrlImage();
                $tmp1["listClothe"] = array();
                foreach ($value->getListClothe() as $value2){
                    $tmp = array();
                    $tmp["cloth_id"] = $value2->getClothId();
                    $tmp["cloth_name"] = $value2->getClothName();
                    $tmp["cloth_color"] = $value2->getClothColor();
                    $tmp["cloth_reference"] = $value2->getClothReference();
                    $tmp["cloth_urlImage"] = $value2->getClothUrlImage();

                    $tmp["cloth_category"] = new Category();
                    $tmp["cloth_category"]->id = $value2->getClothCategory()->getId();
                    $tmp["cloth_category"]->libelle = $value2->getClothCategory()->getLibelle();

                    $tmp["cloth_brand"] = new Brand();
                    $tmp["cloth_brand"]->id = $value2->getClothBrand()->getId();
                    $tmp["cloth_brand"]->libelle = $value2->getClothBrand()->getLibelle();

                    $tmp["cloth_material"] = new Material();
                    $tmp["cloth_material"]->id = $value2->getClothMaterial()->getId();
                    $tmp["cloth_material"]->libelle = $value2->getClothMaterial()->getLibelle();

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
            $app->response()->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode(json_decode ("{}"));
        }
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }
});

/**
 * Supprime une tenue de l'utilisateur
 * method POST
 * url /deleteClothes
 * param - clothes
 */
$app->post('/deleteClothes', 'authenticate', function() use ($app) {
    global $user_id;
    // reading post params
    $content = trim(file_get_contents("php://input"));
    $allPostVars = json_decode($content, true);

    $clothes= new Clothes($allPostVars['id'],$allPostVars['urlImage'], $allPostVars['listClothe'],$allPostVars['score'], $user_id);

    $db = new DbClotheHandler();
    $res = $db->deleteClothes($clothes);

    if ($res === 0) {
        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    } else {
        $app->response->setStatus(400);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }

});

/**
 * update clothes
 * url - /updateClothes
 * method - POST
 * params - clothes
 */
$app->post('/updateClothes', 'authenticate', function () use ($app) {
    try{
        global $user_id;
        $clotheArray = array();

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);


        $clothes = new Clothes($allPostVars['id'], $allPostVars['urlImage'],$allPostVars['listClothe'], $allPostVars['score'], $user_id);

        foreach ($allPostVars['listClothe'] as $valueC){
            $test = array_values($valueC);
            array_push($clotheArray,$test[0]);
        }


        $db = new DbClotheHandler();
        $res = $db->updateClothes($clothes, $clotheArray);

        if ($res == true ){
            $clothesResponse = new Clothes();
            $clothesResponse->setClothesId($res);


            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo '{"id":'. $clothesResponse->getClothesId() .'}';
        }else{
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }

    }catch(PDOException $e) {
        $app->response()->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }

});

/*----------------------------------------------CLOTHES-PROPERTIES--------------------------------------------------------*/

$app->get('/getClotheProperties','authenticate', function (){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();
        $db2 = new DbClotheHandler();
        $db3 = new DbClotheHandler();
        // fetching all user tasks
        $resultBrand = $db->viewAllBrand();
        $resultCategory = $db2->viewAllCategory();
        $resultMaterial = $db3->viewAllMaterial();

        if($resultMaterial){
            $response["listBrands"] = array();
            foreach ($resultBrand as $value){
                $tmpB = array();
                $tmpB["id"] = $value->getId();
                $tmpB["libelle"] = $value->getLibelle();
                array_push($response["listBrands"], $tmpB);
            }

            $response["listCategories"] = array();
            foreach ($resultCategory as $value){
                $tmpC = array();
                $tmpC["id"] = $value->getId();
                $tmpC["libelle"] = $value->getLibelle();
                array_push($response["listCategories"], $tmpC);
            }

            $response["listMaterials"] = array();
            foreach ($resultMaterial as $value){
                $tmpM = array();
                $tmpM["id"] = $value->getId();
                $tmpM["libelle"] = $value->getLibelle();
                array_push($response["listMaterials"], $tmpM);
            }

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($response);
            $db = null;
        }else {
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }
    } catch(PDOException $e) {
        $app->response->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }
});

$app->get('/getBrand','authenticate', function (){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();

        // fetching all user tasks
        $result = $db->viewAllBrand();

        if($result){
            $response["listBrands"] = array();
            foreach ($result as $value){
                $tmp = array();
                $tmp["id"] = $value->getId();
                $tmp["libelle"] = $value->getLibelle();

                array_push($response["listBrands"], $tmp);
            }

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($response);
            $db = null;
        }else {
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }
    }catch(PDOException $e) {
        $app->response->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }
});

$app->get('/getCategory','authenticate', function (){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();

        // fetching all user tasks
        $result = $db->viewAllCategory();

        if($result){
            $response["listCategories"] = array();
            foreach ($result as $value){
                $tmp = array();
                $tmp["id"] = $value->getId();
                $tmp["libelle"] = $value->getLibelle();

                array_push($response["listCategories"], $tmp);
            }

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($response);
            $db = null;
        }else {
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }
    }catch(PDOException $e) {
        $app->response->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }
});

$app->get('/getMaterial','authenticate', function (){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();

        // fetching all user tasks
        $result = $db->viewAllMaterial();

        if($result){
            $response["listMaterials"] = array();
            foreach ($result as $value){
                $tmp = array();
                $tmp["id"] = $value->getId();
                $tmp["libelle"] = $value->getLibelle();

                array_push($response["listMaterials"], $tmp);
            }

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($response);
            $db = null;
        }else {
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }
    }catch(PDOException $e) {
        $app->response->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }
});


/*----------------------------------------------POST--------------------------------------------------------*/
/**
 * create post
 * url - /addPost
 * method - POST
 * params - post
 */
$app->post('/addPost', 'authenticate', function() use ($app) {
    try{
        global $user_id;
        $response = array();

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);


        $post= new Post($allPostVars['title'],$allPostVars['desc'], $allPostVars['clothes_id'], $user_id);

        $db = new DbPostHandler();
        $res = $db->createPost($post);

        if ($res == true ){
            $tmp = new Post();
            $tmp->setPostId($res);

            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode($tmp);
        }else{
            $app->response->setStatus(400);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode (json_decode ("{}"));
        }

    }catch(PDOException $e) {
        $app->response()->setStatus(404);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode (json_decode ("{}"));
    }


});


$app->run();
