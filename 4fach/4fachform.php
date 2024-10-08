<?php

/*****************************************************************************\
   Datei: 4fachform.php

   benoetigte Dateien:

   Beschreibung:

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/

include ("../4fcfg/config.inc.php");
include ("../4fcfg/dbcfg.inc.php");
include ("../4fcfg/e_cfg.inc.php");


/*****************************************************************************\
   Klasse: nachrichten4fach

   konstruktor : nachrichten4fach (Daten übergabe,  für wen (task), Fehler im Formular)
   destruktor  :
   methoden    :

   Beschreibung:

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/
class nachrichten4fach {

	/*************************************************************************
	   Konstruktor der Klasse: nachrichten4fach

	**************************************************************************/
    function nachrichten4fach ($formulardaten, $task, $errorselect){
      $this->task = $task ;
      $this->formdata = $formulardaten ;

      $this->lfd = $this->formdata ["00_lfd"];
      if ($errorselect != ""){
        $this->errorselect = $errorselect;
      } else {

	  // errorselect zuruecksetzen
         $this->errorselect ["01_medium"]   	= true ;
         $this->errorselect ["01_datum"]    	= true ;
         $this->errorselect ["01_zeichen"]   	= true ;
         $this->errorselect ["02_zeit"]      	= true ;
         $this->errorselect ["02_zeichen"]   	= true ;
         $this->errorselect ["03_datum"]   	= true ;
         $this->errorselect ["03_zeit"]   		= true ;
         $this->errorselect ["03_zeichen"]   	= true ;
         $this->errorselect ["05_gegenstelle"]   = true ;
         $this->errorselect ["06_befweg"]     	= true ;
         $this->errorselect ["06_befwegausw"] 	= true ;
         $this->errorselect ["07_durchspruch"]  	= true ;
         $this->errorselect ["08_befhinweis"]   	= true ;
         $this->errorselect ["08_befhinwausw"]	= true ;
         $this->errorselect ["10_anschrift"] 	= true ;
         $this->errorselect ["12_inhalt"]   	= true ;
         $this->errorselect ["12_abfzeit"]   	= true ;
         $this->errorselect ["13_abseinheit"]   	= true ;
         $this->errorselect ["14_zeichen"] 		= true ;
         $this->errorselect ["14_funktion"]   	= true ;
         $this->errorselect ["15_quitdatum"]   	= true ;
         $this->errorselect ["15_quitzeichen"] 	= true ;
         $this->errorselect ["17_vermerke"] 	= true ;
      }
      if ($this->formdata ["01_datum"]		== "0000-00-00 00:00:00") { $this->formdata ["01_datum"] = ""; }
      if ($this->formdata ["02_zeit"]		== "0000-00-00 00:00:00") { $this->formdata ["02_zeit"] = ""; }
      if ($this->formdata ["03_datum"]		== "0000-00-00 00:00:00") { $this->formdata ["03_datum"] = ""; }
      if ($this->formdata ["12_abfzeit"]		== "0000-00-00 00:00:00") { $this->formdata ["12_abfzeit"] = ""; }
      if ($this->formdata ["15_quitdatum"]	== "0000-00-00 00:00:00") { $this->formdata ["15_quitdatum"] = ""; }

      if ( ($this->formdata ["11_gesprnotiz"] == "t") OR
           ($this->formdata ["11_gesprnotiz"] == "1") OR
           ($this->formdata ["11_gesprnotiz"] == "on") ) {

        $this->formdata   ["11_gesprnotiz"] = true;
      } else {
        $this->formdata ["11_gesprnotiz"] = false;
      }

      if (debug){
        echo "<br><big>4fach data 087="; var_dump ($this->formdata); echo "</big><br>";
      }
      $this->plot_form () ;
    }

    var $task;        // text , Fuer welche Funktion ist der Vordruck
    var $formdata ;   // array, Formulardaten
    var $lfd ;        // integer, laufende Nummer der Nachricht
    var $errorselect; // array, Felder die falsch eingegeben wurden.

  // aktive und Inaktive Darstellungsfarben

  var $fktmsgbgcolor ;  // Hintergrundfarbe
  var $bg_color_fm_a  ; // rosa Fernmelder aktiv
  var $bg_color_fmp_a ; // hell gr�n Fernmelderpflichtfeld  aktiv
  var $bg_color_nw_a  ;  // orange
  var $bg_color_tx_a  ; // hell blau
  var $bg_color_si_a  ; // hell violett
  var $bg_color_inaktv ;  // weiss
  var $bg_color_aktv  ;  // weiss
  var $rbl_bg_color ;  // weiss
  var $bg_color_aktv_must ; // rot

  var $feldbg ;
  var $redcopy2;

  /****************************************************************************\
    Hintergrundfarben der Felder aktiv und inaktiv
  \****************************************************************************/
  function feldbgcolor (){
    if ( ( $this->task == "FM-Eingang") or
         ( $this->task == "FM-Eingang_Sichter" ) ) {
      $this->feldbg [ 1]["a"] = $this->bg_color_fmp_a;
      $this->feldbg [10]["a"] = $this->bg_color_fmp_a;
      $this->feldbg [12]["a"] = $this->bg_color_fmp_a;
      $this->feldbg [13]["a"] = $this->bg_color_fmp_a;
    } else {
       $this->feldbg [ 1]["a"] = $this->bg_color_tx_a;
       $this->feldbg [10]["a"] = $this->bg_color_tx_a;
       $this->feldbg [12]["a"] = $this->bg_color_tx_a;
       $this->feldbg [13]["a"] = $this->bg_color_tx_a;
    }

    $this->feldbg [ 1]["i"] = $this->bg_color_inaktv;
    $this->feldbg [ 2]["a"] = $this->bg_color_fm_a;
    $this->feldbg [ 2]["i"] = $this->bg_color_inaktv;
    $this->feldbg [ 3]["a"] = $this->bg_color_fmp_a; //mpr corrected script for Ausgang
    $this->feldbg [ 3]["i"] = $this->bg_color_inaktv;
    $this->feldbg [ 4]["a"] = $this->bg_color_fm_a;
    $this->feldbg [ 4]["i"] = $this->bg_color_inaktv;
    $this->feldbg [ 5]["a"] = $this->bg_color_fmp_a; //mpr corrected script for Ausgang
    $this->feldbg [ 5]["i"] = $this->bg_color_inaktv;
    $this->feldbg [ 6]["a"] = $this->bg_color_fmp_a; //mpr corrected script for Ausgang
    $this->feldbg [ 6]["i"] = $this->bg_color_inaktv;

    $this->feldbg [ 7]["a"] = $this->bg_color_tx_a;
    $this->feldbg [ 7]["i"] = $this->bg_color_inaktv;
    $this->feldbg [ 8]["a"] = $this->bg_color_tx_a;
    $this->feldbg [ 8]["i"] = $this->bg_color_inaktv;
    $this->feldbg [ 9]["a"] = $this->bg_color_tx_a;
    $this->feldbg [ 9]["i"] = $this->bg_color_inaktv;

    $this->feldbg [10]["i"] = $this->bg_color_inaktv;
    $this->feldbg [11]["a"] = $this->bg_color_tx_a;
    $this->feldbg [11]["i"] = $this->bg_color_inaktv;

    $this->feldbg [12]["i"] = $this->bg_color_inaktv;

    $this->feldbg [13]["i"] = $this->bg_color_inaktv;
    $this->feldbg [14]["a"] = $this->bg_color_tx_a;
    $this->feldbg [14]["i"] = $this->bg_color_inaktv;

    $this->feldbg [15]["a"] = $this->bg_color_si_a;
    $this->feldbg [15]["i"] = $this->bg_color_inaktv;
    $this->feldbg [16]["a"] = $this->bg_color_si_a;
    $this->feldbg [16]["i"] = $this->bg_color_inaktv;
    $this->feldbg [17]["a"] = $this->bg_color_si_a;
    $this->feldbg [17]["i"] = $this->bg_color_inaktv;
  }

  // Zuordnung der notwendigen Farben
  var $bg;
  var $feld ;

