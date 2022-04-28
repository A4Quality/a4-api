<?php

namespace App\Controller;

use App\Basics\Logs;
use App\DAO\LogsDAO;

class LogsController
{
    public function getLog(Logs $logs){
        $authDAO = new LogsDAO();
        return $authDAO->getLog($logs);
    }
}