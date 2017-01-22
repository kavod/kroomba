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

function myDiscover() {
  $result = array();
  log::add('kroomba', 'debug', 'myDiscover');
  $node_path = realpath(dirname(__FILE__) . '/../../node');
  $cmd = 'cd ' . $node_path . ' && node discover.js';
  log::add('kroomba', 'debug', 'Découverte des roombas : ' . $cmd);
  exec($cmd . ' 2>&1',$roombas);
  foreach ($roombas as $roomba) {
    log::add('kroomba','debug','Résultat :' . $roomba);
    preg_match('/IP:(\d+\.\d+\.\d+\.\d+),blid:(\d+)/',$roomba,$matches);
    log::add('kroomba','debug','ip :' . $matches[1]);
    log::add('kroomba','debug','blid :' . $matches[2]);
    $result[] = array(
      "ip"   =>  $matches[1],
      "blid" =>  $matches[2]
    );
  }
  return $result;
}

function getPassword($ip,$blid) {
  log::add('kroomba', 'debug', 'getPassword');
  $node_path = realpath(dirname(__FILE__) . '/../../node');
  $cmd = 'cd ' . $node_path . ' && node getPassword.js ' . $ip . ' ' . $blid;
  log::add('kroomba', 'debug', 'Getting password for ' . $ip . ' : ' . $cmd);
  exec($cmd . ' 2>&1',$password);
  log::add('kroomba', 'debug', 'Résultat: '.$password[0]. '('.count($password).')');
  if (count($password) > 1 || $password[0] == 'Error') {
    foreach($password as $line) {
      log::add('kroomba', 'debug', 'Error: '.$line);
    }
    log::add('kroomba', 'debug', 'Not found');
    return false;
  } else {
    preg_match('/Password:(\w+)/',$password[0],$matches);
    log::add('kroomba', 'debug', 'Found: '.$matches[1]);
    return $matches[1];
  }
}

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    log::add('kroomba', 'debug', 'Action : ' . init('action'));

    if (init('action') == 'getPassword') {
      $kroomba = eqLogic::byLogicalId( 'kroomba_'.init('blid'), 'kroomba', $_multiple = false);
      if ( !is_object($kroomba)) {
        ajax::error('Unknown kroomba: kroomba_'.init('blid'),400);
      } else {
        $password = getPassword(
          $kroomba->getConfiguration("roomba_ip",""),
          $kroomba->getConfiguration("username","")
        );
        if ($password) {
          ajax::success($password);
        } else {
          ajax::error('No signal from Roomba. Check instructions and IP',401);
        }
      }
    }

    if (init('action') == 'discover') {
      $roombas = myDiscover();
      foreach($roombas as $roomba)
      {
        $kroomba = eqLogic::byLogicalId( 'kroomba_'.$roomba['blid'], 'kroomba', $_multiple = false);
        if ( !is_object($kroomba)) {
          $kroomba = new kroomba();
          $kroomba->setName('Kroomba_'.$roomba['blid']);
      		$kroomba->setLogicalId('kroomba_'.$roomba['blid']);
      		$kroomba->setEqType_name('kroomba');
        }
        $kroomba->setConfiguration("roomba_ip",$roomba['ip']);
        $kroomba->setConfiguration("username",$roomba['blid']);
        $kroomba->setConfiguration('battery_type', 'undefined');
        $kroomba->save();
      }
      ajax::success();
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
