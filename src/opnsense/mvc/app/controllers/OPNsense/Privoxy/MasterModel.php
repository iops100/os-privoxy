<?php
/**
 *    Copyright (C) 2018 Damien Vargas
 *    Copyright (C) 2017 Frank Wall
 *    Copyright (C) 2015 Deciso B.V.
 *
 *    All rights reserved.
 *
 *    Redistribution and use in source and binary forms, with or without
 *    modification, are permitted provided that the following conditions are met:
 *
 *    1. Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *    2. Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 *    THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 *    AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *    AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 *    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *    POSSIBILITY OF SUCH DAMAGE.
 *
 */
namespace OPNsense\Privoxy;

use OPNsense\Base\BaseModel;
use OPNsense\Core\Backend;
use \OPNsense\Core\Config;

/**
 * Class Privoxy
 * @package OPNsense\Privoxy
 */
abstract class MasterModel extends BaseModel
{
	private $system_interfaces = array();
	/**
	 * Create reference array with UUID and position in list
	 * @return array
	 */
	protected function prepareTableauRulePosition($nodes){
		$liste_uuid=array();
		foreach ($nodes->getChildren() as $alias) {
			$uuid=$alias->getAttributes()["uuid"];
			$node=$alias->getNodes();
			if(!is_null($node)){
				$liste_uuid[$uuid]=(string)$node["rulePosition"];
			}
		}
		asort($liste_uuid);
		return $liste_uuid;
	}
	
	protected function getPortLine($portValue,$separator="=") {
		if (! empty ( $portValue )) {
			return " port " . $separator." ". $portValue . " ";
		}
		return '';
	}
	
	function getLogstype($logsType) {
		if (! empty ( $logsType )) {
			return " log: " . $logsType . "\n";
		}
		return '';
	}
	
	function getProtocol($protocol) {
		if (! empty ( $protocol )) {
			return " protocol: " . $protocol . "\n";
		}
		return '';
	}
	
	protected function getInterfaceIP($opnsenseInterfacesList){
		if(is_array($opnsenseInterfacesList)) {
			foreach($opnsenseInterfacesList as $interface){
				if($interface['selected']==1){
					return $this->find_interface ( strtolower($interface["value"]) );
				}
			}
			throw new \Exception('No Interface selected');
		}
		throw new \Exception('Interfaces List not usable');
	}
	
	/**
	 * collect interface IP
	 * @return array interface mapping (raw interface to description)
	 */
	private function getInterfaceIPV4() {
		$intfmap = array();
		$config = Config::getInstance()->object();
		if ($config->interfaces->count() > 0) {
			foreach ($config->interfaces->children() as $key => $node) {
				$nom = !empty((string)$node->descr) ? (string)$node->descr : $key;
				$intfmap[strtolower($nom)] = (string)$node->ipaddr;
			}
		}
		$this->setSystemInterfaces($intfmap);
		return $this->getSystemInterfaces();
	}
	
	private function find_interface(
			$interface_name) {
				$interfaces=$this->getSystemInterfaces();
				if(empty($interfaces)){
					$interfaces=$this->getInterfaceIPV4();
				}
				if(isset($interfaces[$interface_name])){
					return $interfaces[$interface_name];
				}
				switch (strtolower($interface_name)) {
					case "lo0" :
					case "loopback" :
					case "localhost" :
						return "127.0.0.1";
				}
				return $interface_name;
	}
	
	public function getSystemInterfaces(){
		return $this->system_interfaces;
	}
	
	public function setSystemInterfaces($interfaces){
		$this->system_interfaces=$interfaces;
		return $this;
	}
	
	/**
	 * get configuration state
	 * @return bool
	 */
	public function configChanged()
	{
		return file_exists("/tmp/privoxy.dirty");
	}
	
	/**
	 * mark configuration as changed
	 * @return bool
	 */
	public function configDirty()
	{
		return @touch("/tmp/privoxy.dirty");
	}
	
	/**
	 * mark configuration as consistent with the running config
	 * @return bool
	 */
	public function configClean()
	{
		return @unlink("/tmp/privoxy.dirty");
	}
}
