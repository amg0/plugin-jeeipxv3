<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
const JEEIPXV3 = 'jeeipxv3';     // plugin logical name

class jeeipxv3 extends eqLogic {

  private static $ipxDevices = array(
    "led" => array( 0, 31 ),    // min, max idx on IPX card
    "btn" => array( 0, 31 ),
    "analog" => array( 0, 15 )
  );

  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */
/*     * ***********************Methode static*************************** */

public static function daemon() {
  log::add(JEEIPXV3, 'debug', __METHOD__ . ' running: start');
  $starttime = microtime (true);   // current time in sec as a float

  // for all root equipments
  foreach (self::byType(JEEIPXV3) as $eqLogic) {
    // only the root equipment must refresh data from the IPX
    if (is_null( $eqLogic->getConfiguration('type',null) )) {
      $eqLogic->refreshFromIPX();
    }
  }

  $seconds = config::byKey('refresh_freq', JEEIPXV3, 120, true);
  $endtime = microtime (true);     // current time in sec as a float
  if ( $endtime - $starttime < $seconds )
  {
    $ms = floor(($seconds - ($endtime - $starttime))*1000000);
    log::add(JEEIPXV3, 'info', sprintf('%s refresh_freq:%d sleeping for millisec:%d',__METHOD__,$seconds,$ms/1000) );
    usleep($ms);
  }
}

public static function deamon_info() {
  //log::add(JEEIPXV3, 'debug', __METHOD__);
  $return = array();
  $return['log'] = __CLASS__;
  $return['state'] = 'nok';
  $cron = cron::byClassAndFunction(JEEIPXV3, 'daemon');
  if (is_object($cron) && $cron->running()) {
    $return['state'] = 'ok';
  }
  $return['launchable'] = 'ok';
  return $return;
}

public static function deamon_start($debug = false) {
  log::add(JEEIPXV3, 'debug', __METHOD__);
  self::deamon_stop();
  $deamon_info = self::deamon_info();
  if ($deamon_info['launchable'] != 'ok') {
    throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
  }
  $cron = cron::byClassAndFunction(JEEIPXV3, 'daemon');
  if (!is_object($cron)) {
    throw new Exception(__('Tâche cron introuvable', __FILE__));
  }
  $cron->run();
}

public static function deamon_stop() {
  log::add(JEEIPXV3, 'debug', __METHOD__);
  $cron = cron::byClassAndFunction(JEEIPXV3, 'daemon');
  if (!is_object($cron)) {
    throw new Exception(__('Tâche cron introuvable', __FILE__));
  }
  $cron->halt();
}

public static function deamon_changeAutoMode($mode) {
  log::add(JEEIPXV3, 'debug', __METHOD__.'('.$mode.')');
  $cron = cron::byClassAndFunction(JEEIPXV3, 'daemon');
  if (!is_object($cron)) {
    throw new Exception(__('Tâche cron introuvable', __FILE__));
  }
  $cron->setEnable($mode);
  $cron->save();
}

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  public static function cron() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
    $this->setEqType_name('jeeipxv3');
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
    $type = $this->getConfiguration('type',null);
    switch($type) {
      case 'led': { // Relay
        $cmdEtat = $this->createOrUpdateCommand( 'Etat', 'status', 'info', 'binary', 1, 'GENERIC_INFO' );
        $this->createOrUpdateCommand( 'On', 'btn_on', 'action', 'other', 1, 'LIGHT_ON', (int) $cmdEtat->getId() );
        $this->createOrUpdateCommand( 'Off', 'btn_off', 'action', 'other', 1, 'LIGHT_OFF', (int) $cmdEtat->getId() );
        break;
      }
      default: {  // Root Equipment
        $this->createOrUpdateCommand( 'Etat', 'status', 'info', 'binary', 1, 'ENERGY_STATE' );
        $this->createOrUpdateCommand( 'Version', 'version', 'info', 'string', 1, 'GENERIC_INFO' );
        $this->createOrUpdateCommand( 'MAC', 'mac', 'info', 'string', 1, 'GENERIC_INFO' );
        $this->createOrUpdateCommand( 'Update Time', 'updatetime', 'info', 'string', 0, 'GENERIC_INFO' );
        $this->createOrUpdateCommand( 'Last XML', 'lastxml', 'info', 'string', 0, 'GENERIC_INFO' );
        $this->createOrUpdateCommand( 'Config Push', 'configpush', 'action', 'other', 0, 'GENERIC_ACTION' );
        $this->readConfigurationFromIPX();
        break;
      }
    }
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());

    // if this is a root EqLogic then lets search for all its children
    $type = $this->getConfiguration('type',null);

    if (is_null($type)) { 
      $idroot = $this->getId();
      foreach (self::byType(JEEIPXV3) as $eqLogic) {
        // if it is a children, then remove it
        if ($idroot == $eqLogic->getConfiguration('rootid',null) ) {
          $eqLogic->remove();    
        }
      }
    }    
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
  }

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  // Logical ID for child equipment
  // rootid_xxxn xxx is ipx class and n is the index
  public function getChildID($suffix) {
    return $this->getId()."_".$suffix;
  }

  // find the root equiment from any equipment, return $this is the eq is a root
  public function getRoot()
  {
     log::add(JEEIPXV3, 'debug', __METHOD__);
     $idroot = $this->getConfiguration('rootid',null);
     return ( $idroot==null) ? $this : eqLogic::byId($idroot);
  }

  // get root URL of the IPX800 finishing by a /
	public function getUrl() {
		$url = 'http://';
		if ($this->getConfiguration('username') != '') {
			$url .= $this->getConfiguration('username') . ':' . $this->getConfiguration('password') . '@';
		}
		$url .= $this->getConfiguration('ipaddr');
		if ($this->getConfiguration('port') != '') {
			$url .= ':' . $this->getConfiguration('port');
		}
    return $url."/";
	}

  // returns XML object read from the remote IPX url. 
  // only works on action that produces xml output
  // throws exception if it fails
  public function ipxHttpCallXML($action) {
    $url = $this->getUrl() . $action;
    log::add(JEEIPXV3, 'debug', __METHOD__ .' url:'.$url);

    $result = simplexml_load_file($url); 
    if ($result===false) {
      log::add(JEEIPXV3, 'warning', __METHOD__ .' simplexml_load_file returned false');
      $this->checkAndUpdateCmd('status', 0);
      throw new Exception(__('IPX ne répond pas', __FILE__));
    }
    $this->checkAndUpdateCmd('status', 1);
    log::add(JEEIPXV3, 'debug', __METHOD__ .' simplexml_load_file returned:'.json_encode($result)); 
    return $result;
  }

  // generates if needed a 32chars API key 
  // ( default is more but it is too long for push URL in IPC )
  private static function genShortKey() {
    log::add(JEEIPXV3, 'debug', __METHOD__ );
    $key = jeedom::getApiKey(JEEIPXV3);
    if (strlen($key)>32) {
      $key = config::genKey($_car=32);															 																							 
      $save = config::save('api', $key, JEEIPXV3);		
      log::add(JEEIPXV3, 'debug', __METHOD__ . ' new 32 key:' . $NewsKey);									 
    }
    return $key;
  }

  // configures the push URL on the IPC
  // throws exception if it fails
	public function configPush() {
    log::add(JEEIPXV3, 'debug', __METHOD__ );
    $jeedomip = config::byKey("internalAddr");
    $jeedomport = config::byKey("internalPort");
    $ipxurl = $this->getUrl();

    $url =  $ipxurl . sprintf("protect/settings/push3.htm?channel=65&server=%s&port=%s&pass=user:pass&enph=1",$jeedomip,$jeedomport);
    log::add(JEEIPXV3, 'debug', __METHOD__ . ' calling 1 ' . $url);
    $result = file_get_contents($url);
		if ($result === false) {
      log::add(JEEIPXV3, 'error', __METHOD__ .' IPX does not respond, url:'.$url);
      throw new Exception('L\'ipx ne repond pas.');
    }

    $data='I=$I&O=$O&A=$A'; //'mac=$M&I=$I&O=$O&A=$A';
    $callbackurl = sprintf("/core/api/jeeApi.php?apikey=%s&type=event&plugin=jeeipxv3&id=%s&%s",
      self::genShortKey(),
      $this->getId(),
      $data
    );
  
    log::add(JEEIPXV3, 'debug', __METHOD__ . ' callback url ' . $callbackurl); 
    $url =  $ipxurl . sprintf("protect/settings/push3.htm?channel=65&cmd1=%s", urlencode($callbackurl) );
    log::add(JEEIPXV3, 'debug', __METHOD__ . ' calling 2 ' . $url);
    $result = file_get_contents($url);
		if ($result === false) {
      log::add(JEEIPXV3, 'error', __METHOD__ .' IPX does not respond, url:'.$url);
      throw new Exception('L\'ipx ne repond pas.');
    }
	}

  // callback push from IPX
  // http://192.168.0.9/core/api/jeeApi.php?apikey=xxxx&type=event&plugin=jeeipxv3&id=3912&mac=$M&I=$I&O=$O&A=$A
  public function event() 
  {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' eqlogic id:'.init('id'));
    log::add(JEEIPXV3, 'debug', __METHOD__ .' O:'.init('O'));
    log::add(JEEIPXV3, 'debug', __METHOD__ .' $_GET:'.json_encode($_GET));
    log::add(JEEIPXV3, 'debug', __METHOD__ .' $_POST:'.json_encode($_POST));
    log::add(JEEIPXV3, 'debug', __METHOD__ .' $_REQUEST:'.json_encode($_REQUEST));

    $eqLogicId = init('id');
    $eqLogic = self::byId( $eqLogicId , JEEIPXV3);

    if (is_object($eqLogic)) {
      $outArray = init('O');
      $len = strlen($outArray);
      for ($i = 0; $i < $len; $i++) {
        $eqLogic->updateChild( 'led' . $i , (int)$outArray[$i]);
      }
    } else {
      log::add(JEEIPXV3, 'warning', __METHOD__ .' received events on unknown EQlogic id:' . $eqLogicId);
    }
     /*
http://192.168.0.9/core/api/jeeApi.php?apikey=xxxx&type=event&plugin=jeeipxv3&id=2597&toto=titi
http://192.168.0.9/core/api/jeeApi.php?apikey=xxxxx&type=event&plugin=jeeipxv3&id=3912&mac=$M&I=$I&O=$O&A=$A
server: 192.168.0.17 port:3480
/data_request?id=lr_IPX800_Handler&mac=$M&deviceID=71&I=$I&O=$O&A=$A
0055|[2023-04-20 23:56:30]DEBUG : jeeipxv3::event $_GET:{"apikey":"xxx","type":"event","plugin":"jeeipxv3","id":"3912","I":"00000000000000000000000000000000","O":"00000000000000000000000000000000","A":"183","190":"","0":""}
0056|[2023-04-20 23:56:30]DEBUG : jeeipxv3::event $_POST:[]
0057|[2023-04-20 23:56:30]DEBUG : jeeipxv3::event $_REQUEST:{"apikey":"xxx","type":"event","plugin":"jeeipxv3","id":"3912","I":"00000000000000000000000000000000","O":"00000000000000000000000000000000","A":"183","190":"","0":""}
     */
  }
  
  // find and update a child EQLogic with a value received from the IPX
  public function updateChild($child, int $value) {
    log::add(JEEIPXV3, 'debug', __METHOD__ .sprintf(" name:'%s' value:%s",$child,$value));
    $eqLogic = self::byLogicalId( $this->getChildID($child) , JEEIPXV3);
    if (is_object($eqLogic)) {
      $eqLogic->checkAndUpdateCmd('status', $value);
    } else {
      log::add(JEEIPXV3, 'debug', __METHOD__ .' did found the eqlogic for child:' .$child);
    }
  }

  // call IPX and refresh all configured child EQuipments
  public function refreshFromIPX() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());

    if (($this->getIsEnable() == 1) && ($this->getConfiguration('ipaddr')!='')) {
      $xml = $this->ipxHttpCallXML('globalstatus.xml');    
      $this->checkAndUpdateCmd('updatetime', time());
      $this->checkAndUpdateCmd('version', (string) $xml->version ); // have to cast to string
      $this->checkAndUpdateCmd('mac', (string) $xml->config_mac );  // have to cast to string
      $this->checkAndUpdateCmd('lastxml', json_encode($xml) );

      foreach( self::$ipxDevices as $key => $value ) {
        for( $i=$value[0] ; $i<=$value[1]; $i++) {
          $child = $key.$i;
          // if the EQLogic is supposed to be here, then try to update it
          if ( $this->getConfiguration($child,0) == 1) {
            $ipxval = $xml->xpath( $child  )[0];
            $this->updateChild( $child  , (int)$ipxval);
          }
        }
      };
      return $xml;
    }
    return null;
  }

  public function readConfigurationFromIPX() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());  
    if ( $this->getConfiguration('led0',0) == 1) {
      $this->createOrUpdateChildEQ( 'light', 'led', 'led0', $this->getIsEnable() , $this->getIsVisible());
    } else {
      $this->removeChildEQ( 'led0' );
    }
    return; 
  }

  public function createOrUpdateChildEQ($category,$type,$child,$enable=0,$visible=0) {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
    //$child = ;
    $eqLogic = self::byLogicalId( $this->getChildID($child) , JEEIPXV3);

    if (!is_object($eqLogic)) {
       log::add(JEEIPXV3, 'info', __METHOD__.sprintf(' create for child:%s',$child));
       $eqLogic = new jeeipxv3();
       $eqLogic->setEqType_name(JEEIPXV3);
       $eqLogic->setLogicalId( $this->getChildID($child) );
       $eqLogic->setConfiguration('type', $type);
       $eqLogic->setConfiguration('rootid', $this->getId());
       $eqLogic->setIsEnable($enable);
       $eqLogic->setIsVisible($visible);
       $eqLogic->setCategory( $category ,'1');
       $eqLogic->setObject_id($this->getObject_id());  // same parent as root parent
       $eqLogic->setName( $this->getName() . "_" . $child );
       $eqLogic->save(); 
    }
    else {
       // todo : if object is not new, try not to change its parent ID
       // but should we verify that the old parent id is still a valid object ???
       //$eqLogic->setObject_id($this->getObject_id());  // same parent as root parent
    }
  }

  public function removeChildEQ( $child ) {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
    $eqLogic = self::byLogicalId( $this->getChildID($child) , JEEIPXV3);
    if (is_object($eqLogic)) {
      $eqLogic->remove();
    }
  }

  public function createOrUpdateCommand( $name, $logicalid, $type, $subtype, $is_visible, $generic_type, $targetcmdid=NULL) {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' name:' . $name);
    $cmd = $this->getCmd(null, $logicalid);
    if (!is_object($cmd)) {
      $cmd = new jeeipxv3Cmd();
      $cmd->setName($name);
      $cmd->setEqLogic_id($this->getId());
      $cmd->setType($type);
      $cmd->setSubType($subtype);
      $cmd->setLogicalId($logicalid);
      $cmd->setIsVisible($is_visible);
      //$cmd->setDisplay('generic_type', $generic_type);
      $cmd->setGeneric_type($generic_type);
      if (!is_null($targetcmdid)) {
        $cmd->setValue( (int) $targetcmdid );
      } 
      // $cmd->setUnite('');
      // $cmd->setIsHistorized(0);
      $cmd->save();
    } else {
      if ($cmd->getDisplay('generic_type') == "") {
        $cmd->setDisplay('generic_type', $generic_type);
        $cmd->save();
      }
    }
    return $cmd;
  }

  /*     * **********************Getteur Setteur*************************** */

}


class jeeipxv3Cmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' options:' . json_encode($_options));
    $eqLogic = $this->getEqLogic(); //Récupération de l’eqLogic
    $root = $eqLogic->getRoot();
    $cmdid = $this->getLogicalId();
    log::add(JEEIPXV3, 'debug', __METHOD__ . sprintf(' root:%s eqlogic:%s cmd:%s',$root->getId(),$eqLogic->getId(), $cmdid));
    switch ($cmdid) {
      case 'configpush':
        $root->configPush();
        break;
    }
  }

  /*     * **********************Getteur Setteur*************************** */

}
