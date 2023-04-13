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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
  function jeeipxv3_install() {
    log::add('jeeipxv3', 'debug', __METHOD__);

    //
    // Create & start the deamon
    //
    $cron = cron::byClassAndFunction('jeeipxv3', 'daemon');
    if (!is_object($cron)) {
      log::add('jeeipxv3', 'info', 'Create Cron');
      $cron = new cron();
      $cron->setClass('jeeipxv3');
      $cron->setFunction('daemon');
      $cron->setEnable(1);
      $cron->setDeamon(1);
      $cron->setTimeout(1440);
      $cron->setSchedule('* * * * *');
      $cron->save();
      $cron->start();
    }
  }

// Fonction exécutée automatiquement après la mise à jour du plugin
  function jeeipxv3_update() {
    log::add('jeeipxv3', 'debug', __METHOD__);
    $cron = cron::byClassAndFunction('jeeipxv3', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
  }

// Fonction exécutée automatiquement après la suppression du plugin
  function jeeipxv3_remove() {
    log::add('jeeipxv3', 'debug', __METHOD__);
    //
    // Stop the deamon
    //
    $cron = cron::byClassAndFunction('jeeipxv3', 'daemon');
    if (is_object($cron)) {
        log::add('jeeipxv3', 'info', 'Stopping and removing Cron');
        $cron->halt();
        $cron->remove();
    }
  }

?>
