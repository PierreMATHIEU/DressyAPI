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
require_once '../models/Post.php';
require_once '../models/Color.php';

include '../../dressyNetwork/Models/Prepare.php';
include '../../dressyNetwork/Models/Network.php';

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
 * Validation de l'adresse email
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

function imageBase64(){
    $target_dir = "/var/www/html/uploads/";
    $target_file_name = $target_dir.basename($_FILES["file"]["name"]);
    $response = array();


    if (isset($_FILES["file"]))
    {
        //Déplace un fichier téléchargé
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file_name))
        {
            $success = true;
            $message = "Successfully Uploaded";
        }
        else
        {
            $success = false;
            $message = "Error while uploading";
        }
    }
    else
    {
        $success = false;
        $message = "Required Field Missing";
    }

    $response["success"] = $success;
    $response["message"] = $message;
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

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);

        $cloth_id = (!empty($allPostVars['cloth_id'])) ? $allPostVars['cloth_id'] : 0;
        $cloth_name = (!empty($allPostVars['cloth_name'])) ? $allPostVars['cloth_name'] : "";
        $cloth_reference = (!empty($allPostVars['cloth_reference'])) ? $allPostVars['cloth_reference'] : "";
        $cloth_urlImage = (!empty($allPostVars['cloth_urlImage'])) ? $allPostVars['cloth_urlImage'] : "";
        $cloth_category = (!empty($allPostVars['cloth_category'])) ? $allPostVars['cloth_category'] : array();
        $cloth_brand = (!empty($allPostVars['cloth_brand'])) ? $allPostVars['cloth_brand'] : array();
        $cloth_material = (!empty($allPostVars['cloth_material'])) ? $allPostVars['cloth_material'] : array();
        $cloth_color = (!empty($allPostVars['cloth_color'])) ? $allPostVars['cloth_color'] : array();

        $clothe= new Clothe($cloth_id,$cloth_name,$cloth_reference,$cloth_urlImage, $cloth_category ,$cloth_brand,$cloth_material, $cloth_color,$user_id);

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
        $result = $db->viewAllClothe($user_id);
        if($result){
            $response["listClothe"] = array();

            foreach ($result as $value){
                $tmp = array();
                $tmp["cloth_id"] = $value->getClothId();
                $tmp["cloth_name"] = $value->getClothName();

                //$tmp["cloth_color"] = $value->getClothColor();
                $tmp["cloth_color"] = new Color();
                $tmp["cloth_color"]->setId($value->getClothColor()->getId());
                $tmp["cloth_color"]->setLibelle($value->getClothColor()->getLibelle());
                $tmp["cloth_color"]->setIdFann($value->getClothColor()->getIdFann());

                $tmp["cloth_reference"] = $value->getClothReference();
                $tmp["cloth_urlImage"] = $value->getClothUrlImage();

                $tmp["cloth_category"] = new Category();
                $tmp["cloth_category"]->id = $value->getClothCategory()->getId();
                $tmp["cloth_category"]->libelle = $value->getClothCategory()->getLibelle();
                $tmp["cloth_category"]->id_fann = $value->getClothCategory()->getIdFann();

                $tmp["cloth_brand"] = new Brand();
                $tmp["cloth_brand"]->id = $value->getClothBrand()->getId();
                $tmp["cloth_brand"]->libelle = $value->getClothBrand()->getLibelle();
                $tmp["cloth_brand"]->id_fann = $value->getClothBrand()->getIdFann();

                $tmp["cloth_material"] = new Material();
                $tmp["cloth_material"]->id = $value->getClothMaterial()->getId();
                $tmp["cloth_material"]->libelle = $value->getClothMaterial()->getLibelle();
                $tmp["cloth_material"]->id_fann = $value->getClothMaterial()->getIdFann();

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

    global $user_id;
    $content = trim(file_get_contents("php://input"));
    $allPostVars = json_decode($content, true);

    $cloth_id = (!empty($allPostVars['cloth_id'])) ? $allPostVars['cloth_id'] : 0;
    $cloth_name = (!empty($allPostVars['cloth_name'])) ? $allPostVars['cloth_name'] : "";
    $cloth_reference = (!empty($allPostVars['cloth_reference'])) ? $allPostVars['cloth_reference'] : "";
    $cloth_urlImage = (!empty($allPostVars['cloth_urlImage'])) ? $allPostVars['cloth_urlImage'] : "";
    $cloth_category = (!empty($allPostVars['cloth_category'])) ? $allPostVars['cloth_category'] : array();
    $cloth_brand = (!empty($allPostVars['cloth_brand'])) ? $allPostVars['cloth_brand'] : array();
    $cloth_material = (!empty($allPostVars['cloth_material'])) ? $allPostVars['cloth_material'] : array();
    $cloth_color = (!empty($allPostVars['cloth_color'])) ? $allPostVars['cloth_color'] : array();

    $clothe= new Clothe($cloth_id,$cloth_name,$cloth_reference,$cloth_urlImage, $cloth_category ,$cloth_brand,$cloth_material, $cloth_color,$user_id);

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

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);

        $cloth_id = (!empty($allPostVars['cloth_id'])) ? $allPostVars['cloth_id'] : 0;
        $cloth_name = (!empty($allPostVars['cloth_name'])) ? $allPostVars['cloth_name'] : "";
        $cloth_reference = (!empty($allPostVars['cloth_reference'])) ? $allPostVars['cloth_reference'] : "";
        $cloth_urlImage = (!empty($allPostVars['cloth_urlImage'])) ? $allPostVars['cloth_urlImage'] : "";
        $cloth_category = (!empty($allPostVars['cloth_category'])) ? $allPostVars['cloth_category'] : array();
        $cloth_brand = (!empty($allPostVars['cloth_brand'])) ? $allPostVars['cloth_brand'] : array();
        $cloth_material = (!empty($allPostVars['cloth_material'])) ? $allPostVars['cloth_material'] : array();
        $cloth_color = (!empty($allPostVars['cloth_color'])) ? $allPostVars['cloth_color'] : array();

        $clothe= new Clothe($cloth_id,$cloth_name,$cloth_reference,$cloth_urlImage, $cloth_category ,$cloth_brand,$cloth_material, $cloth_color,$user_id);

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
 * Créer une tenue
 * url - /addClothe
 * method - POST
 * params - clothe
 */
$app->post('/addClothes', 'authenticate', function() use ($app) {
    try{
        $clotheArray = array();
        global $user_id;
        $response = array();

        $score = "";

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);

        $urlImage = (!empty($allPostVars['urlImage'] )) ? $allPostVars['urlImage'] : "";
        $listClothe = (!empty($allPostVars['listClothe'] )) ? $allPostVars['listClothe'] : array();
        $score = (!empty($allPostVars['score'] )) ? $allPostVars['score'] : 0;

        $clothes = new Clothes(0,$urlImage, $listClothe,$score, $user_id);

        foreach ($allPostVars['listClothe'] as $valueC){
            array_push($clotheArray,$valueC['cloth_id']);
        }

        $db = new DbClotheHandler();
        $res = $db->createClothes($clothes, $clotheArray);

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

                    $tmp["cloth_color"] = new Color();
                    $tmp["cloth_color"]->setId($value2->getClothColor()->getId());
                    $tmp["cloth_color"]->setLibelle($value2->getClothColor()->getLibelle());
                    $tmp["cloth_color"]->setIdFann($value2->getClothColor()->getIdFann());

                    $tmp["cloth_reference"] = $value2->getClothReference();
                    $tmp["cloth_urlImage"] = $value2->getClothUrlImage();

                    $tmp["cloth_category"] = new Category();
                    $tmp["cloth_category"]->id = $value2->getClothCategory()->getId();
                    $tmp["cloth_category"]->libelle = $value2->getClothCategory()->getLibelle();
                    $tmp["cloth_category"]->id_fann = $value2->getClothCategory()->getIdFann();

                    $tmp["cloth_brand"] = new Brand();
                    $tmp["cloth_brand"]->id = $value2->getClothBrand()->getId();
                    $tmp["cloth_brand"]->libelle = $value2->getClothBrand()->getLibelle();
                    $tmp["cloth_brand"]->id_fann = $value2->getClothBrand()->getIdFann();

                    $tmp["cloth_material"] = new Material();
                    $tmp["cloth_material"]->id = $value2->getClothMaterial()->getId();
                    $tmp["cloth_material"]->libelle = $value2->getClothMaterial()->getLibelle();
                    $tmp["cloth_material"]->id_fann = $value2->getClothMaterial()->getIdFann();

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

    $id = (!empty($allPostVars['id'] )) ? $allPostVars['id'] : 0;
    $urlImage = (!empty($allPostVars['urlImage'] )) ? $allPostVars['urlImage'] : "";
    $listClothe = (!empty($allPostVars['listClothe'] )) ? $allPostVars['listClothe'] : array();
    $score = (!empty($allPostVars['score'] )) ? $allPostVars['score'] : 0;

    $clothes = new Clothes($id,$urlImage, $listClothe,$score, $user_id);

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
 * Met à jour une tenue
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

        $id = (!empty($allPostVars['id'] )) ? $allPostVars['id'] : 0;
        $urlImage = (!empty($allPostVars['urlImage'] )) ? $allPostVars['urlImage'] : "";
        $listClothe = (!empty($allPostVars['listClothe'] )) ? $allPostVars['listClothe'] : array();
        $score = (!empty($allPostVars['score'] )) ? $allPostVars['score'] : 0;

        $clothes = new Clothes($id,$urlImage, $listClothe,$score, $user_id);

        foreach ($allPostVars['listClothe'] as $valueC){
            array_push($clotheArray,$valueC['cloth_id']);
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
        $db0 = new DbClotheHandler();
        $db = new DbClotheHandler();
        $db2 = new DbClotheHandler();
        $db3 = new DbClotheHandler();
        $db4 = new DbClotheHandler();
        // fetching all user tasks

        $resultColor = $db0->viewAllColor();
        $resultBrand = $db->viewAllBrand();
        $resultCategory = $db2->viewAllCategory();
        $resultMaterial = $db3->viewAllMaterial();

        if($resultMaterial){
            $response["listColors"] = array();
            foreach ($resultColor as $value){
                $tmpA = array();
                $tmpA["id"] = $value->getId();
                $tmpA["libelle"] = $value->getLibelle();
                $tmpA["id_fann"] = $value->getIdFann();
                array_push($response["listColors"], $tmpA);
            }

            $response["listBrands"] = array();
            foreach ($resultBrand as $value){
                $tmpB = array();
                $tmpB["id"] = $value->getId();
                $tmpB["libelle"] = $value->getLibelle();
                $tmpB["id_fann"] = $value->getIdFann();
                array_push($response["listBrands"], $tmpB);
            }

            $response["listCategories"] = array();
            foreach ($resultCategory as $value){
                $tmpC = array();
                $tmpC["id"] = $value->getId();
                $tmpC["libelle"] = $value->getLibelle();
                $tmpC["id_fann"] = $value->getIdFann();
                array_push($response["listCategories"], $tmpC);
            }

            $response["listMaterials"] = array();
            foreach ($resultMaterial as $value){
                $tmpM = array();
                $tmpM["id"] = $value->getId();
                $tmpM["libelle"] = $value->getLibelle();
                $tmpM["id_fann"] = $value->getIdFann();
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
                $tmp["id_fann"] = $value->getIdFann();

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
                $tmp["id_fann"] = $value->getIdFann();

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
                $tmp["id_fann"] = $value->getIdFann();

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

$app->get('/getColor','authenticate', function (){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbClotheHandler();

        // fetching all user tasks
        $result = $db->viewAllColor();

        if($result){
            $response["listColors"] = array();
            foreach ($result as $value){
                $tmp = array();
                $tmp["id"] = $value->getId();
                $tmp["libelle"] = $value->getLibelle();
                $tmp["id_fann"] = $value->getIdFann();

                array_push($response["listColors"], $tmp);
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

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);

        $username = (!empty($allPostVars['username'] )) ? $allPostVars['username'] : "";
        $title = (!empty($allPostVars['title'] )) ? $allPostVars['title'] : "";
        $desc = (!empty($allPostVars['desc'] )) ? $allPostVars['desc'] : "";
        $clothes = (!empty($allPostVars['clothes'] )) ? $allPostVars['clothes'] : array();

        $post= new Post(0,$username,$title,$desc, $clothes, $user_id);

        $db = new DbPostHandler();
        $res = $db->createPost($post);

        if ($res == true ){
            $app->response->setStatus(200);
            $app->response()->headers->set('Content-Type', 'application/json');
            echo json_encode(array("id" => $res));
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
 * Liste des top postes
 * method GET
 * url /getPost
 */
$app->get('/getTopPost', 'authenticate', function(){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbPostHandler();
        $result = $db->viewTopPost($user_id);

        if($result){
            $response["posts"]= array();
            foreach ($result as $value1){
                $tmp = array();
                $tmp["username"] = $value1->getUsername();
                $tmp["title"] = $value1->getTitle();
                $tmp["desc"] = $value1->getDesc();
                $tmp["clothes"] = new Clothes();

                foreach ($value1->getClothesId() as $value){
                    $tmp1 = array();
                    $tmp["clothes"]->setClothesId($value->getClothesId());
                    $tmp["clothes"]->setUrlImage($value->getUrlImage());
                    $tmp["clothes"]->listClothe = array();
                    //$tmp1["urlImage"] = $value->getUrlImage();
                    //$tmp1["listClothe"] = array();
                    foreach ($value->getListClothe() as $value2){
                        $tmp2 = array();
                        $tmp2["cloth_id"] = $value2->getClothId();
                        $tmp2["cloth_name"] = $value2->getClothName();

                        $tmp2["cloth_color"] = new Color();
                        $tmp2["cloth_color"]->setId($value2->getClothColor()->getId());
                        $tmp2["cloth_color"]->setLibelle($value2->getClothColor()->getLibelle());
                        $tmp2["cloth_color"]->setIdFann($value2->getClothColor()->getIdFann());

                        $tmp2["cloth_reference"] = $value2->getClothReference();
                        $tmp2["cloth_urlImage"] = $value2->getClothUrlImage();

                        $tmp2["cloth_category"] = new Category();
                        $tmp2["cloth_category"]->id = $value2->getClothCategory()->getId();
                        $tmp2["cloth_category"]->libelle = $value2->getClothCategory()->getLibelle();
                        $tmp2["cloth_category"]->id_fann = $value2->getClothCategory()->getIdFann();

                        $tmp2["cloth_brand"] = new Brand();
                        $tmp2["cloth_brand"]->id = $value2->getClothBrand()->getId();
                        $tmp2["cloth_brand"]->libelle = $value2->getClothBrand()->getLibelle();
                        $tmp2["cloth_brand"]->id_fann = $value2->getClothBrand()->getIdFann();

                        $tmp2["cloth_material"] = new Material();
                        $tmp2["cloth_material"]->id = $value2->getClothMaterial()->getId();
                        $tmp2["cloth_material"]->libelle = $value2->getClothMaterial()->getLibelle();
                        $tmp2["cloth_material"]->id_fann = $value2->getClothMaterial()->getIdFann();

                        array_push($tmp["clothes"]->listClothe, $tmp2);
                    }
                    $tmp["clothes"]->setScore($value->getScore());
                    $tmp["clothes"]->setUserId($value->getUserId());
                    //$tmp1["score"] = $value->getScore();
                    //array_push($tmp["clothes"], $tmp1);
                }
                array_push($response["posts"], $tmp);
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

/**
 * Liste des derniers postes
 * method GET
 * url /getPost
 */
$app->get('/getLastPost', 'authenticate', function(){
    $app = \Slim\Slim::getInstance();
    try
    {
        global $user_id;
        $response = array();
        $db = new DbPostHandler();
        $result = $db->viewLastPost($user_id);

        if($result){
            $response["posts"]= array();
            foreach ($result as $value1){
                $tmp = array();
                $tmp["username"] = $value1->getUsername();
                $tmp["title"] = $value1->getTitle();
                $tmp["desc"] = $value1->getDesc();
                $tmp["clothes"] = new Clothes();

                foreach ($value1->getClothesId() as $value){
                    $tmp1 = array();
                    $tmp["clothes"]->setClothesId($value->getClothesId());
                    $tmp["clothes"]->setUrlImage($value->getUrlImage());
                    $tmp["clothes"]->listClothe = array();
                    //$tmp1["urlImage"] = $value->getUrlImage();
                    //$tmp1["listClothe"] = array();
                    foreach ($value->getListClothe() as $value2){
                        $tmp2 = array();
                        $tmp2["cloth_id"] = $value2->getClothId();
                        $tmp2["cloth_name"] = $value2->getClothName();

                        $tmp2["cloth_color"] = new Color();
                        $tmp2["cloth_color"]->setId($value2->getClothColor()->getId());
                        $tmp2["cloth_color"]->setLibelle($value2->getClothColor()->getLibelle());
                        $tmp2["cloth_color"]->setIdFann($value2->getClothColor()->getIdFann());

                        $tmp2["cloth_reference"] = $value2->getClothReference();
                        $tmp2["cloth_urlImage"] = $value2->getClothUrlImage();

                        $tmp2["cloth_category"] = new Category();
                        $tmp2["cloth_category"]->id = $value2->getClothCategory()->getId();
                        $tmp2["cloth_category"]->libelle = $value2->getClothCategory()->getLibelle();
                        $tmp2["cloth_category"]->id_fann = $value2->getClothCategory()->getIdFann();

                        $tmp2["cloth_brand"] = new Brand();
                        $tmp2["cloth_brand"]->id = $value2->getClothBrand()->getId();
                        $tmp2["cloth_brand"]->libelle = $value2->getClothBrand()->getLibelle();
                        $tmp2["cloth_brand"]->id_fann = $value2->getClothBrand()->getIdFann();

                        $tmp2["cloth_material"] = new Material();
                        $tmp2["cloth_material"]->id = $value2->getClothMaterial()->getId();
                        $tmp2["cloth_material"]->libelle = $value2->getClothMaterial()->getLibelle();
                        $tmp2["cloth_material"]->id_fann = $value2->getClothMaterial()->getIdFann();

                        array_push($tmp["clothes"]->listClothe, $tmp2);
                    }
                    $tmp["clothes"]->setScore($value->getScore());
                    $tmp["clothes"]->setUserId($value->getUserId());
                    //$tmp1["score"] = $value->getScore();
                    //array_push($tmp["clothes"], $tmp1);
                }
                array_push($response["posts"], $tmp);
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

/*------------------------------------------------SIMILARITY----------------------------------------------------*/
/**
 * Get similarity
 * url - /getSimilarity
 * method - POST
 * params - post
 */
$app->post('/getSimilarity', 'authenticate', function() use ($app) {
    try{
        global $user_id;
        $response = array();

        $content = trim(file_get_contents("php://input"));
        $allPostVars = json_decode($content, true);

        $cloth_id = (!empty($allPostVars['cloth_id'])) ? $allPostVars['cloth_id'] : 0;
        $cloth_name = (!empty($allPostVars['cloth_name'])) ? $allPostVars['cloth_name'] : "";
        $cloth_reference = (!empty($allPostVars['cloth_reference'])) ? $allPostVars['cloth_reference'] : "";
        $cloth_urlImage = (!empty($allPostVars['cloth_urlImage'])) ? $allPostVars['cloth_urlImage'] : "";
        $cloth_category = (!empty($allPostVars['cloth_category'])) ? $allPostVars['cloth_category'] : array();
        $cloth_brand = (!empty($allPostVars['cloth_brand'])) ? $allPostVars['cloth_brand'] : array();
        $cloth_material = (!empty($allPostVars['cloth_material'])) ? $allPostVars['cloth_material'] : array();
        $cloth_color = (!empty($allPostVars['cloth_color'])) ? $allPostVars['cloth_color'] : array();

        $clothe= new Clothe($cloth_id,$cloth_name,$cloth_reference,$cloth_urlImage, $cloth_category ,$cloth_brand,$cloth_material, $cloth_color,$user_id);

        $category = $clothe->getClothCategory();
        $material = $clothe->getClothMaterial();
        $color = $clothe->getClothColor();

        $fann = new Network();
        $similarityTab = $fann->use($category['id_fann'],$material['id_fann'],$color['id_fann']);
        $db = new DbClotheHandler();
        $result = $db->getSimilarity($user_id, $similarityTab);

        if($result){
            $response["listClothe"] = array();

            foreach ($result as $value){
                $tmp = array();
                $tmp["cloth_id"] = $value->getClothId();
                $tmp["cloth_name"] = $value->getClothName();

                $tmp["cloth_color"] = new Color();
                $tmp["cloth_color"]->setId($value->getClothColor()->getId());
                $tmp["cloth_color"]->setLibelle($value->getClothColor()->getLibelle());
                $tmp["cloth_color"]->setIdFann($value->getClothColor()->getIdFann());

                $tmp["cloth_reference"] = $value->getClothReference();
                $tmp["cloth_urlImage"] = $value->getClothUrlImage();

                $tmp["cloth_category"] = new Category();
                $tmp["cloth_category"]->id = $value->getClothCategory()->getId();
                $tmp["cloth_category"]->libelle = $value->getClothCategory()->getLibelle();
                $tmp["cloth_category"]->id_fann = $value->getClothCategory()->getIdFann();

                $tmp["cloth_brand"] = new Brand();
                $tmp["cloth_brand"]->id = $value->getClothBrand()->getId();
                $tmp["cloth_brand"]->libelle = $value->getClothBrand()->getLibelle();
                $tmp["cloth_brand"]->id_fann = $value->getClothBrand()->getIdFann();

                $tmp["cloth_material"] = new Material();
                $tmp["cloth_material"]->id = $value->getClothMaterial()->getId();
                $tmp["cloth_material"]->libelle = $value->getClothMaterial()->getLibelle();
                $tmp["cloth_material"]->id_fann = $value->getClothMaterial()->getIdFann();

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

$app->run();
