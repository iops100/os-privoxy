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


/**
 * Class Privoxy
 * @package OPNsense\Privoxy
 */
class General extends MasterModel
{
	private $privoxy_conf='/usr/local/opnsense/service/templates/OPNsense/Privoxy/config';
	private $privoxy_rc_conf='/etc/rc.conf.d/privoxy';
    /**
     * check if module is enabled
     * @return bool is the Privoxy service enabled
     */
    public function isEnabled()
    {
        if ((string)$this->enabled === "1") {
            return true;
        }
        return false;
    }
    
    private function booleanNotMandatoryLine($field, $configWord)
    {
    	if($field==="1"){
    		return $configWord." ".$field;
    	}
    	return '';
    }
    
    private function textNotMandatoryLine($field, $configWord)
    {
    	if($field==""){
    		return '';
    	}
    	return $configWord." ".$field;
    }
    
    private function confDirLine()
    {
    	/*if (!empty((string)$this->confdir)) {
    		return 'confdir '.(string)$this->confdir;
    	}*/
    	return 'confdir /usr/local/etc/privoxy';
    }
    
    private function logDirLine()
    {
    	if (!empty((string)$this->logdir)) {
    		return 'logdir '.(string)$this->logdir;
    	}
    	return 'logdir /var/log/privoxy';
    }
    
    private function logFileLine()
    {
    	if (!empty((string)$this->logfile)) {
    		return 'logfile '.(string)$this->logfile;
    	}
    	return 'logfile logfile';
    }
    
    private function miscFilesLine()
    {
    	$mdlCl=new MiscFiles();
    	return $mdlCl->createMiscFilesRules();
    }
    
    private function listenInterfaceLines(){
    	foreach ( $this->listenAddress->getFlatNodes() as $item ) {
    		foreach($item->getNodeData() as $interface){
    			if($interface['selected']==1){
    				return "listen-address " . $this->getInterfaceIP(array($interface)) . ":". $this->listenPort;
    			}
    		}
    	}
    	
    	if((string)$this->listenLocalhost === "1"){
    		return "listen-address 127.0.0.1:". $this->listenPort;
    	}
    	throw new \Exception("You need to select a listen interface (including localhost)");
    }
    
    private function toggleLine()
    {
    	return 'toggle '.(string)$this->toggle;
    }
    
    private function enableRemoteToggleLine()
    {
    	return 'enable-remote-toggle '.(string)$this->enableRemoteToggle;
    }
    private function enableRemoteHttpToggleLine()
    {
    	return 'enable-remote-http-toggle '.(string)$this->enableRemoteHttpToggle;
    }
    private function enableEditActionsLine()
    {
    	return 'enable-edit-actions '.(string)$this->enableEditActions;
    }
    private function enforceBlocksLine()
    {
    	return 'enforce-blocks '.(string)$this->enforceBlocks;
    }
    private function bufferLimitLine()
    {
    	return 'buffer-limit '.(string)$this->bufferLimit;
    }
    private function enableProxyAuthenticationForwardingLine()
    {
    	return 'enable-proxy-authentication-forwarding '.(string)$this->enableProxyAuthenticationForwarding;
    }
    private function forwardedConnectRetriesLine()
    {
    	return 'forwarded-connect-retries '.(string)$this->forwardedConnectRetries;
    }
    private function acceptInterceptedRequestsLine()
    {
    	return 'accept-intercepted-requests '.(string)$this->acceptInterceptedRequests;
    }
    private function allowCgiRequestCrunchingLine()
    {
    	return 'allow-cgi-request-crunching '.(string)$this->allowCgiRequestCrunching;
    }
    private function splitLargeFormsLine()
    {
    	return 'split-large-forms '.(string)$this->splitLargeForms;
    }
    private function keepAliveTimeoutLine()
    {
    	return 'keep-alive-timeout '.(string)$this->keepAliveTimeout;
    }
    private function toleratePipeliningLine()
    {
    	return 'tolerate-pipelining '.(string)$this->toleratePipelining;
    }
    private function defaultServerTimeoutLine()
    {
    	$value=(string)$this->defaultServerTimeout;
    	if($value==''){
    		return '';
    	}
    	return 'default-server-timeout '.(string)$this->defaultServerTimeout;
    }
    private function socketTimeoutLine()
    {
    	return 'socket-timeout '.(string)$this->socketTimeout;
    }
    private function trustInfoUrlLine()
    {
    	return $this->textNotMandatoryLine((string)$this->trustInfoUrl, 'trust-info-url');
    }
    private function adminAddressLine()
    {
    	return $this->textNotMandatoryLine((string)$this->adminAddress, 'admin-address');
    }
    private function proxyInfoUrlLine()
    {
    	return $this->textNotMandatoryLine((string)$this->proxyInfoUrl, 'proxy-info-url');
    }
    private function templdirLine()
    {
    	return $this->textNotMandatoryLine((string)$this->templdir, 'templdir');
    }
    private function temporaryDirectoryLine()
    {
    	return $this->textNotMandatoryLine((string)$this->temporaryDirectory, 'temporary-directory');
    }
    private function singleThreadedLine()
    {
    	return $this->booleanNotMandatoryLine((string)$this->singleThreaded, 'single-threaded');
    }
    private function hostnameLine()
    {
    	return $this->textNotMandatoryLine((string)$this->hostname, 'hostname');
    }
    private function connectionSharingLine()
    {
    	return $this->booleanNotMandatoryLine((string)$this->connectionSharing, 'connection-sharing');
    }
    private function maxClientConnectionsLine()
    {
    	return $this->textNotMandatoryLine((string)$this->maxClientConnections, 'max-client-connections');
    }
    private function handleAsEmptyDocReturnsOkLine()
    {
    	return $this->booleanNotMandatoryLine((string)$this->handleAsEmptyDocReturnsOk, 'handle-as-empty-doc-returns-ok');
    }
    private function compressionLine()
    {
    	$line='';
    	if((string)$this->enableCompression==="1"){
    		$line="enable-compression 1\n";
    		if(!empty((string)$this->compressionLevel)){
    			$line.="compression-level ".(string)$this->compressionLevel;
    		} else {
    			$line.="compression-level 0";
    		}
    		
    	}
    	return $line;
    }
    private function clientHeaderOrderLine()
    {
    	return $this->textNotMandatoryLine((string)$this->clientHeaderOrder, 'client-header-order');
    }
    private function userParamsLine()
    {
    	if (!empty((string)$this->userParams)) {
    		return (string)$this->userParams;
    	}
    	return '';
    }
    
