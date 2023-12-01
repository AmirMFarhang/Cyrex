<?php

namespace App\Controllers;
use App\Controllers;
use App\Entities;
class AppController
{
    public $HD;
    public $SM;
    public $Meso;
    public $Conf;
    public $ApiList =
        [
            0 => ['index', 'Example']
        ];

    public function LinkControllers() {
        // Directory where your classes are located
        $directory = __DIR__;

        // List of class files in the directory
        $classFiles = scandir($directory);

        // Loop through class files to include them
        foreach ($classFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $className = pathinfo($file, PATHINFO_FILENAME);
                if($className == "AppController")
                    continue;
                $classFile = $directory . '/' . $file;
                $afterclassFile = $directory . '/After/After' . $file;
                $beforeclassFile = $directory . '/Before/Before' . $file;
                if (file_exists($classFile)) {
                    require_once $className.'.php';
                    require_once $afterclassFile;
                    require_once $beforeclassFile;
                    $class = "App\Controllers\\".$className;
                    $afterclass = "App\Controllers\After\After".$className;
                    $afterclassname = "After".$className;
                    $beforeclass = "App\Controllers\Before\Before".$className;
                    $beforeclassname = "Before".$className;
                    $this->$className = new $class;
                    $this->$className->$afterclassname = new $afterclass;
                    $this->$className->$beforeclassname = new $beforeclass;
                }
            }
        }
    }

    public function LinkEntities() {

        // Directory where your classes are located
        $directory = realpath(__DIR__ . '/../Models/Entities');
        // List of class files in the directory
        $classFiles = scandir($directory);

        // Loop through class files to include them
        foreach ($classFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === "php") {
                $className = pathinfo($file, PATHINFO_FILENAME);
                $classFile = $directory . '/' . $file;
                if (file_exists($classFile)) {
                    require_once $classFile;
                }
            }
        }
    }

    public function HandleApis($request, $response)
    {
        print_r("received \n");
        $response->header("Access-Control-Allow-Origin", "*");
        $response->header("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
        $response->header("Access-Control-Allow-Headers", "Content-Type, Authorization");
        $response->setheader("Access-Control-Allow-Origin", "*");
        $response->setheader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
        $response->setheader("Access-Control-Allow-Headers", "Content-Type, Authorization");
        if ($this->Conf->IsDebug)
        {
//            var_dump($request);
            // var_dump($request->files);
        }
        if($this->Conf->API->ActionNumberAPI) {
            $var = $request->post['action'];
            if (isset($this->ApiList[$request->post['action']])) {
                $api = $this->ApiList[$request->post['action']];
                // $api[0] contains the action name (e.g., 'index')
                // $api[1] contains the controller name (e.g., 'ExampleController')
                $actionName = $api[0];
                $controllerName = $api[1].'Controller';
                $this->$controllerName->Response = $response;
                $this->$controllerName->Request = $request;
                $this->$controllerName->$actionName();
                return;
            } else {
                // Handle the case where the requested API doesn't exist
                print_r("API not found");
                $response->status('403');
                $response->end();
                return;
            }
        }
        //URL based API
        $path = $request->server['request_uri'];
        // Remove leading and trailing slashes and explode the URL
        $parts = explode('/', trim($path, '/'));
        $controllerName = ucfirst($parts[0]) . 'Controller';
        if(count($parts) >= 1)
        {
            //index
//            $response->header('Content-Type', 'text/html; charset=utf-8');
//            // Read and send the HTML content
//            $htmlContent = file_get_contents('./View/index.html'); // Replace with your file path
//            $response->end($htmlContent);
        }
        if($request->post == null && $request->get == null)
        {
            if($path == '/')
            {
                $path = "/index.html";
            }
            //this is only view
            $staticFilePath = './View' . $path;
            // Check if the file exists and is a regular file
            if (is_file($staticFilePath)) {
                // Determine the content type based on the file extension
                $mimeTypes = [
                    'html' => 'text/html',
                    'php' => 'application/x-httpd-php',
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                ];

                // Determine the file extension
                $fileExtension = pathinfo($staticFilePath, PATHINFO_EXTENSION);

                // Set the appropriate Content-Type header based on the file extension
                if (array_key_exists($fileExtension, $mimeTypes)) {
                    $response->header('Content-Type', $mimeTypes[$fileExtension]);
                }

                // Read and send the file content
                $staticContent = file_get_contents($staticFilePath);
                $response->end($staticContent);
                return;
            }
            // Handle 404 for non-existent files
            else {
                $response->status(404);
                $response->end();
                return;
            }

            return;
        }
        // Ensure there are at least two parts (controller and method)
        if (count($parts) >= 2) {
            $methodName = $parts[1];


            // Check if the method exists
            if (method_exists($this->$controllerName, $methodName)) {
                // Call the appropriate method
                $content = $this->$controllerName->$methodName();
                $response->end($content);
            } else {
                $response->status(404);
                $response->end("Not Found");
            }
        } else {
            $res = "
   _____                    
  / ____|                   
 | |    _   _ _ __ _____  __
 | |   | | | | '__/ _ \ \/ /
 | |___| |_| | | |  __/>  < 
  \_____\__, |_|  \___/_/\_\
   ..... __/ |..............
   .....|___/...............
        ";
            $response->end($res);
        }
    }
    public function StartApp($server)
    {
        print_r("Cyrex server has started!");
    }

    public function before()
    {
        $className = get_called_class();
        // Extract the class name without the namespace and without the leading "App\" portion
        $childClass = substr($className, strrpos($className, '\\') + 1);
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $callingMethod = $trace[1]['function'];
        $beforeController = 'Before'.$childClass;
        $this->$beforeController->$callingMethod();
    }

    public function after()
    {
        $className = get_called_class();
        // Extract the class name without the namespace and without the leading "App\" portion
        $childClass = substr($className, strrpos($className, '\\') + 1);
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $callingMethod = $trace[1]['function'];
        $beforeController = 'After'.$childClass;
        $this->$beforeController->$callingMethod();
    }

    public function ball()
    {
        $className = get_called_class();
        // Extract the class name without the namespace and without the leading "App\" portion
        $childClass = substr($className, strrpos($className, '\\') + 1);
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $callingMethod = 'all';
        $beforeController = 'Before'.$childClass;
        $this->$beforeController->$callingMethod();
    }

    public function fall()
    {
        $className = get_called_class();
        // Extract the class name without the namespace and without the leading "App\" portion
        $childClass = substr($className, strrpos($className, '\\') + 1);
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $callingMethod ='all';
        $beforeController = 'After'.$childClass;
        $this->$beforeController->$callingMethod();
    }



}