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

  // public static function cron15() {
  //   foreach (eqLogic::byType('kroomba', true) as $kroomba) {
  //     if ($kroomba->getConfiguration('type') == 'vigilance') {
  //       $kroomba->getVigilance();
  //       $kroomba->refreshWidget();
  //     }
  //     if ($kroomba->getConfiguration('type') == 'crue') {
  //       $kroomba->getCrue();
  //       $kroomba->refreshWidget();
  //     }
  //   }
  //   log::add('kroomba', 'debug', '15mn cron');
  // }

  // public static function cron5() {
  //   foreach (eqLogic::byType('kroomba', true) as $kroomba) {
  //     if ($kroomba->getConfiguration('type') == 'pluie1h') {
  //       $kroomba->getPluie();
  //       $kroomba->refreshWidget();
  //     }
  //   }
  //   log::add('kroomba', 'debug', '5mn cron');
  // }

  // public static function cronHourly() {
  //   foreach (eqLogic::byType('kroomba', true) as $kroomba) {
  //     if ($kroomba->getConfiguration('type') == 'maree') {
  //       $kroomba->getMaree();
  //       $kroomba->refreshWidget();
  //     }
  //     if ($kroomba->getConfiguration('type') == 'air') {
  //       $kroomba->getAir();
  //       $kroomba->refreshWidget();
  //     }
  //     if ($kroomba->getConfiguration('type') == 'seisme') {
  //       $kroomba->getSeisme();
  //       $kroomba->refreshWidget();
  //     }
  //   }
  //   log::add('kroomba', 'debug', 'Hourly cron');
  // }

  // public static function cronDaily() {
  //   foreach (eqLogic::byType('kroomba', true) as $kroomba) {
  //     foreach ($kroomba->getCmd() as $cmd) {
  //       $cmd->setConfiguration('alert', '0');
  //       $cmd->save();
  //     }
  //   }
  // }

  public function preSave() {
  }
  public function postSave() {
    // if ($this->getConfiguration('roomba_ip') == '') {
    //   $roomba_ip = $this->discover();
    //   $this->setConfiguration('roomba_ip', $roomba_ip);
    //   //$this->save();
    // }
    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'status');
    if (!is_object($cmdlogic)) {
      $cmdlogic = new kroombaCmd();
      $cmdlogic->setName(__('Status', __FILE__));
      $cmdlogic->setEqLogic_id($this->getId());
      $cmdlogic->setLogicalId('status');
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

    // $this->status();
    $this->mission();
    $this->sys();
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

  // public function status() {
  //   $node_path = realpath(dirname(__FILE__) . '/../../node');
  //   $cmd = 'cd ' . $node_path . ' && node mission.js';
  //   log::add('kroomba', 'debug', 'Lancement mission : ' . $cmd);
  //   exec($cmd . ' 2>&1',$result);
  //   log::add('kroomba_node', 'debug', 'Résultat : ' . implode($result));
  //   $result = json_decode(implode($result),true)['ok']['phase'];
  //
  //   $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'status');
  //   $cmdlogic->setConfiguration('value', $result);
  //   $cmdlogic->save();
  //   $cmdlogic->event($result);
  //   return ;
  // }

  // public function discover() {
  //   $node_path = realpath(dirname(__FILE__) . '/../../node');
  //   $cmd = 'cd ' . $node_path . ' && node discover.js';
  //   log::add('kroomba', 'debug', 'Découverte des roombas : ' . $cmd);
  //   exec($cmd . ' 2>&1',$result);
  //   log::add('kroomba','debug','Résultat0 :' . $result[0]);
  //   $roomba_ip = preg_match('/IP:(\d+\.\d+\.\d+\.\d+)/',$result[0],$matches);
  //   log::add('kroomba','debug','Résultat1 :' . $matches[1]);
  //   return $matches[1];
  // }

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
    $cmdlogic->setConfiguration('value', implode($result));
    $cmdlogic->save();
    $cmdlogic->event(implode($result));

    $result = json_decode(implode($result),true)['ok']['phase'];
    $cmdlogic = kroombaCmd::byEqLogicIdAndLogicalId($this->getId(),'status');
    $cmdlogic->setConfiguration('value', $result);
    $cmdlogic->save();
    $cmdlogic->event($result);
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
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
      return $replace;
    }
    $version = jeedom::versionAlias($_version);
    if ($this->getDisplay('hideOn' . $version) == 1) {
      return '';
    }

    $replace['#kroomba_ip#'] = $this->getConfiguration('roomba_ip','');
            log::add('kroomba', 'debug', '2');
    return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $_version, 'kroomba', 'kroomba')));
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
		}
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'stop') {
			$eqLogic->stop();
		}
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'dock') {
			$eqLogic->dock();
		}
		if ($this->getLogicalId() == 'sys') {
			$eqLogic->sys();
		}
  }
}

?>