    private function debugLevelLine()
    {
    	$listedebugline="";
    	if (!empty((string)$this->debugLevel)) {
    		$liste_debug=explode(",",(string)$this->debugLevel);
    		if(is_array($liste_debug)){
    			foreach($liste_debug as $debug){
    				$listedebugline.=str_replace("debug","debug ",$debug)."\n";
    			}
    			return $listedebugline;
    		}
    	}
    	return $listedebugline;
    }
    
    private function forwardsListLines(){
    	$mdlCl=new Forward();
    	return $mdlCl->createForwardsRules();
    }
    
    
    public function generatePrivoxyConf() {
    	if(! $this->isEnabled()){
    		return $this;
    	}
    	
    	$privoxy_conf_file = <<< EOF
{$this->confDirLine()}
{$this->logDirLine()}
{$this->logFileLine()}
{$this->miscFilesLine()}
{$this->listenInterfaceLines()}
{$this->toggleLine()}
{$this->enableRemoteToggleLine()}
{$this->enableRemoteHttpToggleLine()}
{$this->enableEditActionsLine()}
{$this->enforceBlocksLine()}
{$this->bufferLimitLine()}
{$this->enableProxyAuthenticationForwardingLine()}
{$this->forwardedConnectRetriesLine()}
{$this->acceptInterceptedRequestsLine()}
{$this->allowCgiRequestCrunchingLine()}
{$this->splitLargeFormsLine()}
{$this->keepAliveTimeoutLine()}
{$this->toleratePipeliningLine()}
{$this->defaultServerTimeoutLine()}
{$this->socketTimeoutLine()}

{$this->debugLevelLine()}

{$this->forwardsListLines()}

#User Params
{$this->userParamsLine()}

#Not Mandatory options
{$this->trustInfoUrlLine()}
{$this->adminAddressLine()}
{$this->proxyInfoUrlLine()}
{$this->templdirLine()}
{$this->temporaryDirectoryLine()}
{$this->singleThreadedLine()}
{$this->hostnameLine()}
{$this->connectionSharingLine()}
{$this->maxClientConnectionsLine()}
{$this->handleAsEmptyDocReturnsOkLine()}
{$this->compressionLine()}
{$this->clientHeaderOrderLine()}
EOF;

		exec ( "/bin/cp -f " . $this->privoxy_conf . " " . $this->privoxy_conf . "_sav" );
		file_put_contents ( $this->privoxy_conf, strtr ( $privoxy_conf_file, array (
				"\r" => ""
		) ) );

    	return $this;
    }
    
}
