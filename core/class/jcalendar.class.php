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
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class jcalendar extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */  
    // Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function cron() {
      foreach (eqLogic::byType('jcalendar') as $jcalendar) {
        if ($jcalendar->getConfiguration('candleTimes', '') == 1) {
          log::add('jcalendar', 'debug', 'Récupération des données chaque minute car recherche contrainte électricité');
          $jcalendar->getjCalendarData();
        }
      }
    }
     
    
    // Fonction exécutée automatiquement toutes les heures par Jeedom
    public static function cronHourly() {
      foreach (eqLogic::byType('jcalendar') as $jcalendar) {
        if ($jcalendar->getConfiguration('candleTimes', '') != 1) {
          log::add('jcalendar', 'debug', 'Récupération des données une fois par jour car pas de recherche de contrainte électricité');
          $jcalendar->getjCalendarData();
        }
      }
    }

    
    // Fonction exécutée automatiquement tous les jours par Jeedom
    public static function cronDaily() {
      foreach (eqLogic::byType('jcalendar') as $jcalendar) {
        if ($jcalendar->getConfiguration('candleTimes', '') != 1) {
          log::add('jcalendar', 'debug', 'Récupération des données une fois par jour car pas de recherche de contrainte électricité');
          $jcalendar->getjCalendarData();
        }
      }
    }
     

    public function getjCalendarData() {
      // Récupérer la géolocalisation
      if ($this->getConfiguration('geoloc', 'none') == 'none') {
          return;
      }

      if ($this->getConfiguration('geoloc') == 'jeedom') {
          $geoloc_lat = config::byKey('info::latitude');
          $geoloc_long = config::byKey('info::longitude');
      } else {
          $geotrav = eqLogic::byId($this->getConfiguration('geoloc'));
          if (!(is_object($geotrav) && $geotrav->getEqType_name() == 'geotrav')) {
              return;
          }
          $geolocval = geotravCmd::byEqLogicIdAndLogicalId($this->getConfiguration('geoloc'),'location:coordinate')->execCmd();
          $geoloctab = explode(',', trim($geolocval));
          $geoloc_lat=$geoloctab[0];
          $geoloc_long=$geoloctab[1];
      }

      // Récupérer le nom et la configuration de l'équipement
      $cmd_list=array();

      if($this->getConfiguration('candleTimes', '') == 1) { 
        $candleTimes = 'on'; 
        $cmd_list[]='candles'; $cmd_list[]='havdalah';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('shabbat','Shabbat:','info','binary','0');
        $this->jcalendarCmdCreate('candles','Allumage bougies:','info','string','');
        $this->jcalendarCmdCreate('havdalah','Havdalah:','info','string','');
      } else { 
        $candleTimes = 'off';
      }

      switch($this->getConfiguration('hebrewDates', '')) {
        case "none":  $hebrewDates_string='';
                      break; 
        case "some":  $hebrewDates_string='&D=on&d=off';
                      $cmd_list[]='hebdate';
                      // creation de la commande liée à l'information
                      $this->jcalendarCmdCreate('hebdate','Date Hébraïque:','info','string');  
                      break;
        case "entire": $hebrewDates_string='&D=on&d=on';
                      $cmd_list[]='hebdate';
                      // creation de la commande liée à l'information
                      $this->jcalendarCmdCreate('hebdate','Date Hébraïque:','info','string');  
                      break;
      }

      if($this->getConfiguration('majorHoliday', '') == 1) { 
        $majorHoliday = 'on';
        $cmd_list[]='holiday major';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('holiday major','Vacances Majeures:','info','string');
      } else { 
        $majorHoliday = 'off';
      }

      if($this->getConfiguration('minorHoliday', '') == 1) { 
        $minorHoliday = 'on'; 
        $cmd_list[]='holiday minor';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('holiday minor','Vacances Mineures:','info','string');
      } else { 
        $minorHoliday = 'off'; 
      }
      
      if($this->getConfiguration('modernHoliday', '') == 1) { 
        $modernHoliday = 'on'; 
        $cmd_list[]='holiday modern';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('holiday modern','Vacances Modernes:','info','string');
      } else { 
        $modernHoliday = 'off'; 
      }
      
      if($this->getConfiguration('minorFests', '') == 1) { 
        $minorFests = 'on'; 
        $cmd_list[]='holiday fast';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('holiday fast','Vacances fêtes:','info','string');
      } else { 
        $minorFests = 'off'; 
      }

      if($this->getConfiguration('specialShabbatot', '') == 1) { 
        $specialShabbatot = 'on'; 
        $cmd_list[]='holiday shabbat';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('holiday shabbat','Vacances shabbat:','info','string');
      } else { 
        $specialShabbatot = 'off'; 
      }

      if($this->getConfiguration('parashatOnSaturday', '') == 1) { 
        $parashatOnSaturday = 'on'; 
        $cmd_list[]='parashat';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('parashat','Parashat le samedi:','info','string');
      } else { 
        $parashatOnSaturday = 'off'; 
      }

      if($this->getConfiguration('roshChodesh', '') == 1) { 
        $roshChodesh = 'on'; 
        $cmd_list[]='roshchodesh';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('roshchodesh','roshchodesh:','info','string');
      } else {  
        $roshChodesh = 'off'; 
      }

      if($this->getConfiguration('omerDays', '') == 1) { 
        $omerDays = 'on'; 
        $cmd_list[]='omer';
        // creation de la commande liée à l'information
        $this->jcalendarCmdCreate('omer','Jour Omer:','info','string');
      } else { 
        $omerDays = 'off'; 
      }

      if($this->getConfiguration('holidaysAndTorah', '') == 1) { $holidaysAndTorah = 'on'; } else { $holidaysAndTorah = 'off'; }

      $candleAfterSunrise=trim(config::byKey('jcalendar-candleAfterSunrise', 'jcalendar'));
      if( $candleAfterSunrise == '') { $candleAfterSunrise = 50; }

      $candleBeforeSunset=trim(config::byKey('jcalendar-candleBeforeSunset', 'jcalendar'));
      if( $candleBeforeSunset == '') { $candleBeforeSunset = 18; }

      $url=trim(config::byKey('jcalendar-url', 'jcalendar'));
      $language=trim(config::byKey('jcalendar-languages', 'jcalendar'));
      $month=date("n");

      // Construction de la requête http et récupération du fichier json
      $uri = $url.'/hebcal/?v=1&cfg=json&maj='.$majorHoliday.'&min='.$minorHoliday.'&mod='.$modernHoliday.'&nx='.$roshChodesh.'&year=now&month='.$month.'&ss='.$specialShabbatot.'&mf='.$minorFests.'&c='.$candleTimes.'&geo=pos&latitude='.$geoloc_lat.'&m='.$candleAfterSunrise.'&s='.$parashatOnSaturday.'&lg='.$language.'&longitude='.$geoloc_long.'&tzid='.date_default_timezone_get().'&b='.$candleBeforeSunset.$hebrewDates_string.'&o='.$omerDays;

      log::add('jcalendar', 'debug', '------------------------------------------------');
      log::add('jcalendar', 'debug', 'Appel : ' . $uri);
      log::add('jcalendar', 'debug', '------------------------------------------------');

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$uri);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $json_string = curl_exec ($ch);
      curl_close ($ch);

      $parsed_json = json_decode($json_string, true);
      $jcal_data = $parsed_json['items'];  

      foreach ($cmd_list as $the_jcmd) {
        $found=0;
        // Boucler sur chaque événement reçu afin de récupérer la valeur
        foreach ($jcal_data as $event) {
          // Si la catégorie ou sous-catégorie correspond à la commande, on prend la valeur 
          if ((($the_jcmd==$event['category']) || ($the_jcmd==$event['category'].' '.$event['subcat'])) && (substr_count($event['date'], date('Y-m-d')) > 0)) {
            $found=1;
            log::add('jcalendar', 'debug', '------------------------------------------------');
            log::add('jcalendar', 'debug', 'Evènement à enregistrer pour le ' . date('Y-m-d') . ' ' .$event['category']. ' ' .$event['subcat']);
            log::add('jcalendar', 'debug', '------------------------------------------------');
            log::add('jcalendar', 'debug', 'Titre : ' . $event['title']);
            log::add('jcalendar', 'debug', 'Date : ' . $event['date']);
            log::add('jcalendar', 'debug', 'Catégorie : ' . $event['category']);
            log::add('jcalendar', 'debug', 'Sous-catégorie : ' . $event['subcat']);
            log::add('jcalendar', 'debug', 'Titre original : ' . $event['title_orig']);
            log::add('jcalendar', 'debug', 'En hébreu : ' . $event['hebrew']);
            log::add('jcalendar', 'debug', '------------------------------------------------');

            $this->jcalendarCmdUpdate($event['title'],$event['category'],$event['subcat'],$event['title_orig'],$event['hebrew'],$event['date']); 
            break;
          } else {
            $found=0;
          }
        }
      
        if ($found==0) {
          log::add('jcalendar', 'debug', '------------------------------------------------');
            log::add('jcalendar', 'debug', 'Aucun evènement à enregistrer pour la commande ' .$the_jcmd);
            log::add('jcalendar', 'debug', '------------------------------------------------');
            $the_jcmdcat=explode(' ',$the_jcmd);
            $this->jcalendarCmdUpdate('',$the_jcmdcat[0],$the_jcmdcat[1],'','','');    
        }
      }
    }
    
    public function jcalendarCmdCreate($cmd_id,$cmd_name,$cmd_type,$cmd_subtype,$value) {
        $cmd = $this->getCmd(null,$cmd_id);
        if (!is_object($cmd)) {
          $cmd = new jcalendarCmd();
          $cmd->setLogicalId($cmd_id);
          $cmd->setIsVisible(1);
          $cmd->setName(__($cmd_name, __FILE__));
          $cmds = $this->getCmd();
          $order = count($cmds);
          $cmd->setOrder($order);
          $cmd->setType($cmd_type);
          $cmd->setSubType($cmd_subtype);
          $cmd->setEqLogic_id($this->getId());
          $cmd->save();
        }
    }

    public function jcalendarCmdUpdate($title,$category,$subcategory,$title_orig,$hebrew,$date) {
      // Gestion de début et fin de la constrainte électrique
      if (($category == 'candles') || ($category == 'havdalah')) {
        if ($date!='-') {
          // Création des commandes concenant l'heure du début de la contrainte et de fin.
          $date=substr($date, 0, 16);
          $dateTime=explode('T',$date);
          $cmdDate=$dateTime[0];
          $cmdTime=$dateTime[1];
          $formattedDateTime=$cmdDate . ' '.$cmdTime;

          log::add('jcalendar', 'debug', 'Date et heure nettoyée : ' . $formattedDateTime);

          $cmd = $this->getCmd(null,$category);
          $cmd->setEqLogic_id($this->getId());
          $cmd->save();
          $this->checkAndUpdateCmd($category,str_replace(':','',$cmdTime));
          $cmdId = $cmd->getId();

          log::add('jcalendar', 'debug', 'Création / Maj de la commande: ' . $category . ' avec la valeur ' . str_replace(':','',$cmdTime)); 

          // Détermination du statut de la contrainte en fonction de l'heure actuelle et la commande
          log::add('jcalendar', 'debug', 'Comparaison date/heure pour shabbat: Maintenant=' . time() . ' et ' . strtotime($formattedDateTime)); 
          if((time()) >= strtotime($formattedDateTime)) {
            if ($category == 'candles') { $state=1; }
            if ($category == 'havdalah') { $state=0; }
          }

          $cmd = $this->getCmd(null,'shabbat');
          $cmd->setEqLogic_id($this->getId());
          $cmd->save();
          if ($state!="") {
            $this->checkAndUpdateCmd('shabbat',$state);
            log::add('jcalendar', 'debug', 'Création / Maj de la commande shabbat avec la valeur ' . $state);
          }
          $cmdId = $cmd->getId();  
        }  
      } else {
        // Gestion des autres catégories
        $cmd = $this->getCmd(null,$category.' '.$subcategory);
        $cmd->setEqLogic_id($this->getId());
        $cmd->save();
        if($this->getConfiguration('hebrewDisplay', '') == 1) {
          $this->checkAndUpdateCmd($category.' '.$subcategory, $hebrew);
        } else {
          $this->checkAndUpdateCmd($category.' '.$subcategory, $title_orig);
        }
        $cmdId = $cmd->getId();  
        log::add('jcalendar', 'debug', 'Création / Maj de la commande: ' . $category . ' ' . $subcategory . ' avec la valeur ' . $title_orig); 
      }
    }

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
      $this->getjCalendarData();        
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
      $eqlogicCmd = jcalendarCmd::byEqLogicIdAndLogicalId($this->getId(),'refresh');
      if (!is_object($eqlogicCmd)) {
          $eqlogicCmd = new jcalendarCmd();
          $eqlogicCmd->setName(__('Rafraichir', __FILE__));
          $eqlogicCmd->setEqLogic_id($this->getId());
          $eqlogicCmd->setLogicalId('refresh');
          $eqlogicCmd->setType('action');
          $eqlogicCmd->setSubType('other');
          $eqlogicCmd->save();
      }


      $cmd_list=array();
      if ($this->getConfiguration('candleTimes') == 0) {
        $cmd_list[]='candles'; $cmd_list[]='havdalah'; $cmd_list[]='shabbat';
      }

      if ($this->getConfiguration('hebrewDates') == 'none') {
        $cmd_list[]='hebdate';
      }

      if ($this->getConfiguration('majorHoliday') == 0) {
        $cmd_list[]='holiday major';
      }

      if ($this->getConfiguration('minorHoliday') == 0) {
        $cmd_list[]='holiday minor';
      }

      if ($this->getConfiguration('modernHoliday') == 0) {
        $cmd_list[]='holiday modern';
      }

      if ($this->getConfiguration('minorFests') == 0) {
        $cmd_list[]='holiday fast';
      }

      if ($this->getConfiguration('specialShabbatot') == 0) {
        $cmd_list[]='holiday shabbat';
      }

      if ($this->getConfiguration('parashatOnSaturday') == 0) {
        $cmd_list[]='parashat';
      }

      if ($this->getConfiguration('roshChodesh') == 0) {
        $cmd_list[]='roshchodesh';
      }

      if ($this->getConfiguration('omerDays') == 0) {
        $cmd_list[]='omer';
      }

      if (!empty($cmd_list)) {
        foreach ($cmd_list as $the_cmd) {
          log::add('jcalendar', 'debug', 'Suppression de la commande : ' . $the_cmd);
          $cmd_to_remove = $this->getCmd(null, $the_cmd);
          if (is_object($cmd_to_remove)) {
            $cmd_to_remove->remove();
          }
        }
      } 
      $this->getjCalendarData();        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class jcalendarCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
        
    }

    /*     * **********************Getteur Setteur*************************** */
}


