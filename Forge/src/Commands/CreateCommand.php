<?php

namespace Cyrex\Commands;
use Cyrex\Commands\DoctorCommand;
class CreateCommand
{
    public function handle($args)
    {
        if(!isset($args[0]))
        {
            print_r("Create command need the creation type");
            return;
        }
        $obj = $args[0];
        switch ($obj)
        {
            case 'controller':
                if(!isset($args[1]))
                {
                    print_r("Create command need the name of the controller");
                    return;
                }
                $doc = new DoctorCommand();
                $doc->handle();

                break;
        }
    }
}