/*****************************************************************************\
   Funktion    : get_access_by_task
   Beschreibung: 
     

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/
  function get_access_by_task (){
    // Alle Felder auf inaktiv setzen
    for ( $i = 0; $i <= 17; $i++ ){
      $this->bg [$i] = $this->feldbg [$i]["i"] ;
      $this->feld [$i] = false;
    }

    switch ($this->task) {
      // Annahme einer Meldung durch Fernmelder
      case "FM-Eingang" :
      case "FM-Eingang_Anhang" :

        $this->bg [1] = $this->feldbg [1]["a"] ;
        $this->feld [1] = true;
        $this->bg [5] = $this->feldbg [5]["a"] ;
        $this->feld [5] = true;
        for ($i=7;$i<=14;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
        $this->bg   [11] = $this->feldbg [11]["i"] ;
        $this->feld [11] = false;

      break;
      case "FM-Eingang_Sichter" :
      case "FM-Eingang_Anhang_Sichter"  :
        $this->bg [1] = $this->feldbg [1]["a"] ;
        $this->feld [1] = true;
        $this->bg [5] = $this->feldbg [5]["a"] ;
        $this->feld [5] = true;
        for ($i=7;$i<=17;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
        // Ausser Gespraechsnotiz
        $this->bg [11]   = $this->feldbg [11]["i"] ;
        $this->feld [11] = false;

      break;
      // Weitergabe einer Meldung durch den Fernmelder
      case "FM-Ausgang" :
        $this->bg [3] = $this->feldbg [3]["a"] ;
        $this->feld [3] = true;
        $this->bg [5] = $this->feldbg [5]["a"] ;
        $this->feld [5] = true;
        $this->bg [6] = $this->feldbg [6]["a"] ;
        $this->feld [6] = true;
      break;

      // Weitergabe einer Meldung durch den Fernmelder mit Sichterfunktion
      case "FM-Ausgang_Sichter" :
        $this->bg [3] = $this->feldbg [3]["a"] ;
        $this->feld [3] = true;
        $this->bg [5] = $this->feldbg [5]["a"] ;
        $this->feld [5] = true;
        $this->bg [6] = $this->feldbg [6]["a"] ;
        $this->feld [6] = true;
        for ($i=15;$i<=17;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
      break;

      case "Stab_schreiben" :
        for ($i=7;$i<=14;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
      break;

      case "Stab_lesen" :
        for ($i=1;$i<=17;$i++){
          $this->bg [$i] = $this->formbgcolor ;
          $this->feld [$i] = false;
        }

      break;

      case "Stab_sichten" :
        for ($i=15;$i<=17;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
      break;
      case "Stab_gesprnoti":
        $this->bg [1] = $this->feldbg [1]["a"] ;
        $this->feld [1] = true;

        for ($i=7;$i<=14;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
        for ($i=15;$i<=17;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
      break;

      case "FM-Admin" :
        for ($i=1;$i<=17;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
        }
      break;

      case "SI-Admin" :
        for ($i=15;$i<=17;$i++){
          $this->bg [$i] = $this->feldbg [$i]["a"] ;
          $this->feld [$i] = true;
      }
      break;

      default :
        for ($i=1;$i<=17;$i++){
          $this->feld [$i] = false;
        }
    } // switch $rolle
  }

  var $empfarray ;

/*****************************************************************************\
   Funktion    : ziele ()
   Beschreibung:

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/
  function ziele (){
  include ("../4fcfg/fkt_rolle.inc.php");

    for ($i=1; $i <= 5 ; $i++){
      for ($j=1; $j <= 4 ; $j++){
        $this->empfarray [$i][$j]["checked"] = false;
        $this->empfarray [$i][$j]["cpycol"]  = "";
        $this->empfarray [$i][$j]["typ"]     = $empf_matrix [$i][$j]["typ"];
        $this->empfarray [$i][$j]["fkt"]     = $empf_matrix [$i][$j]["fkt"];
        $this->empfarray [$i][$j]["rolle"]   = $empf_matrix [$i][$j]["rolle"];
        $this->empfarray [$i][$j]["auto"]    = $empf_matrix [$i][$j]["auto"];
      }
    }

    $empf_text  = $this->formdata ["16_empf"] ; // Zeile mit den Empfaengern aus der DB
      // Wandel die Textzeile mit den Empfaengern in ein ARRAY um
    $empf_array_color = explode (",",$empf_text);

    for ( $i=0; $i <= count ( $empf_array_color ); $i++ ) {
        //  die Farbe der Kopie
      list ( $fkt, $cpycol ) = explode ("_", $empf_array_color [$i]);
      if ( $fkt != "" ){
        $empf_array [$i]['fkt'] = $fkt ;
        $empf_array [$i]['cpy'] = $cpycol ;
        if ($fkt == $_SESSION ['vStab_funktion']) {
          $this->fktmsgbgcolor = $cpycol ;
        }
      }
    }
    $sonstcount = 2;
    for ($i=1; $i <= 5 ; $i++){
      for ($j=1; $j <= 4 ; $j++){
        if (isset ($empf_array)){
          foreach ($empf_array as $empfaenger){
            if ( ( strtoupper ( $empfaenger['fkt'] ) ==  strtoupper ( $empf_matrix [$i][$j]["fkt"]) ) and
                 ( $empf_matrix [$i][$j]["fkt"] != "" ) ){
              $this->empfarray [$i][$j]["checked"] = true;
              $this->empfarray [$i][$j]["cpycol"] = $empfaenger['cpy'];
            }
          }
        }
      }
    }
  $this->redcopy2 = $redcopy2;

  }


/*****************************************************************************\
   Funktion    :
   Beschreibung:

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/
    // Listet unter Inhalt eventuelle Anhangsdateien als href auf
  function list_anhang (){
    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
      // in 12_anhang stehen die Anhangdateien mit ";" getrennt.
    echo "<br>";
    $anhaenge = split(";", $this->formdata ["12_anhang"]);
    foreach ($anhaenge as $anhang){
      if ($anhang != "") {
        echo "<a style=\"font-size:18px; font-weight:900;\" href=\"";
        echo $conf_4f ["ablage_uri"]."/".$anhang;
        echo "\" target=\"_blank\">";
        echo $anhang;
        echo "</a><br>";
      }
    }
  } // list_anhang ()

/*****************************************************************************\
   Funktion    : show_menue_buttons ()
   Beschreibung:

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/
  function show_menue_buttons ($umfang, $ordnum){
    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/fkt_rolle.inc.php");

//    echo "<fieldset>";
//    echo "<legend>Aktion:</legend>\n";
//    echo "<TABLE BORDER=\"1\" CELLSPACING=\"2\" CELLPEDDING=\"0\" bgcolor=$color_data_table >\n";
    echo "<TABLE>\n";
    echo "<TBODY>\n";
    echo "<TR>\n";
    echo "<TD>\n";
    if ($umfang == 2){
      echo "<TABLE BORDER=\"0\" CELLSPACING=\"1\" CELLPEDDING=\"0\">\n";
      echo "<input type=\"hidden\" name=\"kate_todo\" value=\"speichern\">\n";
      echo "<input type=\"hidden\" name=\"msglfd\" value=\"".$this->formdata["00_lfd"]."\">\n";
      echo "<TR>\n";
      echo "<TBODY>\n";
      echo "<TD>\n";
        // Druckersymbol
      echo "<a href=\"javascript:window.print()\">
            <img src=\"".$conf_design_path."/print.gif\" alt=\"Drucken\" width=\"32\"height=\"32\" border=\"0\" title=\"Drucken\"></a>\n";
      echo "</TD>\n";

      if ( $this->task == "Stab_lesen"){   // Stab lesen
          // MASTER KATEGORIE
        echo "<TD>";
        $katego_master = new  kategorien ("master");
        $katearr_master = $katego_master->db_get_kategobymsg ($this->formdata["00_lfd"]);
        echo"<a ";
          // Ist die Funktion berechtigt globale Kategorien zu aendern?
        $berechtigt = ($_SESSION ["vStab_funktion"] == $redcopy2) OR
                      ($_SESSION ["vStab_funktion"] == "Si");
        if ($berechtigt) {
          echo "href=\"katgoedt.php?dbtyp=master&fkt=edit&msgno=".$this->formdata["00_lfd"]."\"";
        }
        echo ">
            <img src=\"".$conf_design_path."/folder_global.gif\"
                 alt=\"globale Ordner verwalten (falls berechtigt)\"
                 width=\"32\"
                 height=\"32\"
                 border=\"0\"
                 title=\"globale Ordner verwalten (falls berechtigt)\"></a>";
        echo "</TD>";

          // Schreibe die augenblickliche Masterkategorie hin
        if ( $katearr_master["kategorie"] != ""){
          echo "<TD>";
          $color = "red";
          echo"<a><img src=\"./createbutton.php?icontext=".$katearr_master["kategorie"]."&color=".$color."\" alt=\"".$katearr_master["beschreibung"]."\"></a>";
          echo "</TD>";
        }

        echo "<TD>";

        if ($berechtigt) {
          $katego_master->pulldown_kategorien ($katearr_master["lfd"], true, $ordnum);
        }
        echo "</TD>\n";

          // FUNKTIONS KATEGORIE
        echo "<TD>\n";
        $katego_fkt = new  kategorien ("fkt");
        $katearr_fkt = $katego_fkt->db_get_kategobymsg ($this->formdata["00_lfd"]);
        echo"<a href=\"katgoedt.php?dbtyp=fkt&fkt=edit&msgno=".$this->formdata["00_lfd"]."\">
            <img src=\"".$conf_design_path."/folder_user.gif\"
                 alt=\"Funktionsordner verwalten\"
                 width=\"32\"
                 height=\"32\"
                 border=\"0\"
                 title=\"funktionsordner verwalten\"></a>\n";
        echo "</TD>\n";
        echo "<TD>\n";
        echo "</TD>";
        echo "<TD>";
        if ($katearr_fkt["kategorie"] != "" ){
          $color = "blue";
          echo"<a><img src=\"./createbutton.php?icontext=".$katearr_fkt["kategorie"]."&color=".$color."\" alt=\"".$katearr_fkt["beschreibung"]."\"></a>";
        }
        echo "</TD>";
        echo "<TD>";
        $katego_fkt->pulldown_kategorien ($katearr_fkt["lfd"], true, $ordnum);
        echo "</TD>\n";

         // BENUTZER KATEGORIE
        echo "<TD>\n";
        $katego_user = new  kategorien ("user");
        $katearr_user = $katego_user->db_get_kategobymsg ($this->formdata["00_lfd"]);
        echo"<a href=\"katgoedt.php?dbtyp=user&fkt=edit&msgno=".$this->formdata["00_lfd"]."\">
            <img src=\"".$conf_design_path."/folder_local.gif\"
                 alt=\"pers&ouml;nliche Ordner verwalten\"
                 width=\"32\"
                 height=\"32\"
                 border=\"0\"
                 title=\"pers&ouml;nliche Ordner verwalten\"></a>\n";
        echo "</TD>\n";
        echo "<TD>\n";
        echo "</TD>";
        echo "<TD>";
        if ($katearr_user["kategorie"] != "" ){
          $color = "green";
          echo"<a><img src=\"./createbutton.php?icontext=".$katearr_user["kategorie"]."&color=".$color."\" alt=\"".$katearr_user["beschreibung"]."\"></a>";
        }
        echo "</TD>";
        echo "<TD>";
        $katego_user->pulldown_kategorien ($katearr_user["lfd"], true, $ordnum);
        echo "</TD>";
        echo "<TD><input type=\"image\" name=\"4fachkatego_absenden\" src=\"button.php?type=menue&m_text=<=zuordnen&m_fs=10&m_form=rund\" alt=\"zuordnen\">";
        echo "</TD>\n";
      }
      echo "</TR>\n";
      echo "</TBODY>\n";
      echo "</TABLE>\n";
    }
    echo "</TD>";

    echo "<TD>";
//    echo "<TABLE BORDER=\"1\" CELLSPACING=\"1\" CELLPEDDING=\"1\">\n";
        echo "<TABLE>\n";
    echo "<TBODY>\n";
    echo "<TR>\n";
          /*
                                          04Richtung      Antwort Weiterleitung

          FM      FM-Eingang_Sichter              -       -         -
          FM      FM-Eingang                      -       -         -
          FM      FM-Ausgang                      A       X         -

          Si      Stab_sichten                    E       -         -
          Si      Stab_sichten                    A       -         -
          Si      SI-Admin                        E       -         -
          Si      SI-Admin                        A       -         -

          Stab    Stab_lesen                      E       X         X
          Stab    Stab_lesen                      A       -         X

          Stab    Stab_schreiben                  -       -         -
                                                          2         2
          */

      switch ($this->task){
      case "Stab_lesen":
          echo "<td>\n";
          echo "<input type=\"hidden\" name=\"00_lfd\" value=\"".$this->lfd."\">\n";
          echo "<input type=\"hidden\" name=\"task\" value=\"".$this->task."\">\n";
          echo "<input type=\"image\" name=\"gelesen\" src=\"button.php?type=menue&m_text=gelesen/OK&m_fs=10&m_form=rund\" alt=\"gelesen\">\n";
          echo "</td>";

          if ($this->formdata["04_richtung"]=="E"){
            echo "<td><input type=\"image\" name=\"antwort\" src=\"button.php?type=menue&m_text=Antwort&m_fs=10&m_form=spitz\" alt=\"antworten\"></td>\n";
            echo "<td><input type=\"image\" name=\"weiterleiten\" src=\"button.php?type=menue&m_text=Weiterleiten&m_fs=10&m_form=spitz\" alt=\"weiterleiten\"></td>\n";
          } elseif ($this->formdata["04_richtung"]=="A"){
            echo "<td><input type=\"image\" name=\"weiterleiten\" src=\"button.php?type=menue&m_text=Weiterleiten&m_fs=10&m_form=spitz\" alt=\"weiterleiten\"></td>\n";
          }
        break;

        case "FM-Eingang":
        case "FM-Eingang_Sichter":
        case "Stab_schreiben":
        case "FM-Eingang_Anhang":
        case "FM-Eingang_Anhang_Sichter":
        case "Stab_gesprnoti":
          echo "<td>\n";
          echo "<input type=\"hidden\" name=\"00_lfd\" value=\"".$this->lfd."\">\n";
          echo "<input type=\"hidden\" name=\"task\" value=\"".$this->task."\">\n";
            // Anhänge
          echo "<input type=\"image\" name=\"anhang_plus\" src=\"".$conf_design_path."/attachment.gif\" alt=\"Anhang anfuegen\">\n";
          echo "</td>\n";
          echo "<td><input type=\"image\" name=\"absenden\" src=\"button.php?type=menue&m_text=absenden&m_fs=10&m_form=rund\" alt=\"absenden\"></td>\n";
          echo "<td><input type=\"image\" name=\"abbrechen\" src=\"button.php?type=menue&m_text=abbrechen&m_fs=10&m_form=rund\" alt=\"abbrechen\"></td>\n";
        break;

        case "FM-Ausgang":
        case "FM-Ausgang_Sichter":
          echo "<td>\n";
          echo "<input type=\"hidden\" name=\"00_lfd\" value=\"".$this->lfd."\">\n";
          echo "<input type=\"hidden\" name=\"task\" value=\"".$this->task."\">\n";
          echo "<input type=\"image\" name=\"absenden\" src=\"button.php?type=menue&m_text=absenden&m_fs=10&m_form=rund\" alt=\"absenden\">\n";
          echo "</td>\n";
          echo "<td><input type=\"image\" name=\"abbrechen\" src=\"button.php?type=menue&m_text=abbrechen&m_fs=10&m_form=rund\" alt=\"abbrechen\"></td>\n";
          if ($this->formdata["04_richtung"]=="A"){
            echo "<td><input type=\"image\" name=\"antwort\" src=\"button.php?type=menue&m_text=Antwort&m_fs=10&m_form=spitz\" alt=\"antworten\"></td>\n";
          }

        break;
        case "Stab_sichten":
        case "SI-Admin":
          echo "<td>\n";
          echo "<input type=\"hidden\" name=\"00_lfd\" value=\"".$this->lfd."\">\n";
          echo "<input type=\"hidden\" name=\"task\" value=\"".$this->task."\">\n";
          echo "<input type=\"image\" name=\"absenden\" src=\"button.php?type=menue&m_text=absenden&m_fs=10&m_form=rund\" alt=\"absenden\">\n";
          echo "</td>\n";
          echo "<td><input type=\"image\" name=\"abbrechen\" src=\"button.php?type=menue&m_text=abbrechen&m_fs=10&m_form=rund\" alt=\"abbrechen\"></td>\n";
        break;
     } // switch
    echo "</TR>";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</TD>\n";
    echo "</TR>\n";
    echo "</TBODY>\n";
    echo "</TABLE>\n";
