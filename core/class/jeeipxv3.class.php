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

  //
  // TODO:  implement the refresh
  //
  foreach (self::byType(JEEIPXV3) as $eqLogic) {
    $eqLogic->refreshFromIPX();
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
  $return['log'] = '';
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
    if (is_null($type)) {
      $this->createOrUpdateCommands();
      $this->readConfigurationFromIPX();
    }
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());
    
    // TODO -- remove all child EQ
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

  public function getChildID($suffix) {
    return $this->getId()."_".$suffix;
  }

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

  public function refreshFromIPX() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());

    if (($this->getIsEnable() == 1) && ($this->getConfiguration('ipaddr')!='')) {
      $xml = $this->ipxHttpCallXML('globalstatus.xml');    
      $this->checkAndUpdateCmd('updatetime', time());
      $this->checkAndUpdateCmd('version', (string) $xml->version ); // have to cast to string
      $this->checkAndUpdateCmd('mac', (string) $xml->config_mac );  // have to cast to string
      $this->checkAndUpdateCmd('lastxml', json_encode($xml) );
      return $xml;
    }
    return null;
  }

  public function readConfigurationFromIPX() {
    log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());  
    $this->createOrUpdateChildEQ( 'light', 'led', 'led0' );
    return; //$xml;
  }

  public function createOrUpdateCommands() {
    $this->createOrUpdateCommand( 'Etat', 'status', 'info', 'binary', 1, 'GENERIC_INFO' );
    $this->createOrUpdateCommand( 'Version', 'version', 'info', 'string', 1, 'GENERIC_INFO' );
    $this->createOrUpdateCommand( 'MAC', 'mac', 'info', 'string', 1, 'GENERIC_INFO' );
    $this->createOrUpdateCommand( 'Update Time', 'updatetime', 'info', 'string', 0, 'GENERIC_INFO' );
    $this->createOrUpdateCommand( 'Last XML', 'lastxml', 'info', 'string', 0, 'GENERIC_INFO' );
  }

  public function createOrUpdateChildEQ($category,$type,$child) {
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
       //$eqLogic->setConfiguration('ipaddr', $this->getConfiguration('ipaddr'));
       $eqLogic->setIsEnable(1);
       $eqLogic->setIsVisible(1);
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

  public function createOrUpdateCommand( $name, $logicalid, $type, $subtype, $is_visible, $generic_type) {
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
      $cmd->setDisplay('generic_type', $generic_type);
      // $cmd->setUnite('');
      // $cmd->setIsHistorized(0);
      $cmd->save();
    } else {
      if ($cmd->getDisplay('generic_type') == "") {
        $cmd->setDisplay('generic_type', $generic_type);
        $cmd->save();
      }
    }
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
  }

  /*     * **********************Getteur Setteur*************************** */

}
