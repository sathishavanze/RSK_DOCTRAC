<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class API_model extends MY_Model {
  function __construct()
  { 
    parent::__construct();
  }

  /**
  * Function to get Bot Credrentials
  *
  * @param Auth (String)
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return object
  * @since July 18th 2020
  * @version Bot API
  *
  */
  function GetBotCredrentials($Auth){
    $this->db->select("*");  
    $this->db->from('mSettings');
    $this->db->where('SettingValue', $Auth);
    $output = $this->db->get();
    $mSettings = $output->result_array();
    $arr = [];
    foreach ($mSettings as $key => $value) {
      if($value['SettingField'] == 'BotAuthkey'){
        $arr['BotAuthkey'] = $value['SettingValue'];
      }
    }
    return $arr;
  }

  /**
  * Function to get OrderDetails 
  *
  * @param OrderNumber (String)
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return array
  * @since July 17th 2020
  * @version Bot API
  *
  */
  function GetOrderDetails($OrderNumber){
    $this->db->Select('*')->from('tOrders')->where('OrderNumber', $OrderNumber);
    return $this->db->get()->row();
  }

  /**
  * Function to create directory for the specified path
  *
  * @param Path (String)
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return array
  * @since July 17th 2020
  *
  */
  public function CreateDirectoryToPath($Path = '')
  {
    if (empty($Path)) 
    {
      die('No Path to create directory');
    }
    if (!file_exists($Path)) {
      if (!mkdir($Path, 0777, true)) die('Unable to create directory');
    }
    chmod($Path, 0777);
  }

}?>