<?php

class Endpoint {
    public $start_time;
    
    function __construct() {
        $this->start_time = time();
        
        ini_set('error_log', '/home/sean/errors');
        ini_set('error_append_string', '\n---------');
        error_log("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n");

        require_once('/home/sean/projects/common/orm.php');
        require_once('/home/sean/projects/common/helper.php');

        $here = scandir('.');

        foreach ($here as $v) {
            if (strpos($v, '.php') === FALSE) {
                continue;
            }
            require_once(__dir__.'/'.$v);
        }        
    }
    
    function exec() {
        try {
            $lvb = new LongPollQuery;
            $lvb->poll();
        } catch (Exception $e) {
            echo json_encode(array('Exception' => $e->getMessage()));
        }
    }
}

$endpoint = new Endpoint;
$endpoint->exec();