//    echo "</fieldset>\n";
  }


/*****************************************************************************\
   Funktion    :  showerrorinfo
   Beschreibung:  Ausgabe Fehlermeldung Info

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/
  function showerrorinfo ($errorat)
  {   include ("../4fcfg/config.inc.php");
    echo "<a href=\"../language/german/helptext.php?Errorart=".$errorat.
         "\" onclick=\"FensterOeffnen(this.href); return false\"><img src=\"".
         $conf_design_path."/warning.gif\" alt=\"Fehler\" width=\"24\"height=\"24\" title=\"Fehler\"></a>";
  }



  var $formbgcolor ; // Hintergrundfarbe

/*****************************************************************************\
   Funktion     :  plot_form

   Beschreibung :  Ausgabe des Formulars

   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/
  function plot_form (){

    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/para.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
    include ("../4fcfg/fkt_rolle.inc.php");
    include ("../4fcfg/color.inc.php");

    $this->ziele (); // Ziele und Farben   $fktmsgbgcolor

    switch ($this->fktmsgbgcolor) {
      case "rt": $this->formbgcolor =  $cfg ["vbg"]  ["rt"] ; break;
      case "gn": $this->formbgcolor =  $cfg ["vbg"]  ["gn"] ; break;
      case "bl": $this->formbgcolor =  $cfg ["vbg"]  ["bl"] ; break;
      case "ge": $this->formbgcolor =  $cfg ["vbg"]  ["ge"] ; break;
      default  : $this->formbgcolor =  $cfg ["vbg"]  ["default"] ;
    }
    $this->feldbgcolor ();
    $this->get_access_by_task ($this->task);

    if (debug){
      echo "<big><big>TASK TASK TASK===".$this->task."</big></big><br><b>";
    }

    pre_html ("N","Formular ".$this->task." ".$conf_4f ["Titelkurz"]." ".$conf_4f ["Version"], ""); // Normaler Seitenaufbau ohne Auffrischung
  //  echo "<body style=\"text-align: left; background-color: rgb(220,220,255); \">\n"; //".$this->formbgcolor.";\">\n";
	    echo "<body style=\"text-align: left; background-color:#87CEEB; \">\n"; //".$this->formbgcolor.";\">\n";

    include_once ("./katego.php");

    echo "<FORM style=\"\" method=\"get\" action=\"".$conf_4f ["MainURL"]."\" name=\"4fach\">\n";
    $this->show_menue_buttons (2, "oben");
    echo "<!-- **********4fachform.php-697-anfang-HAUPTTABELLE*********** -->\n";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; width: 810px;\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    echo "<td style=\"height: 113px; width: 860px;\">\n";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; height: 32px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    /****************************************************************************\
                              F M  -  B E T R I E B S S T E L L E
    \****************************************************************************/
    // Zeile, Spalte 1,1    EINGANG    1  1   Eingang
    echo "<td style=\"width: 230px; background-color: ".$this->bg[1].";\">\n";
    echo "<div style=\"text-align: center; width: 250px;\">EINGANG</div>\n"; // 200px
    echo "</td>\n";
    // Zeile, Spalte 1,2    AUSGANG    2  2   Ausgang Annahmevermerk Befoerderungsvermerk
    echo "<td style=\"text-align: center; background-color: ".$this->bg[2]."; width: 400px;\">\n"; // 427px
    echo "<div style=\"text-align: center; width: 405px;\">AUSGANG</div>\n"; // 450px
    // Zeile, Spalte 1,3    Nachweisung   8   4   Nachweis Nummer E A
    echo "<td style=\"text-align: center; width: 150px; background-color: ".$this->bg[4].";\">Nachweisnummer</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    /****************************************************************************\
    |  Zeile, Spalte 2 , 1   Aufnahmevermerk  1   1   Eingang                    |
    \****************************************************************************/
    if (!$this->feld [1]){
      $param = " disabled ";
      // Radio Button die deaktiviert sind liefern keinen Wert zurueck !!!
      echo "<input id=\"f_01_medium\" type=\"hidden\" name=\"01_medium\" value=\"".$this->formdata["01_medium"]."\">\n";
    }
    else {
      $param = "";
    } 							// 230px
    echo "<td style=\"background-color: ".$this->bg[1]."; width: 250px; text-align: center; vertical-align: top;\"><!--005-->\n";
    echo "<div style=\"text-align: center;\"><label for=\"01_medium\">Aufnahmevermerk<br></label></div>\n";
    if ( ( $this->errorselect ["01_medium"] == false ) AND ($this->feld [1]) ) {
      $this->showerrorinfo ("01_medium");
    }
    if ($this->formdata["01_medium"]=="Fe") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_01_medium_fe\" name=\"01_medium\" value=\"Fe\" type=\"radio\" ".$param.$sel.">Fe";
    if ($this->formdata["01_medium"]=="Fu") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_01_medium_fu\" name=\"01_medium\" value=\"Fu\" type=\"radio\" ".$param.$sel.">Fu";
    if ($this->formdata["01_medium_me"]=="Me") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_01_medium_me\" name=\"01_medium\" value=\"Me\" type=\"radio\" ".$param.$sel.">Me";
    if ($this->formdata["01_medium"]=="Fax") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_01_medium_fax\" name=\"01_medium\" value=\"Fax\" type=\"radio\" ".$param.$sel.">Fax";
    if ($this->formdata["01_medium"]=="@") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_01_medium_at\" name=\"01_medium\" value=\"@\" type=\"radio\" ".$param.$sel.">@";
    echo "<br>\n";
    if (!$this->feld [1]){
      if ( ( $this->formdata["01_datum"] != "") or
           ( $this->formdata["01_zeichen"] != "" ) ) {
        if ( posttakzeit ) {
          echo "<div style=\"text-align: center;\"><b>";
          echo $this->formdata["01_datum"]."&nbsp; &nbsp;".$this->formdata["01_zeichen"];
          echo "</b></div>";
        } else {
        echo "<div style=\"text-align: center;\"><b>";
        echo $this->formdata["01_datum"]."&nbsp; &nbsp;".$this->formdata["01_zeichen"];
        echo "</b></div>";
        }
      } else {
        echo "<br>";
      }
    } else {
    if ( $this->errorselect ["01_datum"] == false ){
      $this->showerrorinfo ("01_datum");
    }
    echo "<input id=\"f_01_datum\" maxlength=\"13\" size=\"13\" name=\"01_datum\" value=\"".$this->formdata["01_datum"]."\">\n";
    if ( $this->errorselect ["01_zeichen"] == false ){
      $this->showerrorinfo ("01_zeichen");    }
    echo "<input id=\"f_01_zeichen\" maxlength=\"3\" size=\"3\" name=\"01_zeichen\" value=\"".$this->formdata["01_zeichen"]."\">\n";
    }
    echo "<div style=\"text-align: center;\"><label for=\"01_datum\">Datum &nbsp; &nbsp;Uhrzeit &nbsp; &nbsp;</label><label for=\"01_zeichen\"></label>Zeichen</td></div>";
    /****************************************************************************\
    | Zeile, Spalte 2 , 2+3  2   2   Ausgang Annahmevermerk +
    |                         4  3   Ausgang Bef�rderungsvermerk
    02_zeit
    02_zeichen
    \****************************************************************************/
    echo "<td style=\"width: 400px; background-color: ".$this->bg[2].";\"><!--006-->\n"; // 427px
    echo "<table style=\"text-align: \"center\"; background-color: ".$this->rbl_bg_color."; width: 400px; height: 80px; margin-left: auto; margin-right: auto;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
    echo "<tbody><!--table + tbody 003-->\n";
    echo "<tr>\n";
    echo "<td style=\"height: 80px; width: 150px; background-color: ".$this->bg[2]."; text-align: center; vertical-align: top;\">\n";
    echo "<div style=\"text-align: center;\">Annahmevermerk<br></div>\n";
    if (!$this->feld[2]) {
      if ( ( $this->formdata["02_zeit"] != "" ) or
           ( $this->formdata["02_zeichen"] != "" ) ) {
        echo "<div style=\"text-align: center;\"><b>";
        echo $this->formdata["02_zeit"]."&nbsp; &nbsp;".$this->formdata["02_zeichen"];
        echo "</b></div>";
      } else {
        echo "<br>";
      }
    } else {
      if ( $this->errorselect ["02_zeit"] == false ){
      $this->showerrorinfo ("02_zeit");      }
      echo "<input id=\"f_02_zeit\" maxlength=\"13\" size=\"13\" name=\"02_zeit\" value=\"".$this->formdata["02_zeit"]."\">&nbsp;\n";
      if ( $this->errorselect ["02_zeichen"] == false ){
      $this->showerrorinfo ("02_zeichen");      }
      echo "<input id=\"f_02_zeichen\" maxlength=\"3\" size=\"3\" name=\"02_zeichen\" value=\"".$this->formdata["02_zeichen"]."\"><br>\n";
    }
    echo "<div style=\"text-align: center;\">";
    echo "&nbsp;Uhrzeit &nbsp; &nbsp;Zeichen</td>\n";
    echo "</div>";
    echo "<td style=\"height: 80px; width: 220px; background-color: ".$this->bg[3]."; text-align: center; vertical-align: top;\">\n";
    echo "<div style=\"text-align: center;\">Bef&ouml;rderungsvermerk<br></div>\n";
    if (!$this->feld [3]){
      if ( ( $this->formdata["03_datum"]   != "") or
           ( $this->formdata["03_zeichen"] != "" ) ) {
        if ( posttakzeit ) {
          echo "<div style=\"text-align: center;\"><b>";
          $takzeit = konv_datetime_taktime ($this->formdata["03_datum"]);
          echo $takzeit."&nbsp; &nbsp;".$this->formdata["03_zeichen"];
          echo "</b></div>";
        } else {
          echo "<div style=\"text-align: center;\"><b>";
          echo $this->formdata["03_datum"]."&nbsp; &nbsp;".$this->formdata["03_zeichen"];
          echo "</b></div>";
        }
      }else {
        echo "<br>";
      }
    } else {
      if ( $this->errorselect ["03_datum"] == false ){
      $this->showerrorinfo ("03_datum");      }
      echo "<input id=\"f_03_datum\" maxlength=\"13\" size=\"13\" name=\"03_datum\" value=\"".$this->formdata["03_datum"]."\">\n";
      if ( $this->errorselect ["03_zeichen"] == false ){
      $this->showerrorinfo ("03_zeichen");      }

      echo "<input id=\"f_03_zeichen\" maxlength=\"3\" size=\"3\" name=\"03_zeichen\" value=\"".$this->formdata["03_zeichen"]."\"><br>\n";
    }
    echo "<div style=\"text-align: center;\">";
    echo "Datum &nbsp; &nbsp;Uhrzeit &nbsp; &nbsp;Zeichen</td>\n";
    echo "</div>";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 2 , 4    8   4   Nachweis Nummer E A
    04_richtung;
    04_nummer;
    \****************************************************************************/
    echo "<td style=\"width: 150px; background-color: ".$this->bg[4]."; text-align: left; vertical-align: top;\">Nachweis Nr.";
    if (!$this->feld[4]) {
        echo "<div style=\"text-align: center;\"><b><big><big><big>";
        echo $this->formdata["04_richtung"]."&nbsp; &nbsp;".$this->formdata["04_nummer"];
        echo "</big></big></big></b></div>";
        echo "<input id=\"f_04_richtung\" type=\"hidden\" name=\"04_richtung\" value=\"".$this->formdata["04_richtung"]."\">\n";
        echo "<input id=\"f_04_nummer\" type=\"hidden\" name=\"04_nummer\" value=\"".$this->formdata["04_nummer"]."\">\n";
    } else {
      echo "<input id=\"f_04_nummer\" maxlength=\"6\" size=\"6\" name=\"04_nummer\" value=\"".$this->formdata["04_nummer"]."\"><br>\n";
      if (!$this->feld[4]) {
        $param = " disabled ";
        // Radio Button die deaktiviert sind liefern keinen Wert zurck !!!
        echo "<input id=\"f_04_richtung\" type=\"hidden\" name=\"04_richtung\" value=\"".$this->formdata["04_richtung"]."\">\n";
      }
      else {
        $param = "";
      }
      if ($this->formdata["04_richtung"]=="E") {$sel = "checked=\"checked\"";} else {$sel = "";}
      echo "<input id=\"f_04_richtung\" name=\"04_richtung\" value=\"E\" type=\"radio\" ".$param.$sel.">E<br>\n";
      if ($this->formdata["04_richtung"]=="A") {$sel = "checked=\"checked\"";} else {$sel = "";}
      echo "<input id=\"f_04_richtung\" name=\"04_richtung\" value=\"A\" type=\"radio\" ".$param.$sel.">A<br>\n";
    }
    echo "</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    // Zeile, Spalte 3 , 1  Rufname der Gegenst. 16   5   Rufname der Gegenstelle
    echo "<td>\n";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; width: 821px; height: 52px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    echo "<td style=\"width: 227px; background-color: ".$this->bg[5].";\">Rufname der Gegenstelle/<br>\n";
    echo "Spruchkopf</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 3 , 2   16   5   Rufname der Gegenstelle
    05_gegenstelle
    \****************************************************************************/
    echo "<td style=\"text-align: center; background-color: ".$this->bg[5]."; width: 580px;\">\n";
    if  (!$this->feld[5]) {
      echo "<div style=\"text-align: left;\"><b>";
      echo $this->formdata["05_gegenstelle"];
      echo "</b></div>";
    } else {
       echo "<input id=\"f_05_gegenstelle\" maxlength=\"80\" size=\"80\" name=\"05_gegenstelle\" value=\"".$this->formdata["05_gegenstelle"]."\">\n";
    }
    echo "</td>";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    // Zeile 4
    echo "<tr> ";
    echo "<td style=\"width: 821px; height: 40px;\">";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; width: 821px; height: 46px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    // Zeile, Spalte 4 , 1   32   6   Befoerderungsweg
    echo "<td style=\"width: 131px; background-color: ".$this->bg[6]."; font-size:11px\">Bef&ouml;rderungsweg:</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 4 , 2   32   6   Bef�rderungsweg
    06_befweg
    \****************************************************************************/
    echo "<td style=\"text-align: center; width: 446px; background-color: ".$this->bg[6].";\">\n";
    if (!$this->feld[6]) {
      echo "<div style=\"text-align: left;\"><b>";
      echo $this->formdata["06_befweg"];
      echo "</b></div>";
    } else {
      echo "<input id=\"f_06_befweg\" maxlength=\"50\" size=\"50\" name=\"06_befweg\" value=\"".$this->formdata["06_befweg"]."\">\n";
    }
    echo "</td>";
    /****************************************************************************\
    // Zeile, Spalte 4 , 3   32   6   Bef�rderungsweg
    06_befwegausw
    \****************************************************************************/
    if (!$this->feld[6]) {
      $param = " disabled ";
      // Radio Button die deaktiviert sind liefern keinen Wert zurck !!!
      echo "<input id=\"f_06_befwegausw\" type=\"hidden\" name=\"06_befwegausw\" value=\"".$this->formdata["06_befwegausw"]."\">\n";
    }
    else {
      $param = "";
    }
    echo "<td style=\"width: 230px; background-color: ".$this->bg[6].";\">";
    if ($this->formdata["06_befwegausw"]=="Fe") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_06_befwegausw_fe\" name=\"06_befwegausw\" value=\"Fe\" type=\"radio\" ".$param.$sel.">Fe";
    if ($this->formdata["06_befwegausw"]=="Fu") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_06_befwegausw_fu\" name=\"06_befwegausw\" value=\"Fu\" type=\"radio\" ".$param.$sel.">Fu";
    if ($this->formdata["06_befwegausw"]=="Me") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_06_befwegausw_me\" name=\"06_befwegausw\" value=\"Me\" type=\"radio\" ".$param.$sel.">Me";
    if ($this->formdata["06_befwegausw"]=="Fax") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_06_befwegausw_fax\" name=\"06_befwegausw\" value=\"Fax\" type=\"radio\" ".$param.$sel.">Fax";
    if ($this->formdata["06_befwegausw"]=="@") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_06_befwegausw_at\" name=\"06_befwegausw\" value=\"@\" type=\"radio\" ".$param.$sel.">@";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    /*                          F M  -  B E T R I E B S S T E L L E
    ********************************************************************************************
    ********************************************************************************************
                                            I N H A L T
    */
    echo "<tr>\n";
    echo "<td style=\"width: 831px; height: 0px;\" align=\"left\" valign=\"top\">\n";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; width: 821px; height: 64px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    /****************************************************************************\
    // Zeile, Spalte 5,1   64 7   Durchsage / Spruch
    \****************************************************************************/
    if (!$this->feld[7]) {
      $param = " disabled ";
      // Radio Button die deaktiviert sind liefern keinen Wert zurck !!!
      echo "<input id=\"f_07_durchspruch\" type=\"hidden\" name=\"07_durchspruch\" value=\"".$this->formdata["07_durchspruch"]."\">\n";
    }
    else {
      $param = "";}
    echo "<td style=\"width: 126px; background-color:".$this->bg[7]."; font-size:10px\">\n";
    if ($this->formdata["07_durchspruch"]=="D") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_07_durchspruch\" name=\"07_durchspruch\" value=\"D\" type=\"radio\" ".$param.$sel.">DURCHSAGE<br>\n";
    if ($this->formdata["07_durchspruch"]=="S") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_07_durchspruch\" name=\"07_durchspruch\" value=\"S\" type=\"radio\" ".$param.$sel.">Spruch</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 5,2   128    8   Befoerderungshinweis
    \****************************************************************************/
    echo "<td style=\"text-align: left; width: 120px; background-color: ".$this->bg[8].";  font-size:10px\">Bef&ouml;rderungshinweis:<br>Tel.</td>\n"; //140px
    /****************************************************************************\
    // Zeile, Spalte 5,3   128    8   Befoerderungshinweis
    08_befhinweis
    \****************************************************************************/
    echo "<td style=\"width: 294px; background-color: ".$this->bg[8].";  font-size:10px\">\n";
    if  (!$this->feld[8]) {
      echo "<div style=\"text-align: left;\"><b>";
      echo $this->formdata["08_befhinweis"];
      echo "</b></div>";
    } else {
      echo "<input id=\"f_08_befhinweis\" maxlength=\"40\" size=\"40\" name=\"08_befhinweis\" value=\"".$this->formdata["08_befhinweis"]."\">";
    }
    echo "</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 5,4   128    8   Befoerderungshinweis
    08_befhinwausw
    \****************************************************************************/
    if  (!$this->feld[8]) {
      $param = " disabled ";
      // Radio Button die deaktiviert sind liefern keinen Wert zurck !!!
      echo "<input id=\"f_08_befhinwausw\" type=\"hidden\" name=\"08_befhinwausw\" value=\"".$this->formdata["08_befhinwausw"]."\">\n";
    }
    else {
      $param = "";
    }
    echo "<td style=\"width: 225px; background-color: ".$this->bg[8].";\">\n";
    if ($this->formdata["08_befhinwausw"]=="Fe") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_08_befhinwausw_fe\" name=\"08_befhinwausw\" value=\"Fe\" type=\"radio\" ".$param.$sel.">Fe";
    if ($this->formdata["08_befhinwausw"]=="Fu") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_08_befhinwausw_fu\" name=\"08_befhinwausw\" value=\"Fu\" type=\"radio\" ".$param.$sel.">Fu";
    if ($this->formdata["08_befhinwausw"]=="Me") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_08_befhinwausw_me\" name=\"08_befhinwausw\" value=\"Me\" type=\"radio\" ".$param.$sel.">Me";
    if ($this->formdata["08_befhinwausw"]=="Fax") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_08_befhinwausw_fax\" name=\"08_befhinwausw\" value=\"Fax\" type=\"radio\" ".$param.$sel.">Fax";
    if ($this->formdata["08_befhinwausw"]=="@") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_08_befhinwausw_at\" name=\"08_befhinwausw\" value=\"@\" type=\"radio\" ".$param.$sel.">@";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td style=\"text-align: left; background-color: ".$this->rbl_bg_color."\" align=\"left\" valign=\"top\">\n";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; width: 820px; height: 100px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    /****************************************************************************\
    // Zeile, Spalte 6,1   Vorrangstufe     256   9   VORRANGSTUFE !!!
    09_vorrangstufe;
    \****************************************************************************/
    echo "<td style=\"width: 90px; background-color: ".$this->bg[9].";\">Vorrangstufe<br>\n";
    if (((($this->formdata["09_vorrangstufe"]) != "" )) or (!$this->feld[9])) {
      echo "<div style=\"text-align: center; font-size:24px; font-weight:900;\"><big><big><b>";
      echo $this->formdata["09_vorrangstufe"];
      echo "</big></big></b></div>";
    } else {
      echo "<select ".$param." name=\"09_vorrangstufe\" style=\"text-align: center; background-color:".$this->bg[9]."; font-size:xx-large; font-weight:bold;\">\n";
      if ($this->formdata["09_vorrangstufe"]=="") {$sel = " selected ";} else {$sel = "";}
      echo "<option ".$sel."></option>\n";
      if ($this->formdata["09_vorrangstufe"]=="eee") {$sel = " selected ";} else {$sel = "";}
      echo "<option ".$sel.">eee</option>\n";
      if ($this->formdata["09_vorrangstufe"]=="sss") {$sel = " selected ";} else {$sel = "";}
      echo "<option ".$sel.">sss</option>\n";
      if ($this->formdata["09_vorrangstufe"]=="bbb") {$sel = " selected ";} else {$sel = "";}
      echo "<option ".$sel." >bbb</option>\n";
      if ($this->formdata["09_vorrangstufe"]=="aaa") {$sel = " selected ";} else {$sel = "";}
      echo "<option ".$sel.">aaa</option>\n";
    }
    echo "</select></td>\n";
    /****************************************************************************\
    // Zeile, Spalte 6,2   Anschrift      512 10  Anschrift
    10_anschrift
    \****************************************************************************/
    echo "<td style=\"width: 600px; background-color: ".$this->bg[10].";\">";
    echo "Anschrift:";
    if ( $this->errorselect ["10_anschrift"] == false ){
      $this->showerrorinfo ("10_anschrift");    }
    echo "<br>\n";
    if (!$this->feld[10]) {
      echo "<input id=\"f_10_anschrift\" type=\"hidden\" name=\"10_anschrift\" value=\"".$this->formdata["10_anschrift"]."\">\n";
      echo "<div style=\"text-align: center; font-size:24px; font-weight:900;\">";
      echo $this->formdata["10_anschrift"] ;
      echo "</div>\n";
    } else {
      echo "<div style=\"text-align: center;\">";
      echo "<textarea id=\"f_10_anschrift\" style=\"font-size:18px; font-weight:900;\" cols=\"40\" rows=\"2\" name=\"10_anschrift\">".$this->formdata["10_anschrift"] ;
      echo "</textarea></div>\n";
    }
    echo "</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 6,3   Gespr�chsnotiz    1024 11  Gespr�chsnotiz
    11_gesprnotiz
    \****************************************************************************/
    if (((($this->formdata["11_gesprnotiz"]) != "" )) or (!$this->feld[11])) {
      if ( $this->formdata["11_gesprnotiz"] ){$this->formdata["11_gesprnotiz"]= "on"; }
      echo "<input id=\"f_11_gesprnotiz\" type=\"hidden\" name=\"11_gesprnotiz\" value=\"".$this->formdata["11_gesprnotiz"]."\">\n";
      $param = " disabled ";}
    else {
      $param = "";}
    echo "<td style=\"width: 110px; background-color: ".$this->bg[11].";\">Gespr&auml;chsnotiz<br>\n";
    echo "<div style=\"text-align: center;\">";
    if ($this->formdata["11_gesprnotiz"] == "on") {$sel = "checked=\"checked\"";} else {$sel = "";}
    echo "<input id=\"f_11_gesprnotiz\" name=\"11_gesprnotiz\" type=\"checkbox\" ".$param.$sel."><br>\n";
    echo "</div>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=\"left\" valign=\"TOP\">\n";
    /****************************************************************************\
    // Zeile, Spalte 7,1  Inhalt   2048   12  Inhalt, Abfassungszeit
    12_inhalt
    \****************************************************************************/
    echo "<table style=\"text-align: left; width: 820px; height: 216px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    if  (!$this->feld[12]) {
      $param = " readonly ";}
    else {
      $param = "";}
    echo "<td valign=\"TOP\" style=\"background-color: ".$this->bg[12].";\">";
    echo "Inhalt/Text:";
    if ( $this->errorselect ["12_inhalt"] == false ){
      $this->showerrorinfo ("12_inhalt");
    }
    echo "<br>\n";
    if  ($this->feld[12]) {
      echo "<div style=\"text-align: center;\">";
      echo "<textarea id=\"f_12_inhalt\" style=\"font-size:18px; font-weight:900;\" cols=\"65\" rows=\"10\" name=\"12_inhalt\"".$param.">".$this->formdata["12_inhalt"];
      echo "</textarea></div>\n";
    } else {
      echo "<div style=\"text-align: left; font-size:18px; font-weight:400;\">"; //900
      echo "<input id=\"f_12_inhalt\" type=\"hidden\" name=\"12_inhalt\" value=\"".$this->formdata["12_inhalt"]."\">\n";
      echo nl2br ( $this->formdata["12_inhalt"]) ;
      echo "</div>";
    }
      // Sind Anhaege definiert? Wenn ja, anzeigen.
    if ($this->formdata["12_anhang"] != ""){
      echo "<input id=\"f_12_anhang\" type=\"hidden\" name=\"12_anhang\" value=\"".$this->formdata["12_anhang"]."\">\n";
      $this->list_anhang ();
    }
    echo "</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td style=\"text-align: left; background-color: ".$this->rbl_bg_color."; align=\"left\" valign=\"top\">\n";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; width: 820px; height: 35px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    /****************************************************************************\
    // Zeile, Spalte 8,1     2048 12  Inhalt, Abfassungszeit
    \****************************************************************************/
    echo "<td style=\"width: 125px; background-color: ".$this->bg[12].";\">Abfassungszeit:";
    if ( $this->errorselect ["12_abfzeit"] == false ){
      $this->showerrorinfo ("12_abfzeit");    }
    echo "</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 8,2     4096 13  Absender, Einheit
    12_abfzeit
    \****************************************************************************/
    echo "<td style=\"width: 600px; background-color: ".$this->bg[13].";\">\n";

    if (!$this->feld [12]){
      echo "<div style=\"text-align: left; font-size:24px; font-weight:900;\">";
      echo $this->formdata["12_abfzeit"] ;
      echo "<input id=\"f_12_abfzeit\" type=\"hidden\" name=\"12_abfzeit\" value=\"".$this->formdata["12_abfzeit"]."\">\n";
      echo "</div>\n";
    } else {
      echo "<input id=\"f_12_abfzeit\" maxlength=\"13\" size=\"13\" name=\"12_abfzeit\" value=\"".$this->formdata["12_abfzeit"]."\">";
    }
    echo "</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=\"left\" valign=\"top\">\n";
    echo "<table style=\"text-align: left; background-color: ".$this->rbl_bg_color."; width: 820px; height: 54px;\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    // Zeile, Spalte 9,1    4096  13  Absender, Einheit
    echo "<td style=\"width: 100px; background-color: ".$this->bg[13].";\">Absender";
    if ( $this->errorselect ["13_abseinheit"] == false ){
      $this->showerrorinfo ("13_abseinheit");    }
    echo "</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 9,2    8192  14  Zeichen Funktion
    13_abseinheit
    \****************************************************************************/
    echo "<td style=\"text-align: left; width: 200px; background-color: ".$this->bg[13].";\">\n";
    if (!$this->feld [13]){
      echo "<b><big>".$this->formdata["13_abseinheit"]."</big></b>" ;
      echo "<br>";
      echo "<input id=\"f_13_abseinheit\" type=\"hidden\" name=\"13_abseinheit\" value=\"".$this->formdata["13_abseinheit"]."\">\n";
    }
    else {
      echo "<div style=\"text-align: left;\" >";
      echo "<input id=\"f_13_abseinheit\" style=\"font-size:16px; font-weight:900;\" maxlength=\"30\" size=\"30\"
              name=\"13_abseinheit\" value=\"".$this->formdata["13_abseinheit"]."\">";
      echo "</div>\n";
    }
    echo "Einheit/Einrichtung/Stelle";
    echo "</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 9,3 Zeichen     8192 14  Zeichen Funktion
    14_zeichen
    \****************************************************************************/
    echo "<td style=\"width: 100px; background-color: ".$this->bg[14].";\">\n";
    if (!$this->feld [14]){
      echo "<b><big>".$this->formdata["14_zeichen"]."</big></b><br>" ;
      echo "<input id=\"f_14_zeichen\" type=\"hidden\" name=\"14_zeichen\" value=\"".$this->formdata["14_zeichen"]."\">\n";
    } else {
      echo "<input id=\"f_14_zeichen\" maxlength=\"25\" size=\"10\" name=\"14_zeichen\" value=\"".$this->formdata["14_zeichen"]."\"><br>\n";
    }
    echo "Zeichen</td>\n";
    /****************************************************************************\
    // Zeile, Spalte 9,4 Funktion    8192 14  Zeichen Funktion
    14_funktion
    \****************************************************************************/
    echo "<td style=\"width: 100px; background-color: ".$this->bg[14].";\">\n";
    if (!$this->feld [14]){
      echo "<b><big>".$this->formdata["14_funktion"]."</big></b><br>" ;
      echo "<input id=\"f_14_funktion\" type=\"hidden\" name=\"14_funktion\" value=\"".$this->formdata["14_funktion"]."\">\n";
    } else {
      echo "<input id=\"f_14_funktion\" maxlength=\"25\" size=\"10\" name=\"14_funktion\" value=\"".$this->formdata["14_funktion"]."\"".$param."><br>\n";
    }
    echo "Funktion</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    /*                                   I N H A L T
    ********************************************************************************************
    ********************************************************************************************
                                        S I C H T E R
    */
    echo "<tr>\n";
    echo "<td align=\"left\" valign=\"top\">\n";
    echo "<table style=\"text-align: left; width: 820px; height: 229px; background-color: ".$this->rbl_bg_color.";\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    echo "<td style=\"width: 415px; background-color: ".$this->bg[15].";\">\n";
    echo "<table style=\"text-align: left; width: 418px; height: 65px;\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    echo "<tr>\n";
    /****************************************************************************\
    // Zeile, Spalte 10,1 Quittung     16384  15  Quittung Sichter
    15_quitdatum
    15_quitzeichen
    \****************************************************************************/
    echo "<td style=\"width: 109px; background-color: ".$this->bg[15].";\">Quittung:";
    echo "</td>\n";
    echo "<td style=\"width: 289px; background-color: ".$this->bg[15].";\">\n";
    if (!$this->feld [15]){
      echo "<div style=\"text-align: left;\">";
      echo $this->formdata["15_quitdatum"]."&nbsp;&nbsp;".$this->formdata["15_quitzeichen"];
      echo "</div>\n";
    } else {
    if ( $this->errorselect ["15_quitdatum"] == false ){
      $this->showerrorinfo ("15_quitdatum");    }
    echo "<input id=\"f_15_quitdatum\" maxlength=\"13\" size=\"13\" name=\"15_quitdatum\" value=\"".$this->formdata["15_quitdatum"]."\">&nbsp;\n";
    if ( $this->errorselect ["15_quitzeichen"] == false ){
      $this->showerrorinfo ("15_quitzeichen");    }
    echo "<input id=\"f_15_quitzeichen\" maxlength=\"3\" size=\"3\" name=\"15_quitzeichen\" value=\"".$this->formdata["15_quitzeichen"]."\"><br>\n";
    }
    echo "&nbsp;Uhrzeit &nbsp; &nbsp;Zeichen</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "<table style=\"text-align: left; width: 450px; height: 144px; background-color: ".$this->rbl_bg_color.";\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
    echo "<tbody>\n";
    /****************************************************************************\
    // Zeile, Spalte 11,1   32768 16  Ziele
    16_empf
    \****************************************************************************/
    if ((!$this->feld[16])) {
      $param = " disabled ";}
    else {
      $param = "";}
    switch ($this->task) {
      case "SI-Admin":
      case "FM-Eingang_Sichter":
      case "Stab_sichten":
      case "Stab_gesprnoti":
      case "FM-Eingang_Anhang_Sichter":
      case "FM-Admin":
      case "FM-Ausgang_Sichter":
      case "SI-Admin":
        for ($m=1; $m<=5; $m++){ // Zeilen
          echo "<tr>";
          for ($n=1; $n<=4; $n++){  // Spalten
            // rote Kopie geht an...
            if ( ( $this->empfarray [$m][$n]["fkt"] == $this->redcopy2 ) and
                 ( $this->feld[16]) ) { // Wenn Sichter aktiv und rote Kopie
              echo "<td style=\"width: 75px; background-color: rgb(255,0,0);\">";
            }else{
              echo "<td style=\"width: 75px; background-color: ".$this->bg[16].";\">";
            }

            switch ($this->empfarray [$m][$n]["typ"]){
              case "cb":
                if ( $this->empfarray [$m][$n]["fkt"] == $this->redcopy2 ) {
                  $red_inactiv = " disabled ";
                } else {
                  $red_inactiv = " ";
                }

                if ( ( $this->empfarray [$m][$n]["checked"]) and
                     ( $this->empfarray [$m][$n]["cpycol"] == "gn" ) ) {
                  $selcbgn = "checked=\"checked\"";} else {$selcbgn = "";}

                if ( ( $this->empfarray [$m][$n]["checked"]) and
                     ( $this->empfarray [$m][$n]["cpycol"] == "bl" ) ) {
                  $selcbbl = "checked=\"checked\"";} else {$selcbbl = "";}

                echo "<a style=\"background-color:#00B000;\">
                      <input name=\"16_gncopy\" type=\"radio\" ".$selcbgn.$red_inactiv." value=\"16_".$m.$n."_gn\">\n";

                echo "<a style=\"background-color:#0303FD;\">
                      <input name=\"16_".$m.$n."\" value=\"16_".$m.$n."_bl\" type=\"checkbox\" ".$param.$selcbbl.$red_inactiv.">\n</a>";

                echo $this->empfarray [$m][$n]["fkt"] ;

              break;

              case "t":
                if ($this->empfarray [$m][$n]["fkt"] != ""){
                  echo $this->empfarray [$m][$n]["fkt"] ;
                } else {
                  echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
              break;

              case "cbt":
                if ($this->empfarray [$m][$n]["checked"]) {$selcb = "checked=\"checked\"";} else {$selcb = "";}
                echo "<a style=\"background-color:#00B000;\">
                      <input name=\"16_".$m.$n."\" value=\"16_".$m.$n."\" type=\"checkbox\" ".$param.$sel."></a>\n";
                echo "<input maxlength=\"8\" size=\"8\" value=\"".$this->empfarray [$m][$n]["fkt"]."\" name=\"16_empf_sonst_".$m.$n."\" ".$param."></td>\n";
              break;
            }
          } // for $n
          echo "</tr>\n";
        } // for $m
      break;

      case "Stab_lesen":
      case "Stab_schreiben":
      case "FM-Ausgang":
      case "FM-Eingang":
      case "FM-Eingang_Anhang":

    for ($m=1; $m<=5; $m++){ // Zeilen
      echo "<tr>";
      for ($n=1; $n<=4; $n++){  // Spalten
        echo "<td style=\"width: 75px; background-color: ".$this->bg[16].";\">";
        switch ($this->empfarray [$m][$n]["typ"]){

          case "cb":
            if ($this->empfarray [$m][$n]["checked"]) {$sel = "checked=\"checked\"";} else {$sel = "";}
            echo "<input name=\"16_".$m.$n."\" value=\"16_".$m.$n."\" type=\"checkbox\" ".$param.$sel.">\n";
            echo $this->empfarray [$m][$n]["fkt"] ;
          break;

          case "t":
            if ($this->empfarray [$m][$n]["fkt"] != ""){
              echo $this->empfarray [$m][$n]["fkt"] ;
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
          break;

          case "cbt":
            if ($this->empfarray [$m][$n]["checked"]) {$sel = "checked=\"checked\"";} else {$sel = "";}
            echo "<a style=\"background-color:#00B000;\">
                  <input name=\"16_".$m.$n."\" value=\"16_".$m.$n."\" type=\"checkbox\" ".$param.$sel."></a>\n";
            echo "<input maxlength=\"8\" size=\"8\" value=\"".$this->empfarray [$m][$n]["fkt"]."\" name=\"16_empf_sonst_".$m.$n."\" ".$param."></td>\n";
          break;
        }

        echo "</td>\n";
      } // for $n
      echo "</tr>\n";
    } // for $m
   break;

    } // switsch $this->task

    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";

    /****************************************************************************\
    // Zeile, Spalte 10,2  Vermerke      65536    17  Vermerke
    17_vermerke
    \****************************************************************************/
    echo "<td  valign=\"TOP\" style=\"text-align: left; width: 350px; background-color: ".$this->bg[17].";\">Vermerke:<br>\n";

    if (((($this->formdata["17_vermerke"]) != "" )) or (!$this->feld[17])) {
      echo $this->formdata["17_vermerke"];
    } else {
      echo "<textarea cols=\"40\" rows=\"10\" name=\"17_vermerke\" ".$param.">".$this->formdata["17_vermerke"]."</textarea>";
    }

    echo "</td>\n";

    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</tbody>\n";
    echo "</table>\n";

    $this->show_menue_buttons (2, "unten");

    echo "</FORM>\n";

  } // function plot_form

} // class

?>
