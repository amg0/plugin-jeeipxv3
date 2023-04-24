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
	// prefix by underscore : https://community.jeedom.com/t/mysql-error-code-42s22-1054-unknown-column-utils-in-field-list/64274/6
	private static $_ipxDevices = array(
		"led" => array( 0, 31 ),    // min, max idx on IPX card
		"btn" => array( 0, 31 ),
		"analog" => array( 0, 15 )
	);

	private static $_ipxNamesMap = array(
		"led" => "output",
		"btn" => "input",
		"analog" => "analog",
		"count" => "counter"
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

		// do this only for update such that the initial parameters are set ( ipaddr etc ) and comm can happen with IPX
		$type = $this->getConfiguration('type',null);
		if (is_null($type)) {
			$this->updateConfigurationFromIPX();
		}
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
			case 'led':
			case 'btn': { // Relay & Input
				$cmdEtat = $this->createOrUpdateCommand( 'Etat', 'status', 'info', 'binary', 1, 'GENERIC_INFO' );
				$this->createOrUpdateCommand( 'On', $type.'_on', 'action', 'other', 1, 'LIGHT_ON', (int) $cmdEtat->getId() );
				$this->createOrUpdateCommand( 'Off', $type.'_off', 'action', 'other', 1, 'LIGHT_OFF', (int) $cmdEtat->getId() );
				break;
			}
			case 'analog': { // Analog
					$cmdEtat = $this->createOrUpdateCommand( 'Etat', 'status', 'info', 'numeric', 1, 'GENERIC_INFO' );
					break;
				}
			default: {  // Root Equipment
				$this->createOrUpdateCommand( 'Etat', 'status', 'info', 'binary', 1, 'ENERGY_STATE' );
				$this->createOrUpdateCommand( 'Version', 'version', 'info', 'string', 1, 'GENERIC_INFO' );
				$this->createOrUpdateCommand( 'MAC', 'mac', 'info', 'string', 1, 'GENERIC_INFO' );
				$this->createOrUpdateCommand( 'Update Time', 'updatetime', 'info', 'string', 0, 'GENERIC_INFO' );
				$this->createOrUpdateCommand( 'Last XML', 'lastxml', 'info', 'string', 0, 'GENERIC_INFO' );
				$this->createOrUpdateCommand( 'Config Push', 'configpush', 'action', 'other', 0, 'GENERIC_ACTION' );
				$this->createOrUpdateCommand( 'Refresh', 'refreshipx', 'action', 'other', 1, 'GENERIC_ACTION' );
				$this->createOrUpdateCommand( 'Reboot', 'reboot', 'action', 'other', 0, 'GENERIC_ACTION' );
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
	public function buildLogicalID($suffix) {
		return $this->getId()."_".$suffix;
	}

	public function splitLogicalID($lid){
		return preg_split('/_/',$lid);
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

	public function reboot() {
		log::add(JEEIPXV3, 'debug', __METHOD__ );
		$ipxurl = $this->getUrl();
		$url = $ipxurl . "protect/settings/reboot.htm";
		$result = file_get_contents($url);
		if ($result === false) {
			throw new Exception('L\'ipx ne repond pas.');
		}
		log::add(JEEIPXV3, 'debug', __METHOD__ .sprintf('url:%s returned:%s',$url,$result));
		return $result;
	}

	public function setIPXRelay($type,$child,int $value) {
		log::add(JEEIPXV3, 'debug', __METHOD__ . sprintf(' type:%s child:%s value:%s',$type,$child,$value));
		// keep only the numeric index of the child
		$num = (int)str_replace($type,'',$child);
		$ipxurl = $this->getUrl();
		if (($type=='led') && ($value==0)) {
			// sortie sans mode impulsionel
			$url = $ipxurl . sprintf("preset.htm?set%d=%d",$num+1,$value);
		} else {
			// sortie according to configuration of Tb inside IPX800 ( impulse or normal )
			if ($type=='btn')
				$num +=100;
			$url = $ipxurl . sprintf("leds.cgi?led=%d",$num);
		}
		//log::add(JEEIPXV3, 'warning', __METHOD__ .' simulated url:'.$url);
		$result = file_get_contents($url);
		if ($result === false) {
		  log::add(JEEIPXV3, 'error', __METHOD__ .' IPX does not respond, url:'.$url);
		  throw new Exception('L\'ipx ne repond pas.');
		}
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

		$data='mac=$M'; //'mac=$M&I=$I&O=$O&A=$A';
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
		//log::add(JEEIPXV3, 'debug', __METHOD__ .' eqlogic id:'.init('id'));
		log::add(JEEIPXV3, 'debug', __METHOD__ .' $_GET:'.json_encode($_GET));
		//log::add(JEEIPXV3, 'debug', __METHOD__ .' $_POST:'.json_encode($_POST));
		//log::add(JEEIPXV3, 'debug', __METHOD__ .' $_REQUEST:'.json_encode($_REQUEST));

		$eqLogicId = init('id');
		$eqLogic = self::byId( $eqLogicId , JEEIPXV3);
		$eqLogic->refreshFromIPX();

		/* DEBUG Tests
		http://192.168.0.9/core/api/jeeApi.php?apikey=xxxxx&type=event&plugin=jeeipxv3&id=3912&mac=$M&I=$I&O=$O&A=$A
		server: 192.168.0.17 port:3480
		/data_request?id=lr_IPX800_Handler&mac=$M&deviceID=71&I=$I&O=$O&A=$A
		*/
	}
	
	// find and update a child EQLogic with a value received from the IPX
	public function updateChild($child, float $value, int $antype=0 ) {
		log::add(JEEIPXV3, 'debug', __METHOD__ .sprintf(" name:'%s' value:%s anselect:%d",$child,$value,$antype));
		$eqLogic = self::byLogicalId( $this->buildLogicalID($child) , JEEIPXV3);
		if (is_object($eqLogic)) {
			if ( $eqLogic->getConfiguration('anselect',0)  != $antype) {
				$eqLogic->setConfiguration('anselect',$antype);
				log::add(JEEIPXV3, 'info', __METHOD__ .sprintf(" setting anselect of eq:%s to anselect=%d",$this->buildLogicalID($child),$antype));
				$eqLogic->save();
			}
			$unit='';
			switch ($antype) {
				case 1: 
				case 7:
					$value = $value * 0.00323;
					break;
				case 2:
					$value = $value * 0.323 - 50;
					break;
				case 3:
					$value = $value * 0.09775;         
					break;
				case 4:
					$value = $value * 0.00323;
					$value = ($value - 1.63) / 0.0326;
					break;  
				case 5:
					// --TODO humidity sensor so needs add hTemp correction but we do not know 
					// -- hTemp so let's take 15C as an average
					// -- GetAn	HCTemp	0	10	20	30	40		Delta
					// -- 0		0	0	0	0	0		
					// -- 10		9,482268159	9,68054211	9,887284952	10,10305112	10,32844454		0,846176378
					// -- 20		18,96453632	19,36108422	19,7745699	20,20610224	20,65688907		1,692352755
					// -- 30		28,44680448	29,04162633	29,66185485	30,30915336	30,98533361		2,538529133
					// -- 40		37,92907263	38,72216844	39,54913981	40,41220449	41,31377815		3,384705511
					$value = $value * 0.00323;
					$value = ($value/3.3 - 0.1515) / 0.00636;
					$HCtemp=15;
					$value = $value/ (1.0546 - (0.00216 * $HCtemp));
					break;
				case 6:
					$value = ($value * 0.00323 - 0.25) / 0.028;
					break;
				case 8:
					$value = $value * 0.00646;
					break;
				case 9:
					$value = $value * 0.01615;
					break;
				case 10;
					$value = $value / 100;
					break;
				case 11;
					$value = $value - 2500;
					break;
				case 0:
				default:
					break;
			}
			$eqLogic->checkAndUpdateCmd('status', round($value,1) );
		} else {
			log::add(JEEIPXV3, 'warning', __METHOD__ .' did not found the eqlogic for child:' .$child);
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

			foreach( self::$_ipxDevices as $key => $value ) {
				for( $i=$value[0] ; $i<=$value[1]; $i++) {
					$child = $key.$i;
					// if the EQLogic is supposed to be here, then try to update it
					if ( $this->getConfiguration($child,0) == 1) {
						$ipxval = $xml->xpath( $child  )[0];
						switch($ipxval) {
							case 'up':
								$ipxval = 0;
								break;
							case 'dn':
								$ipxval = 1;
								break;
						}

						$antype = ($key =='analog') ? (int) $xml->xpath( 'anselect'.$i )[0] : 0;
						$this->updateChild( $child  , (float)$ipxval, $antype );
					}
				}
			};
			return $xml;
		}
		return null;
	}

	public function updateConfigurationFromIPX() {
		log::add(JEEIPXV3, 'debug', __METHOD__ .' id:' . $this->getId());  
		$xml = $this->ipxHttpCallXML('ioname.xml');

		foreach( self::$_ipxDevices as $key => $value ) {
			for( $i=$value[0] ; $i<=$value[1]; $i++) {
				$child = $key.$i;
				if ( $this->getConfiguration($child,0) == 1) {
					$xpath = self::$_ipxNamesMap[$key] . ((int)$i+1);
					$name = $xml->xpath($xpath)[0];
					$this->createOrUpdateChildEQ( 'light', $key, $child, $this->getIsEnable() , $this->getIsVisible(), $name);
				} else {
					$this->removeChildEQ( $child );
				}
			}
		};
		return; 
	}

	public function createOrUpdateChildEQ($category,$type,$child,$enable=0,$visible=0,$name=null) {
		log::add(JEEIPXV3, 'debug', __METHOD__ .sprintf(' for root:%d child:%s',$this->getId(),$child));
		//$child = ;
		$eqLogic = self::byLogicalId( $this->buildLogicalID($child) , JEEIPXV3);

		if (!is_object($eqLogic)) {
			 log::add(JEEIPXV3, 'info', __METHOD__.sprintf(' create for child:%s',$child));
			 $eqLogic = new jeeipxv3();
			 $eqLogic->setEqType_name(JEEIPXV3);
			 $eqLogic->setLogicalId( $this->buildLogicalID($child) );
			 $eqLogic->setConfiguration('type', $type);
			 $eqLogic->setConfiguration('rootid', $this->getId());
			 $eqLogic->setIsEnable($enable);
			 $eqLogic->setIsVisible($visible);
			 $eqLogic->setCategory( $category ,'1');
			 $eqLogic->setObject_id($this->getObject_id());  // same parent as root parent
			 $eqLogic->setName( is_null($name) ? ($this->getName() . "_" . $child) : $name );
			 $eqLogic->save(); 
		}
		else {
			 // todo : if object is not new, try not to change its parent ID
			 // but should we verify that the old parent id is still a valid object ???
			 //$eqLogic->setObject_id($this->getObject_id());  // same parent as root parent
		}
	}

	public function removeChildEQ( $child ) {
		log::add(JEEIPXV3, 'debug', __METHOD__ .' root id:' . $this->getId() . ' child:' . $child);
		$eqLogic = self::byLogicalId( $this->buildLogicalID($child) , JEEIPXV3);
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
		$cmdid = $this->getLogicalId();
		log::add(JEEIPXV3, 'debug', __METHOD__ .' cmd:'. $cmdid .' options:' . json_encode($_options));
		$eqLogic = $this->getEqLogic(); //Récupération de l’eqLogic
		$root = $eqLogic->getRoot();
		
		log::add(JEEIPXV3, 'debug', __METHOD__ . sprintf(' root:%s eqlogic:%s cmd:%s',$root->getId(),$eqLogic->getId(), $cmdid));
		switch ($cmdid) {
			case 'configpush':
				$root->configPush();
				break;
			case 'reboot':
				$root->reboot();
				break;
			case 'refreshipx':
				$root->refreshFromIPX();
				break;
			case 'led_on':
				$type = 'led';
				$child = $root->splitLogicalID($eqLogic->getLogicalId())[1];  // return child
				$root->setIPXRelay($type,$child,1);
				break;
			case 'led_off':
				$type = 'led';
				$child = $root->splitLogicalID($eqLogic->getLogicalId())[1];  // return child
				$root->setIPXRelay($type,$child,0);
				break;
			case 'btn_on':
			case 'btn_off':
				$type = 'btn';
				$child = $root->splitLogicalID($eqLogic->getLogicalId())[1];  // return child
				$root->setIPXRelay($type,$child,0); // value does not matter in that case as we use the led.cgi command
				break;
			default:
			log::add(JEEIPXV3, 'info', __METHOD__ .' ignoring unknown command');
		}
	}

	/*     * **********************Getteur Setteur*************************** */

}
