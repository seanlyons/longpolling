<?php
class Io extends Endpoint {
    public $valid_input = array(
        //Functions:
        //Create a new game at their x/y.
        
        
        
        
        
        
        
        
        
        
        
        
        'greeting' => array(
            'last_updated' => array('type' => 'int', 'required' => TRUE),
            'id' => array('type' => 'int', 'required' => TRUE),
            'player_id' => array('type' => 'alphanum', 'required' => TRUE),
        ),
    );
    public $args = array();
    
    function sanitize_input() {
always_pre(get_defined_vars());
        $valid = array();
        
        foreach ($this->valid_input as $k => $v) {
            if (!isset($_REQUEST[$k])) {
                if ($v['required'] == TRUE) {
                    throw new Exception('Required parameter not found: '.$k);
                }
                continue;
            }
            $temp = $_REQUEST[$k];
            switch ($v['type']) {
                default:
                    continue 2;
                case 'alphanum':
                    $temp = strtolower($temp);
                    $replaced = preg_replace('/[^a-z0-9]/', '', $temp);
                    if (empty($replaced)) {
                        throw new Exception('Unacceptable arg for expected non-alphanum parameter '.$k);
                    }
                    $valid[$k] = $replaced;
                    break;
                case 'int':
                    $replaced = preg_replace('/[^0-9]/', '', $temp);
                    if (empty($replaced)) {
                        throw new Exception('Unacceptable arg for expected integer parameter '.$k);
                    }
                    $valid[$k] = $replaced;
                    break;
            }
        }
        return $valid;
    }
        
    function ret( $msg = NULL) {
always_pre(get_defined_vars());
        if (empty($msg)) {
            $msg = debug_backtrace();
        }
        $ret['log'] = $this->log;
        $ret['msg'] = $msg;
always_pre($ret);
        exit;
    }
}