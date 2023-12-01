<?php
use Meso\Meso;
use HezarDastan\HezarDastan;
use HezarDastan\Dast;
use App\Config\DefaultConfig;
use SessionManager\SessionManager;
use App\Controllers\AppController;

require_once("../Configs/DefaultConfig.php");
require_once("../Resources/Meso/Meso.php");
require_once("../Resources/Meso/Base.php");
require_once("../Resources/HezarDastan/HezarDastan.php");
require_once("../Resources/SessionManager/SessionManager.php");
require_once("../Src/Controllers/AppController.php");

#region Sessiontbl
$sessions = new Swoole\Table(2024);
$sessions->column('ip', Swoole\Table::TYPE_STRING, 15);       //1,2,4,8
$sessions->column('ts', Swoole\Table::TYPE_STRING, 30);
$sessions->column('user', Swoole\Table::TYPE_STRING, 40);       //1,2,4,8
$sessions->column('type', Swoole\Table::TYPE_STRING, 10);       //1,2,4,8
//$sessions->column('field', Swoole\Table::TYPE_STRING, 10);       //1,2,4,8
$sessions->create();
#endregiont
$Conf = DefaultConfig\RetunConf();
$Meso = new Meso($Conf->Database->Address, $Conf->Database->User, $Conf->Database->Password, $Conf->Database->Database);
if(!$Meso->connect())
{
    print_r("Error: Service can not initialize \n reason: DatabaseCommand Connection Error");
    exit;
}
$HD = new HezarDastan($Meso);
$HFunc = new Dast();
$SM = new SessionManager($sessions);
#region phpmailer
//if($Conf->IsSMTP)
//{
//    $PM->isSMTP();                                            //Send using SMTP
//    $PM->Host = 'smtp.focusapp.site';                           //Set the SMTP server to send through
//    $PM->SMTPAuth = true;                                     //Enable SMTP authentication
//    $PM->Username = 'admin@focusapp.site';                       //SMTP username
//    $PM->Password = 'amir#admin';                                 //SMTP password
//    $PM->Port = 25;
//}
//try
//{
//    $PM->setFrom('verification@compo.team', 'COMPEX');
//}
//catch (Exception $e)
//{
//    print_r("PHP MAILER ERROR : {$PM->ErrorInfo}");
//    exit("Tick server exited in initialize phase");
//}
#endregion
$App = new AppController();
$App->HD = $HD;
$App->Meso = $Meso;
$App->SM = $SM;
$App->Conf = $Conf;

$App->LinkControllers();
$App->LinkEntities();

use Swoole\HTTP\Server;
use Swoole\HTTP\Request;
use Swoole\HTTP\Response;
$sch = new Swoole\Coroutine\Scheduler();
$sch->set(['hook_flags' => SWOOLE_HOOK_ALL]); //mysqli client and PDO
//define server object and config
$Server = new Swoole\HTTP\Server(
    $Conf->Network->MainListen,
    $Conf->Network->PortListen,
);
$Server->set([
    'upload_tmp_dir' => $Conf->App->UploadTempDir,
    'ssl_cert_file' => $Conf->App->SSLCERTFile,
//    'ssl_key_file'// => ';/etc/letsencrypt/live/moshavertik.ir/privkey.pem',
    'daemonize' => $Conf->App->Background,
    'log_file' => $Conf->Log->App,
    'open_http_protocol' => $Conf->App->OpenHTTProtocol
]);
$Server->on("start", [$App, 'StartApp']);

$Server->on("request", [$App, 'HandleApis']);

?>