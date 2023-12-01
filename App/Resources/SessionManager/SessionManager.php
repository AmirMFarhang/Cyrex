<?php

namespace SessionManager;
date_default_timezone_set('Asia/Tehran');

class SessionManager
{
    private $sess;

    public function __construct($tbl)
    {
        $this->sess = $tbl;
    }

    public function AddSession(string $username, string $ip, string $type): string
    {
        foreach ($this->sess as $row => $value) {
            if ($value['user'] == $username) {
                echo 'exist';
                return $row;
            }
        }
        $code = uniqid($username . $ip, true);
        echo 'added';
        $this->sess->set($code, array('user' => $username, 'ts' => time(), 'ip' => $ip, 'type' => $type));
        return $code;
    }

    public function DelSession(string $code): void
    {
        $this->sess->del($code);
    }

    public function ValidateSession($code, $cip): bool
    {
        if ($this->sess->exists($code)) {
            $date = $this->sess->get($code, 'ts');
            $ip = $this->sess->get($code, 'ts');
            $diff = (time() - $date) / 60;
            if ($diff >= 60) {
                if ($ip != $cip) {
                    $this->DelSession($code);
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function ReturnSessionData(string $sid): string
    {
        $data = $this->sess->get($sid);
        $encoded = '';
        foreach ($data as $row => $value) {
            $encoded = $value . '%';
        }
        return substr_replace($encoded, "", -1);
    }

    public function ReturnSessionUser(string $sid): string
    {
        $data = $this->sess->get($sid, 'user');
        return $data;
    }

    public function GetProp(string $sid, $prop): string
    {
        $data = $this->sess->get($sid, $prop);
        return $data;
    }

    public function FindByProp($val, $type)
    {
        foreach ($this->sess as $row => $value) {
            if ($value[$type] == $val) {
                return $row;
            }
        }
        return false;
    }
}