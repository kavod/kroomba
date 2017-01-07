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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class kroomba extends eqLogic {

  public static $_widgetPossibility = array('custom' => true);

  public static function cron5() {
    foreach (eqLogic::byType('kroomba', true) as $kroomba) {
      $kroomba->mission();
      $kroomba->refreshWidget();
    }
  }

  public function preSave() {
    log::add('kroomba', 'debug', 'preSaveBegin:getStatus Battery: ' . $this->getStatus('battery', -2));
    $this->mission();
    log::add('kroomba', 'debug', 'preSaveEnd:getStatus Battery: ' . $this->getStatus('battery', -2));
  }
  public function postSave() {
    log::add('kroomba', 'debug', 'postSaveBegin:getStatus Battery: ' . $this->getStatus('battery', -2));
    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'battery');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Battery', __FILE__));
      $cmdlogic->setEqLogic_id($this->getId());
      $cmdlogic->setLogicalId('battery');
		  $cmdlogic->setDisplay('generic_type', 'BATTERY');
    }
    $cmdlogic->setType('info');
    $cmdlogic->setSubType('numeric');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'status');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Status', __FILE__));
      $cmdlogic->setEqLogic_id($this->getId());
      $cmdlogic->setLogicalId('status');
		  $cmdlogic->setDisplay('generic_type', 'MODE_STATE');
    }
    $cmdlogic->setType('info');
    $cmdlogic->setSubType('string');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'mission');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Mission', __FILE__));
      $cmdlogic->setLogicalId('mission');
      $cmdlogic->setIsVisible(1);
		  $cmdlogic->setDisplay('generic_type', 'GENERIC_ACTION');
    }
    $cmdlogic->setType('action');
    $cmdlogic->setEqLogic_id($this->getId());
    $cmdlogic->setSubType('other');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'start');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Start', __FILE__));
      $cmdlogic->setLogicalId('start');
      $cmdlogic->setIsVisible(1);
		  $cmdlogic->setDisplay('generic_type', 'GENERIC_ACTION');
    }
    $cmdlogic->setType('action');
    $cmdlogic->setEqLogic_id($this->getId());
    $cmdlogic->setSubType('other');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'pause');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Pause', __FILE__));
      $cmdlogic->setLogicalId('pause');
      $cmdlogic->setIsVisible(1);
		  $cmdlogic->setDisplay('generic_type', 'GENERIC_ACTION');
    }
    $cmdlogic->setType('action');
    $cmdlogic->setEqLogic_id($this->getId());
    $cmdlogic->setSubType('other');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'resume');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Resume', __FILE__));
      $cmdlogic->setLogicalId('resume');
      $cmdlogic->setIsVisible(1);
		  $cmdlogic->setDisplay('generic_type', 'GENERIC_ACTION');
    }
    $cmdlogic->setType('action');
    $cmdlogic->setEqLogic_id($this->getId());
    $cmdlogic->setSubType('other');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'stop');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Stop', __FILE__));
      $cmdlogic->setLogicalId('stop');
      $cmdlogic->setIsVisible(1);
		  $cmdlogic->setDisplay('generic_type', 'GENERIC_ACTION');
    }
    $cmdlogic->setType('action');
    $cmdlogic->setEqLogic_id($this->getId());
    $cmdlogic->setSubType('other');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'dock');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Dock', __FILE__));
      $cmdlogic->setLogicalId('dock');
      $cmdlogic->setIsVisible(1);
		  $cmdlogic->setDisplay('generic_type', 'GENERIC_ACTION');
    }
    $cmdlogic->setType('action');
    $cmdlogic->setEqLogic_id($this->getId());
    $cmdlogic->setSubType('other');
    $cmdlogic->save();

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'sys');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Sys', __FILE__));
      $cmdlogic->setEqLogic_id($this->getId());
      $cmdlogic->setLogicalId('sys');
    }
    $cmdlogic->setIsVisible(0);
    $cmdlogic->setType('info');
    $cmdlogic->setSubType('string');
    $cmdlogic->save();

    $this->mission();
    $this->sys();
    log::add('kroomba', 'debug', 'postSaveEnd:getStatus Battery: ' . $this->getStatus('battery', -2));
  }

  public function sys() {
    $node_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'cd ' . $node_path . ' && node sys.js '
      . $this->getConfiguration('username','') . ' '
      . $this->getConfiguration('password','') . ' '
      . $this->getConfiguration('roomba_ip','');
    log::add('kroomba', 'debug', 'Lancement sys : ' . str_replace($this->getConfiguration('password',''),'****',$cmd));
    exec($cmd . ' 2>&1',$result);
    log::add('kroomba_node', 'debug', 'Résultat : ' . implode($result));

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'sys');
    $cmdlogic->setConfiguration('value', implode($result));
    $cmdlogic->save();
    $cmdlogic->event(implode($result));
    return ;
  }

  public function mission() {
    $node_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'cd ' . $node_path . ' && node mission.js '
      . $this->getConfiguration('username','') . ' '
      . $this->getConfiguration('password','') . ' '
      . $this->getConfiguration('roomba_ip','');
    log::add('kroomba', 'debug', 'Lancement mission : ' . str_replace($this->getConfiguration('password',''),'****',$cmd));
    exec($cmd . ' 2>&1',$result);
    log::add('kroomba_node', 'debug', 'Résultat : ' . implode($result));

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'mission');
    if (array_key_exists ('ok',json_decode(implode($result),true))) {
      $cmdlogic->setConfiguration('value', implode($result));
      $cmdlogic->save();
      $cmdlogic->event(implode($result));

      $phase = json_decode(implode($result),true)['ok']['phase'];
      $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'status');
      $cmdlogic->setConfiguration('value', $phase);
      $cmdlogic->save();
      $cmdlogic->event($phase);

      $battery = json_decode(implode($result),true)['ok']['batPct'];
      $this->batteryStatus($battery);
      $this->setStatus('battery', $battery);
      $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'battery');
      $cmdlogic->setConfiguration('value', $battery);
      $cmdlogic->save();
      $cmdlogic->event($battery);
      log::add('kroomba', 'debug', 'getStatus Battery: ' . $this->getStatus('battery', -2));
    } else {
      log::add('kroomba', 'debug', 'Wrong answer: ' . print_r(json_decode(implode($result),true),true));
    }
    $this->toHtml('mobile');
    $this->toHtml('dashboard');
    $this->refreshWidget();
    return ;
  }

  public function start() {
    $node_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'cd ' . $node_path . ' && node start.js '
      . $this->getConfiguration('username','') . ' '
      . $this->getConfiguration('password','') . ' '
      . $this->getConfiguration('roomba_ip','');
    log::add('kroomba', 'debug', 'Start : ' . str_replace($this->getConfiguration('password',''),'****',$cmd));
    exec($cmd . ' 2>&1',$result);
    return ;
  }

  public function pause() {
    $node_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'cd ' . $node_path . ' && node pause.js '
      . $this->getConfiguration('username','') . ' '
      . $this->getConfiguration('password','') . ' '
      . $this->getConfiguration('roomba_ip','');
    log::add('kroomba', 'debug', 'Pause : ' . str_replace($this->getConfiguration('password',''),'****',$cmd));
    exec($cmd . ' 2>&1',$result);
    return ;
  }

  public function resume() {
    $node_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'cd ' . $node_path . ' && node resume.js '
      . $this->getConfiguration('username','') . ' '
      . $this->getConfiguration('password','') . ' '
      . $this->getConfiguration('roomba_ip','');
    log::add('kroomba', 'debug', 'Resume : ' . str_replace($this->getConfiguration('password',''),'****',$cmd));
    exec($cmd . ' 2>&1',$result);
    return ;
  }

  public function stop() {
    $node_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'cd ' . $node_path . ' && node stop.js '
      . $this->getConfiguration('username','') . ' '
      . $this->getConfiguration('password','') . ' '
      . $this->getConfiguration('roomba_ip','');
    log::add('kroomba', 'debug', 'Stop : ' . str_replace($this->getConfiguration('password',''),'****',$cmd));
    exec($cmd . ' 2>&1',$result);
    return ;
  }

  public function dock() {
    $node_path = realpath(dirname(__FILE__) . '/../../node');
    $cmd = 'cd ' . $node_path . ' && node dock.js '
      . $this->getConfiguration('username','') . ' '
      . $this->getConfiguration('password','') . ' '
      . $this->getConfiguration('roomba_ip','');
    log::add('kroomba', 'debug', 'Dock : ' . str_replace($this->getConfiguration('password',''),'****',$cmd));
    exec($cmd . ' 2>&1',$result);
    return ;
  }

  public static function dependancy_info() {
    $return = array();
    $return['log'] = 'kroomba_dep';
    $request = realpath(dirname(__FILE__) . '/../../node/node_modules/dorita980');
    $return['progress_file'] = '/tmp/kroomba_dep';
    if (is_dir($request)) {
      $return['state'] = 'ok';
    } else {
      $return['state'] = 'nok';
    }
    return $return;
  }

  public static function dependancy_install() {
    log::add('kroomba','info','Installation des dépéndances nodejs');
    $resource_path = realpath(dirname(__FILE__) . '/../../resources');
    passthru('/bin/bash ' . $resource_path . '/nodejs.sh ' . $resource_path
      . ' > ' . log::getPathToLog('kroomba_dep') . ' 2>&1 &');
  }

  public function toHtml($_version = 'dashboard') {
    $parameters = $this->getDisplay('parameters');
    if (is_array($parameters)) {
        foreach ($parameters as $key => $value) {
            $replace['#' . $key . '#'] = $value;
        }
    }

    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
      return $replace;
    }
    $version = jeedom::versionAlias($_version);
    if ($this->getDisplay('hideOn' . $version) == 1) {
      return '';
    }
    $img_path = "plugins/kroomba/doc/images/kroomba_";

    $statusCmd = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'status');
    $status = $statusCmd->getConfiguration("value","unknown");
    $replace['#kroomba_ip#'] = $this->getConfiguration('roomba_ip','');
    $replace['#phase#'] = $status;
    switch($status)
    {
      case 'charge':
        $replace['#str_phase#'] = __('Charging', __FILE__);
        break;

      case 'home':
        $replace['#str_phase#'] = __('Returning to dock', __FILE__);
        break;

      case 'run':
        $replace['#str_phase#'] = __('Cleaning', __FILE__);
        break;

      case 'stop':
        $replace['#str_phase#'] = __('Stopped', __FILE__);
        break;

      case 'stuck':
        $replace['#str_phase#'] = __('Stucked !?!', __FILE__);
        break;

      case 'unknown':
      default:
        $replace['#str_phase#'] = __('Unknown: ', __FILE__).$status;
        $status = 'unknown';
        break;
    }
    $replace['#img_phase#'] = $img_path . $status . '.png';

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'mission');
    $replace['#refresh_id#'] = $cmdlogic->getId();
    $replace['#str_refresh#'] = __('Refresh', __FILE__);

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'start');
    $replace['#start_id#'] = $cmdlogic->getId();
    $replace['#str_start#'] = __('Start cleaning', __FILE__);

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'stop');
    $replace['#stop_id#'] = $cmdlogic->getId();
    $replace['#str_stop#'] = __('Stop cleaning', __FILE__);

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'dock');
    $replace['#dock_id#'] = $cmdlogic->getId();
    $replace['#str_dock#'] = __('Back to dock', __FILE__);

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'resume');
    $replace['#resume_id#'] = $cmdlogic->getId();
    $replace['#str_resume#'] = __('Resume cleaning', __FILE__);

    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'pause');
    $replace['#pause_id#'] = $cmdlogic->getId();
    $replace['#str_pause#'] = __('Pause cleaning', __FILE__);

    $vcolor = ($_version == 'mobile') ? 'mcmdColor' : 'cmdColor';
		if ($this->getPrimaryCategory() == '') {
			$replace['#cmdColor#'] = jeedom::getConfiguration('eqLogic:category:default:' . $vcolor);
		} else {
			$replace['#cmdColor#'] = jeedom::getConfiguration('eqLogic:category:' . $this->getPrimaryCategory() . ':' . $vcolor);
		}

    $html = $this->postToHtml($_version, template_replace($replace, getTemplate('core', $_version, 'kroomba', 'kroomba')));
    return $html;
  }
}

class kroombaCmd extends cmd {
	public static $_widgetPossibility = array('custom' => true);
  public function execute($_options = null) {
		if ($this->getType() == 'info') {
			return;
		}
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'mission') {
			$eqLogic->mission();
		}
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'start') {
			$eqLogic->start();
  		$eqLogic->mission();
		}
		if ($this->getLogicalId() == 'pause') {
			$eqLogic->pause();
  		$eqLogic->mission();
		}
		if ($this->getLogicalId() == 'resume') {
			$eqLogic->resume();
  		$eqLogic->mission();
		}
		if ($this->getLogicalId() == 'stop') {
			$eqLogic->stop();
  		$eqLogic->mission();
		}
		if ($this->getLogicalId() == 'dock') {
			$eqLogic->dock();
  		$eqLogic->mission();
		}
		if ($this->getLogicalId() == 'sys') {
			$eqLogic->sys();
		}
  }
}

?>
