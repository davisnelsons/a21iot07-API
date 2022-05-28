<?php
//set_include_path('');
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/db_config/config.php';


$klein = new \Klein\Klein();
//masterclass

$klein->respond(function ($request, $response, $service, $app) use ($klein) {
    // Handle exceptions => flash the message and redirect to the referrer
    $klein->onError(function ($klein, $err_msg) {
        $klein->service()->flash($err_msg);
        $klein->service()->back();
    });
    //connect to DB
    $database = new PDOdb();
    $app->db = $database->getConnection();
    //instantiate models
    $models = (object) array(
        "userModel" => new User($app->db),
        "bpmModel" => new Bpm($app->db),
        "stepsModel" => new Steps($app->db),
        "deviceModel" => new Device($app->db)
    );
    //instantiate controllers   
    $app->bpmController = new BpmController($models);
    $app->userController = new UserController($models);
    $app->stepsController = new StepsController($models);
    $app->deviceController = new DeviceController($models);
});



$klein->respond(function ($request, $response, $service, $app) {
    $params = $request->params();
    if  (array_key_exists("bpm", $params)
    && array_key_exists("timeESP", $params)
    && array_key_exists("device_id", $params)) 
    {
        
            if($app->bpmController->create($request)) {
                $deviceID = $request->params()["device_id"];
                $bpm = $request->params()["bpm"];
                $app->userController->setUserIDfromDeviceID($deviceID);
                $maxHR = ($app->userController->getUserData(null))["max_hr"];
                if($bpm > $maxHR) {
                    $app->userController->sendNotification("Watch out, heart rate threshold reached!");
                }
                return json_encode(array(
                    "message"=>"insert successful"
                ));
            }

    }
    else if (array_key_exists("steps", $params)
    && array_key_exists("timeESP", $params)
    && array_key_exists("device_id", $params)
    ) {
        if($app->stepsController->create($request)) {
            $deviceID = $request->params()["device_id"];
            $stepsMade = $request->params()["steps"];
            $app->userController->setUserIDfromDeviceID($deviceID);
            $totalStepsToday = $app->stepsController->readAllToday();
            $stepGoal = ($app->userController->getUserData(null))["daily_steps"];
            if ($totalStepsToday >= $stepGoal & $totalStepsToday - $stepsMade <= $stepGoal ) {
                $app->userController->sendNotification("Congratulations, you have reached your daily step goal!");
            }
            return json_encode(array(
                "message"=>"insert successful"
            ));
        } else {
            return json_encode(array(
                "error"=>"insert failed"
            ));
        }
    }
} );

//Login endpoint
$klein->respond("POST", "/apiv2/user/login[*]", function ($request, $response, $service, $app) {
    $response->json($app->userController->login($request));
});

/*
User endpoint
*/

$klein->respond("/apiv2/user/[:action].[*]?", function ($request, $response, $service, $app) {
    
    if($request->action == "get_user") {
        if($app->userController->authorizeUser($request)) {
            $userData = $app->userController->getUserData($request);
            return json_encode($userData);
        }
    } else if ($request->action == "post_settings") {
        if($app->userController->authorizeUser($request)) {
            return ($app->userController->setSettings($request)) ?
                json_encode(array("message"=>"insert successful")) : 
                json_encode(array("error"=>"failed to insert"));
        }
    } else if ($request->action == "post_user_data") {
        if($app->userController->authorizeUser($request)) {
            return ($app->userController->setUserData($request)) ?
                json_encode(array("message"=>"insert successful")) : 
                json_encode(array("error"=>"failed to insert"));
        }
    } else if ($request->action == "update_firebase") {
        if($app->userController->authorizeUser($request)) {
            return ($app->userController->setFirebaseToken($request))  ?
            json_encode(array("message"=>"insert successful")) : 
            json_encode(array("error"=>"failed to insert"));
        }
    } else if ($request->action == "link_device") {
        if($app->userController->authorizeUser($request)) {
            return $app->deviceController->linkDevice($request) ? 
            json_encode(array("message"=>"insert successful")) : 
            json_encode(array("error"=>"failed to insert"));
        }
    }
});


/*
BPM endpoint
*/
$klein->respond("/apiv2/bpm/[:action].[*]?/[:specifier]?", function ($request, $response, $service, $app) {
    //read bpm data
    if($request->action == "read") {
        if($app->userController->authorizeUser($request)) {
            return $app->bpmController->read($request);
        } else {
            return json_encode(array(
                "error"=>"invalid token"
            ));
        }
    }
});

/*
Steps endpoint
*/
$klein->respond("/apiv2/steps/[:action].[*]?", function($request, $response, $service, $app) {
    if($request->action == "read") {
        if($app->userController->authorizeUser($request)) {
            return $app->stepsController->read($request);
        } else {
            return json_encode(array(
                "error"=>"invalid token"
            ));
        }
    } else if($request->action == "create") {
        if($app->stepsController->create($request)) {
            $deviceID = $request->params()["device_id"];
            $stepsMade = $request->params()["steps"];
            $app->userController->setUserIDfromDeviceID($deviceID);
            $totalStepsToday = $app->stepsController->readAllToday();
            $stepGoal = ($app->userController->getUserData(null))["daily_steps"];
            if ($totalStepsToday >= $stepGoal & $totalStepsToday - $stepsMade <= $stepGoal ) {
                $app->userController->sendNotification("Congratulations, you have reached your daily step goal!");
            }
            return json_encode(array(
                "message"=>"insert successful"
            ));
        } else {
            return json_encode(array(
                "error"=>"insert failed"
            ));
        }
    }
}); 

/*
Device Endpoint(s)
*/
$klein->respond("/apiv2/device/get_device_notifications.[*]?", function ($request, $response, $service, $app) {
    return json_encode(
        $app->deviceController->getDeviceNotifications($request)
    );
});

$klein->respond("/apiv2/random.php", function () {
    return json_encode(array(
        "test"=>rand(0,1)
    ));
});

$klein->respond("/apiv2/test_notification.php", function () {
    $app->userController->sendNotification("Congratulations, you have reached your daily step goal!");
});

$klein->dispatch();
