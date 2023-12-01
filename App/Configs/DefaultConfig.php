<?php
namespace App\Config\DefaultConfig;

function RetunConf()
{
    return (object)array(
        'IsDebug' => true,
        'Session' => true,
        'IsSMTP'  => false,
        'Network' => (object)[
            "Address" => "127.0.0.1",
            "MainListen" => "0.0.0.0",
            "LocalListen" => "127.0.0.1",
            "PortListen" => "80",
            "PortReserved" => "5070"
        ],
        'Email' => (object)[
            "SMTPServer" => "smtp.test.site",
            "VerificationEmail" => "verification@test.site",
            "VerificationTitle" => "",
        ],
        'Database' => (object)[
            "User" => "",
            "Password" => "",
            "Database" => "",
            "Address" => "" //fill in with your database info
        ],
        'API' => (object)[
            "ActionNumberAPI" => false,
            "CustomRoute" => false,
        ],
        'Log' => (object)[
            "App" => './App.log',
            "Database" => true, //NOT MAINTAINED
        ],
        'App' => (object)[
            "Background" => false,
            "UploadTempDir" => "./files",
            "OpenHTTProtocol" => false,
//            "ServeView" => false, //NOT MAINTAINED
//            "Encrypt" => false, //NOT MAINTAINED
            'SSLCERTFile' => false,
//            'SSLKEYFile' => ,
        ]
    );
}
