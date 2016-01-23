<?php
class LongPollQuery extends Endpoint {
    public $start_time;   
    public $log = array();
    public $ttl_period = ('+1 year');
    
    function __construct() {
always_pre(get_defined_vars());
        $this->start_time = time();
    }
    
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
    
    function check_state_changes($game_id, $last_update) {
// always_pre(get_defined_vars());
        if (empty($game_id) || empty($last_update)) {
            throw new Exception('Required arg missing from game_id|last_update');
        }
        $db = new Db();
        $details['verb'] = 'select';
        $details['actor'] = 'ludwig_von_beatdown';
        $details['where_aa'] = array('_override_text' => "id = $game_id && last_updated >= $last_update");
        $result = $db->cmd( $details );
        
// always_pre($result);
        if ($result['rows_affected'] <= 0) {
            return NULL;
        }
        return $result;
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

    function get_game_data_for_player($player_id) {
always_pre(get_defined_vars());
        $db = new Db();
        $details['query_override'] = "select * from ludwig_players where player_id = '$player_id'";
        $result = $db->cmd( $details );

        if (isset($result['data'])) {
            return $result['data'];
        }
        return NULL;
        
        
    }
    
    function create_new_game($player_id) {
always_pre(get_defined_vars());
        $db = new Db;
        $details['verb'] = 'insert';
        $details['actor'] = 'ludwig_von_beatdown';
        $details['relevant'] = array(
            'is_active' => 1,
            'game_state' => 1,
            'game_x' => $this->args['x'],
            'game_y' => $this->args['y'],
            'last_updated' => time(),
            'rand_seed' => $this->random_string(8),
            'player_registry' => json_encode(array($player_id)),
        );
        $outcome = $db->cmd( $details );                
    }
    
    function register_game_activity($game_id, $player_id) {
always_pre(get_defined_vars());
        $db = new Db;
        $details = array();
        
        $player_data = $this->get_game_data_for_player($player_id);

        if (empty($game_id)) {
            $game_id = $this->create_new_game($player_id);
        }
        
        $relevant = array(
            'game_id' => $game_id,
            'player_state' => 0,
            'ttl' => time() + strtotime($this->ttl_period)
        );

        if (empty($player_data)) {
            $details['verb'] = 'insert';
            $relevant['player_id'] = strval($player_id);
        } else {
            $details['verb'] = 'update';
            $details['where_aa'] = array('player_id' => $player_id);
        }
        $details['actor'] = 'ludwig_players';        
        $details['relevant'] = $relevant;
always_pre($details);
        $outcome = $db->cmd( $details );        
    }
    
    function poll() {
always_pre(get_defined_vars());
        $this->args = $this->sanitize_input();
        
        // always_pre(json_encode($args));
        
        extract($this->args);
        
        if (empty($id) || empty($last_updated) || empty($player_id)) {
            throw new Exception('Required arg missing from id|last_update|player_id');
        }
        
        $this->register_game_activity($id, $player_id);

        //Max execution time: 25s
        while (time() - $this->start_time < 25) {
            $update = $this->check_state_changes($id, $last_updated);
            if (!is_null($update)) {
                $this->ret($update);
            }
            $this->log[microtime()] = 'continuing';
            usleep(100000);
            continue;        
        }
        $this->ret('timeout');
    }
    
    function random_string($num_chars = 20, $hex = FALSE) {
        $index = '0123456789abcdef';
        if ($hex == FALSE) {
            $index .= 'ghijklmnopqrstuvwxyz';
        }
        $str = '';
        for (; $num_chars > 0; $num_chars--) {
            $str .= $index[mt_rand(0, strlen($index) - 1)];
        }
        return $str;
    }
}