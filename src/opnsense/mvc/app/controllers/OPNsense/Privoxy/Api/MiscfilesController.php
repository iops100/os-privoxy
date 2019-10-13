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
namespace OPNsense\Privoxy\Api;

use \OPNsense\Base\ApiMutableModelControllerBase;
use \OPNsense\Base\UIModelGrid;
use \OPNsense\Core\Config;
use \OPNsense\Privoxy\MiscFiles;
use \OPNsense\Privoxy\General;

/**
 * Class MiscFilesController
 * @package OPNsense\Privoxy
 */
class MiscFilesController extends \OPNsense\Privoxy\MasterController
{
	static protected $internalModelName = 'miscfileslist';
	static protected $internalModelClass = '\OPNsense\Privoxy\MiscFiles';
	
	public function searchMiscFilesAction()
    {
    	return $this->searchBase('miscfiles.miscfile', array("ruleType", "targetFile","comment"),"ruleType");
    }
    
    public function getMiscFilesAction($uuid = NULL)
    {
    	return $this->getBase('miscfile', 'miscfiles.miscfile', $uuid);
    }
    
    public function addMiscFilesAction()
    {
    	$mdlServer = new General();
    	$mdlServer->configDirty();
    	return $this->addBase('miscfile', 'miscfiles.miscfile');
    }
    
    public function delMiscFilesAction($uuid)
    {
    	$mdlServer = new General();
    	$mdlServer->configDirty();
    	return $this->delBase('miscfiles.miscfile',$uuid);
    }
    
    public function setMiscFilesAction($uuid)
    {
    	$mdlServer = new General();
    	$mdlServer->configDirty();
    	return $this->setBase('miscfile', 'miscfiles.miscfile',$uuid);
    }
    
    public function toggleMiscFilesAction($uuid)
    {
    	$mdlServer = new General();
    	$mdlServer->configDirty();
    	return $this->toggleBase('miscfiles.miscfile', $uuid);
    }
}
