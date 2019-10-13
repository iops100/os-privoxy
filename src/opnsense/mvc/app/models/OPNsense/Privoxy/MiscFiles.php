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
class MiscFiles extends MasterModel {

	private function retrieveRuleType(
			$listtypes) {
		$ruleType = "";
		foreach ( $listtypes as $type => $data ) {
			if ($data ['selected'] == 1) {
				switch (strtolower ( $type )) {
					case 'filter' :
						$ruleType = 'filterfile';
						break;
					case 'actions' :
						$ruleType = 'actionsfile';
						break;
					case 'trust' :
						$ruleType = 'trustfile';
						break;
				}
			}
		}
		return $ruleType;
	}

	public function createMiscFilesRules() {
		$miscfilerules = "\n#Files List\n";
		foreach ( $this->miscfiles->miscfile->getChildren () as $alias ) {
			$node = $alias->getNodes ();
			//throw new \Exception ( print_r ( $node, true ) );
			if (! is_null ( $node )) {
				$miscfilerules .= $this->retrieveRuleType ( $node ['ruleType'] ) . " ";
				$miscfilerules .= $node ['targetFile'] . " ";
				$miscfilerules .= "# " . $node ['comment'] . "\n";
			}
		}
		return $miscfilerules;
	}
}
