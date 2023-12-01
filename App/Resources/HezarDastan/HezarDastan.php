<?php
namespace HezarDastan;
require_once (__DIR__ . '/Dast.php');
require_once (__DIR__ . "/Hezar.php");
/* this class require Meso
- this class only designed to work with swoole/http for now
- this is official version 0.0.0.1 beta
*/

class HezarDastan extends Dast
{

    public $DBIntractor = '';
    public function __construct($dbi)
    {
        $this->DBIntractor = $dbi;
    }
    public function doAction(string $action, string $table, object $object, $response)
    {
        try
        {

            $val = $this->DBIntractor->$action($table, $object);
            if($val)
            {
                $response->status(200);
                $response->end($val);
            }
            else
            {
                $response->status(500);
                $response->end();
            }
        }
        catch(\Throwable $th)
        {
            $response->status(500);
            $response->end();
        }
    } //get the response do the request with meso
}