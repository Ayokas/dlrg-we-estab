<?php
/*****************************************************************************\
   Datei: data_hndl.php

   benoetigte Dateien:

   Beschreibung:

   Funktionen:

     check_save_user ()
     check_and_save ($data)
     legere_nuntium ($krzl, $fktn, $lfd);
         ==> Zeit wann die Nachricht gelesen wurde,
         oder auch nicht!
   (C) Hajo Landmesser IuK Kreis Heinsberg
   mailto://hajo.landmesser@iuk-heinsberg.de
\*****************************************************************************/

define ("validate",true);     // Soll das Formular �berpr�ft werden

include ("tools.php");

if (validate){
  include ("vali_data.php");
}

/*******************************************************************************
  Benutzeranmeldung Cookies setzen und eintrag in die Datenbank
  1. Sind Cookiedaten vorhanden
     JA   --> Pruefe Cookiedaten mit Datenbankeintraege
          --> Datenabgleich
     NEIN --> Neueintrag Datenbank und COOKIES
********************************************************************************/



/*******************************************************************************\
    Funktion:  check_save_user ()

\*******************************************************************************/
function check_save_user () {
  $error_userlogin = false;
  if (debug) echo "data_hndl.php 44<br>";
  // Als allererstes Pruefen wir mal die Formulardaten auf Vollstaedigkeit
  if ($_GET ["kennwort1"] != "" ){
    if ( ( $_GET [kuerzel] != "" ) AND ( $_GET [benutzer] != ""  ) ) {
      include ("../4fcfg/dbcfg.inc.php");
      include ("../4fcfg/e_cfg.inc.php");
      if (debug) echo "PHP_SELF=".$_SERVER["PHP_SELF"]."<br>";
       /* Die Daten in der Datenbank vorhanden?
          Also suchen wird erst mal nach dem Kuerzel in der Datenbank  */
      $dbaccess = new db_access ($conf_4f_db  ["server"],
                                 $conf_4f_db  ["datenbank"],
                                 $conf_4f_tbl ["benutzer"],
                                 $conf_4f_db  ["user"],
                                 $conf_4f_db  ["password"] );

      if (debug) {echo "db-daten 59 "; var_dump ($conf_4f_db); echo "<br>";}
      // Daten sind in $_GET vorhanden
      $GETkuerzel = strtolower ( $_GET ["kuerzel"]);

      $query = "SELECT * FROM ".$conf_4f_tbl ["benutzer"]." WHERE `kuerzel` LIKE \"".$GETkuerzel."\";";
      if (debug) {echo "data_hndl.php 64 query=".$query."<br>";}

      $result = $dbaccess->query_table ($query);
      if (debug) {echo "result67 ="; var_dump ($result); echo"<br>";}

      if ( ( count ($result) > 0 ) AND ( $result != "" ) ){
        $db_result = $result [1];
        $user_eq = ( $_GET["benutzer"] == $db_result ["benutzer"] );
        $kuerzel_eq = ( $GETkuerzel == $db_result ["kuerzel"] );
        $passwd_eq = ( $_GET["kennwort1"] == $db_result ["password"] );

        $db_gleich  = ( $user_eq  AND $kuerzel_eq AND $passwd_eq);
        $sd_gleich  = ( ( $_SESSION ["vStab_benutzer"] == $db_result [1]["benutzer"] ) AND
                        ( $_SESSION ["vStab_kuerzel"]  == $db_result [1]["kuerzel"] ) AND
                        ( $_SESSION ["vStab_funktion"] == $db_result [1]["benutzer"] )  );

        $sid_gleich = ( ( session_id() == $db_result ["sid"] ));
        $ip_gleich   = ( ( $_SERVER [REMOTE_ADDR] == $db_result ["ip"] ));

        if (  $db_gleich  ) {
          /*** Wiederanmeldung ***/
          if ($db_result ["aktiv"] == 1 ){
            $query = "UPDATE ".$conf_4f_tbl ["benutzer"]."
                      SET   `SID` = \"".session_id()."\",
                             `ip` = \"".$_SERVER ["REMOTE_ADDR"]."\",
                          `fwdip` = \"".$_SERVER["HTTP_X_FORWARDED_FOR"]."\",
                          `aktiv` = \"1\" WHERE `kuerzel` = \"".$GETkuerzel."\";";
            $result = $dbaccess->query_table_iu ($query);
            $_SESSION ["menue"] = "ROLLE";  // Starte Menue im Rollenmodus
            $rolle = rollenfinder ( $_GET["funktion"] );
            $_SESSION ["vStab_benutzer"] = $_GET["benutzer"];
            $_SESSION ["vStab_kuerzel"]  = $GETkuerzel;
            $_SESSION ["vStab_funktion"] = $_GET["funktion"];
            $_SESSION ["vStab_rolle"]    = $rolle;
            $_SESSION ["menue"] = "ROLLE";  // Starte Menu im Rollenmodus
            $_SESSION ["ROLLE"] = $rolle;
            protokolleintrag ("Sessiondaten neu setzen", $_SESSION["vStab_benutzer"].";".$_SESSION["vStab_kuerzel"].";".$_SESSION["vStab_funktion"].";".$_SESSION["vStab_rolle"].";".session_id().";".$_SERVER["REMOTE_ADDR"]);
          }
          if ($db_result ["aktiv"] == 0 ){
            $rolle = rollenfinder ( $_GET["funktion"] );
            $query = "UPDATE ".$conf_4f_tbl ["benutzer"]."
                     SET `funktion` = \"".$_GET ["funktion"]."\",
                         `rolle`    = \"".$rolle."\",
                         `SID`      = \"".session_id()."\",
                         `ip`       = \"".$_SERVER ["REMOTE_ADDR"]."\",
                         `fwdip`    = \"".$_SERVER["HTTP_X_FORWARDED_FOR"]."\",
                         `aktiv`    = \"1\" WHERE kuerzel = \"".$GETkuerzel."\";";
            $result = $dbaccess->query_table_iu ($query);
             // Tabelle fuer die Benutzerfunktion anlegen
            if ($_GET ["funktion"] != "A/W"){
              $usertablename = $conf_4f_tbl ["usrtblprefix"].strtolower ($_GET ["funktion"])."_".strtolower ( $_GET ["kuerzel"]);
              $fkttblname  = $conf_4f_tbl ["usrtblprefix"]."_fkt_".strtolower ($_GET ["funktion"]);
              $dbaccess->create_user_table ($usertablename, $fkttblname);
            }
            $rolle = rollenfinder ( $_GET["funktion"] );
            $_SESSION ["ROLLE"] = $rolle;
            $_SESSION ["vStab_benutzer"] = $_GET["benutzer"];
            $_SESSION ["vStab_kuerzel"]  = $GETkuerzel;
            $_SESSION ["vStab_funktion"] = $_GET["funktion"];
            $_SESSION ["vStab_rolle"]    = $rolle;
            $_SESSION ["menue"] = "ROLLE";  // Starte Menu im Rollenmodus
            $_SESSION ["ROLLE"] = $rolle;
            protokolleintrag ("Funktion Ummelden", $_SESSION["vStab_benutzer"].";".$_SESSION["vStab_kuerzel"].";".$_SESSION["vStab_funktion"].";".$_SESSION["vStab_rolle"].";".session_id().";".$_SERVER["REMOTE_ADDR"]);
          }
        } ELSE { // $db_gleich
          if (!$passwd_eq){
            $infotext = "Passwort falsch !!<br>Das Passwort stimmen nicht �berein.";
            errorwindow( "Benutzeranmeldung", $infotext );
            $error_userlogin = true;
          }
        }
        if ($kuerzel_eq and !$user_eq) {
          // K�rzel in Datenbank vorhanden -- Benutzername passt NICHT dazu !!!
          $infotext = "K�rzel schon vorhanden !!!<br>Benutzername stimmt nicht mit den gespeicherten Daten �berein.";
          errorwindow( "Benutzeranmeldung", $infotext );
          $error_userlogin = true;
        }
      }  else { // nicht in der Datenbank
           /**********************************************************************
                     Es sind keine Daten in der Datenbank ==> Neuer Benutzer
                     Setze die Daten im Session Cookie und in der Datenbank.
            **********************************************************************/
          if (debug) {echo "data_hndl146"."<br>";}
          $rolle = rollenfinder ( $_GET["funktion"] );
          if (debug) {echo "data_hndl148"."<br>";}
          $_SESSION ["vStab_benutzer"] = $_GET["benutzer"];
          $_SESSION ["vStab_kuerzel"]  = $GETkuerzel;
          $_SESSION ["vStab_funktion"] = $_GET["funktion"];
          $_SESSION ["vStab_rolle"]    = $rolle;

          $query = "INSERT into ".$conf_4f_tbl ["benutzer"]." SET
                          `benutzer` = \"".$_GET["benutzer"]."\",
                          `kuerzel`  = \"".$GETkuerzel."\",
                          `funktion` = \"".$_GET["funktion"]."\",
                          `rolle`    = \"".$rolle."\",
                          `sid`      = \"".session_id()  ."\",
                          `ip`       = \"".$_SERVER["REMOTE_ADDR"]."\",
                          `fwdip`    = \"".$_SERVER["HTTP_X_FORWARDED_FOR"]."\",
                          `password` = \"".$_GET["kennwort1"]."\",
                          `aktiv`    = \"1\"";
          if (debug) {echo "data_hndl.php 163 query=".$query."<br>";}
          $result = $dbaccess->query_table_iu ($query);
          if (debug) {echo "data_hndl165"; var_dump ($result); echo "<br>"; }

          protokolleintrag ("Anmelden", $_SESSION["vStab_benutzer"].";".$_SESSION["vStab_kuerzel"].";".$_SESSION["vStab_funktion"].";".$_SESSION["vStab_rolle"].";".session_id().";".$_SERVER["REMOTE_ADDR"]);
          if ($_SESSION ["vStab_funktion"] != "A/W"){
            $usertablename = $conf_4f_tbl ["usrtblprefix"].strtolower ($_GET ["funktion"])."_".strtolower ( $_GET ["kuerzel"]);
            $fkttblname  = $conf_4f_tbl ["usrtblprefix"]."_fkt_".strtolower ($_SESSION["vStab_funktion"]);
            $dbaccess->create_user_table ($usertablename, $fkttblname);
          }
          $_SESSION ["menue"] = "ROLLE";  // Starte Menu im Rollenmodus
          $_SESSION ["ROLLE"] = $rolle;
      } // ( ( count ($result) > 0 ) AND ( $result != "" ) ){
    }  else {  // if $GET [kuerzel und benutzer] == ""
      $_SESSION ["menue"] = "LOGIN";
      $infotext = "Keine Daten eingegeben !!!";
      errorwindow( "Benutzeranmeldung", $infotext );
      $error_userlogin = true;
    }
  } else { // kennwort == ""
     $error_userlogin = true;
  }

  return ($error_userlogin);

} // function save_user


/*****************************************************************************\

\*****************************************************************************/
function check_and_save ($data){

  include ("../4fcfg/config.inc.php");
  include ("../4fcfg/dbcfg.inc.php");
  include ("../4fcfg/e_cfg.inc.php");
  include ("../4fcfg/fkt_rolle.inc.php");

  if ($data ["11_gesprnotiz"] == "on") {
    $data ["11_gesprnotiz"] = "t" ;
  }  else {
    $data ["11_gesprnotiz"] = "f" ;
  }

  if (debug){
    echo "256 check_and_save --->   "; var_dump ($data);
    echo "<br><br><br>\n";
    while (list($key, $val) = each($data)) {
       echo "$key => $val  ---> $data[$key]<br>\n";
    }
  }

// Umwandlung Sonderzeichen in HTML Zeichencode
  if ($data ["12_inhalt"] != ""){
    $data ["12_inhalt"] = htmlentities (  $data ["12_inhalt"] ); }
  if ($data ["17_vermerk"] != ""){
    $data ["17_vermerke"] = htmlentities (  $data ["17_vermerke"] ); }
  $dbaccess = new db_access ($conf_4f_db ["server"], $conf_4f_db ["datenbank"],
                             $conf_4f_tbl ["benutzer"], $conf_4f_db ["user"],
                             $conf_4f_db ["password"] );

  switch ($data["task"]){

    case "FM-Eingang":
    case "FM-Eingang_Anhang":
       /*****************************************************************************************************
           Betroffene Felder:
            01_medium            01_datum   TTMM            01_zeit    SSMM            01_zeichen            05_gegenstelle            07_durchspruch;            08_befhinweis;
            08_befhinwausw;            09_vorrangstufe;            10_anschrift;            11_gesprnotiz;            12_inhalt;            12_abfzeit;            13_abseinheit;
            14_zeichen;            14_funktion;
          Workflow ==>
            Ergaenzung Nachweisdaten (E und Nachweisnummer) 04_richtung 04_nummer
            Daten in Datenbank mit einem INSERT
            INSERT INTO tabelle SET spalten_name=ausdruck, spalten_name=ausdruck, ...
      ******************************************************************************************************/
       $data ["16_empf"] .= $redcopy2."_rt,";

       if ($data ["01_datum"] == "" ) { $data ["01_datum"] = date ("Hi") ; }
       if ($data ["12_abfzeit"] == "" ) { $data ["12_abfzeit"] = date ("Hi") ; }

      if (validate){
         /*----------------------------------------------------*/
        if (debug){
        echo "DATAHNDL286=";
        var_dump ($data); echo "<br>";
        }
        $vali = new vali_data_form ( $data ) ;
        $result = $vali->validatethis (); //checkdata ();
        if (debug){
          echo "DATAHNDL292=";
          echo "<b>RESULT</b>";
          var_dump ($result); echo "<br>";
          echo "<b>vali-data</b>";
          var_dump ($vali->i_data); echo "<br>";
          echo "<b>DATA</b>";
          var_dump ($data); echo "<br>";
          echo "<b>vali-VALIDATE</b>";
          var_dump ($vali->validate); echo "<br>";
        }

        $data = $vali->i_data ;

        if (!$result) {
          $form = new nachrichten4fach ($data, $data["task"], $vali->validate);
          exit ;
        }
        /*----------------------------------------------------*/
      }


       $nachweis_E = get_last_nachw_num ("E") + 1;
       $query = "INSERT into `".$conf_4f_tbl ["nachrichten"]."` SET
            `01_medium`       = \"".$data ["01_medium"]      ."\",
            `01_datum`        = \"".konv_taktime_datetime ($data ["01_datum"])."\",
            `01_zeichen`      = \"".$data ["01_zeichen"]     ."\",
            `04_richtung`     = \"E\",
            `04_nummer`       = \"".$nachweis_E              ."\",
            `05_gegenstelle`  = \"".$data ["05_gegenstelle"] ."\",
            `07_durchspruch`  = \"".$data ["07_durchspruch"] ."\",
            `08_befhinweis`   = \"".$data ["08_befhinweis"]  ."\",
            `08_befhinwausw`  = \"".$data ["08_befhinwausw"] ."\",
            `09_vorrangstufe` = \"".$data ["09_vorrangstufe"]."\",
            `10_anschrift`    = \"".$data ["10_anschrift"]   ."\",
            `11_gesprnotiz`   = \"".$data ["11_gesprnotiz"]  ."\",
            `12_anhang`       = \"".$data ["12_anhang"]      ."\",
            `12_inhalt`       = \"".$data ["12_inhalt"]      ."\",
            `12_abfzeit`      = \"".konv_taktime_datetime ($data ["12_abfzeit"])     ."\",
            `13_abseinheit`   = \"".$data ["13_abseinheit"]  ."\",
            `14_zeichen`      = \"".$data ["14_zeichen"]     ."\",
            `14_funktion`     = \"".$data ["14_funktion"]."\",
            `16_empf`         = \"".$data ["16_empf"]."\",
            `x00_status`      = \"4\",
            `x01_abschluss`   = \"f\"";

        if (debug){  echo "query[FM-Eingang]===".$query."<br>";}

        $result = $dbaccess->query_table_iu ($query);

        protokolleintrag ("FM-Eingang",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    break;

    case "FM-Eingang_Sichter":
    case "FM-Eingang_Anhang_Sichter" :

       /*****************************************************************************************************
           Betroffene Felder:
            01_medium            01_datum   TTMM            01_zeit    SSMM            01_zeichen            05_gegenstelle            07_durchspruch;            08_befhinweis;
            08_befhinwausw;            09_vorrangstufe;            10_anschrift;            11_gesprnotiz;            12_inhalt;            12_abfzeit;            13_abseinheit;
            14_zeichen;            14_funktion;             15_quitdatum;          15_quitzeichen;          16_empf;          17_vermerke;
        Workflow ==>
            Ergaenzung Nachweisdaten (E und Nachweisnummer) 04_richtung 04_numme
            Daten in Datenbank mit einem INSERT
            INSERT INTO tabelle SET spalten_name=ausdruck, spalten_name=ausdruck, ...
      ******************************************************************************************************/
       $data ["16_empf"] = $redcopy2."_rt,";

       for (  $i = 1 ; $i <= 5 ; $i++ ){
         for ( $j = 1 ; $j <= 5 ; $j++ ){
           if ( isset ( $data ["16_".$i.$j] ) ) {
             list ($ord, $pos, $fkt) = explode ("_", $data ["16_".$i.$j]);
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_".$fkt.",";
           }
           if ( $data ["16_gncopy"] == "16_".$i.$j."_gn" ) {
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_gn,";
           }
         }
       }

       if ($data ["01_datum"] == "" ) { $data ["01_datum"] = date ("Hi") ; }
       if ($data ["12_abfzeit"] == "" ) { $data ["12_abfzeit"] = date ("Hi") ; }
       if ($data ["15_quitdatum"] == "" ) { $data ["15_quitdatum"] = date ("Hi") ; }

        if (validate){
           /*----------------------------------------------------*/
          if (debug){
            echo "DATAHNDL368=";
            var_dump ($data); echo "<br>";
          }
          $vali = new vali_data_form ( $data ) ;
          $result = $vali->validatethis (); //checkdata ();
          if (debug){
            echo "DATAHNDL374=";
            echo "<b>RESULT</b>";
            var_dump ($result); echo "<br>";

            echo "<b>vali-data</b>";
            var_dump ($vali->i_data); echo "<br>";

            echo "<b>vali-VALIDATE</b>";
            var_dump ($vali->validate); echo "<br>";
          }
          $data = $vali->i_data ;
          if (debug){
            echo "<b>DATA</b>";
            var_dump ($data); echo "<br>";
          }
          if (!$result) {
           $form = new nachrichten4fach ($data, $data["task"], $vali->validate);
           exit ;
          }
          /*----------------------------------------------------*/
        }

       $nachweis_E = get_last_nachw_num ("E") + 1;
       $query = "INSERT into `".$conf_4f_tbl ["nachrichten"]."` SET
            `01_medium`       = \"".$data ["01_medium"]      ."\",
            `01_datum`        = \"".konv_taktime_datetime ($data ["01_datum"])."\",
            `01_zeichen`      = \"".$data ["01_zeichen"]     ."\",
            `04_richtung`     = \"E\",
            `04_nummer`       = \"".$nachweis_E              ."\",
            `05_gegenstelle`  = \"".$data ["05_gegenstelle"] ."\",
            `07_durchspruch`  = \"".$data ["07_durchspruch"] ."\",
            `08_befhinweis`   = \"".$data ["08_befhinweis"]  ."\",
            `08_befhinwausw`  = \"".$data ["08_befhinwausw"] ."\",
            `09_vorrangstufe` = \"".$data ["09_vorrangstufe"]."\",
            `10_anschrift`    = \"".$data ["10_anschrift"]   ."\",
            `11_gesprnotiz`   = \"".$data ["11_gesprnotiz"]  ."\",
            `12_anhang`       = \"".$data ["12_anhang"]      ."\",
            `12_inhalt`       = \"".$data ["12_inhalt"]      ."\",
            `12_abfzeit`      = \"".konv_taktime_datetime ($data ["12_abfzeit"])     ."\",
            `13_abseinheit`   = \"".$data ["13_abseinheit"]  ."\",
            `14_zeichen`      = \"".$data ["14_zeichen"]     ."\",
            `14_funktion`     = \"".$data ["14_funktion"]    ."\",
            `15_quitdatum`    = \"".konv_taktime_datetime ($data ["15_quitdatum"])   ."\",
            `15_quitzeichen`  =  \"".$data ["15_quitzeichen"]."\",
            `16_empf`         =  \"".$data ["16_empf"]."\",
            `17_vermerke`     =  \"".$data ["17_vermerke"]."\",
            `x00_status`      = \"8\",
            `x01_abschluss`   = \"t\"";

       if (debug){
         echo "query[FM-Eingang_Sichter]===".$query."<br>";
       }
       $result = $dbaccess->query_table_iu ($query);
       protokolleintrag ("FM-Eingang-Sichter",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    break;

    case "Stab_schreiben":

/*          07_durchspruch;          08_befhinweis;          08_befhinwausw;          09_vorrangstufe;          10_anschrift;          11_gesprnotiz;          12_inhalt;
          12_abfzeit;          13_abseinheit;          14_zeichen;          14_funktion;
          Workflow ==>
            Ergaenzung Nachweisdaten (A und Nachweisnummer) 04_richtung 04_nummer
            Daten in Datenbank mit einem INSERT
            INSERT INTO tabelle SET spalten_name=ausdruck, spalten_name=ausdruck, ...
*/


      if ($data ["12_abfzeit"] == "" ) { $data ["12_abfzeit"] = date ("Hi") ; }

      if (validate){
         /*----------------------------------------------------*/
        if (debug){ echo "DATAHNDL506="; var_dump ($data); echo "<br><br>";
        }
        $vali = new vali_data_form ( $data ) ;
        $result = $vali->validatethis (); //checkdata ();
        if (debug){
            echo "<b>DATA</b>"; var_dump ($data); echo "<br>";
          echo "DATAHNDL 453="; echo "<b>RESULT</b>"; var_dump ($result); echo "<br><br>";
          echo "<b>vali-data</b>"; var_dump ($vali->i_data); echo "<br><br>";
          echo "<b>vali-VALIDATE</b>"; var_dump ($vali->validate); echo "<br>";
        }

        $data = $vali->i_data ;

         if (!$result) {
           $form = new nachrichten4fach ($data, $data["task"], $vali->validate);
           exit ;
         }
         /*----------------------------------------------------*/
      }

      if ($data ["02_zeit"] == "" ) {
        $data ["02_zeit"] = convtodatetime ( date ("dm"),   date ("Hi") )  ;
      }


       $data ["16_empf"] = $redcopy2."_rt,".$data ["14_funktion"]."_gn"; // Der Verfasser bekommt den gruenen
       $gesprnotiz_or_not = "`x00_status`      = \"10\",
                `04_richtung`     = \"A\",
                `x01_abschluss`   = \"f\"";
       $nachweis_A       = get_last_nachw_num ("A") + 1;
       $query = "INSERT into `".$conf_4f_tbl ["nachrichten"]."` SET
            `02_zeit`         = \"".$data ["02_zeit"]    ."\",
            `02_zeichen`      = \"\",
            `04_nummer`       = \"".$nachweis_A              ."\",
            `04_richtung`     = \"A\",
            `07_durchspruch`  = \"".$data ["07_durchspruch"] ."\",
            `08_befhinweis`   = \"".$data ["08_befhinweis"]  ."\",
            `08_befhinwausw`  = \"".$data ["08_befhinwausw"] ."\",
            `09_vorrangstufe` = \"".$data ["09_vorrangstufe"]."\",
            `10_anschrift`    = \"".$data ["10_anschrift"]   ."\",
            `11_gesprnotiz`   = \"".$data ["11_gesprnotiz"]  ."\",
            `12_anhang`       = \"".$data ["12_anhang"]      ."\",
            `12_inhalt`       = \"".$data ["12_inhalt"]      ."\",
            `12_abfzeit`      = \"".konv_taktime_datetime ($data ["12_abfzeit"])    ."\",
            `13_abseinheit`   = \"".$data ["13_abseinheit"]  ."\",
            `14_zeichen`      = \"".$data ["14_zeichen"]     ."\",
            `14_funktion`     = \"".$data ["14_funktion"]    ."\",
            `16_empf`         = \"".$data ["16_empf"]."\",
            `x00_status`      = \"2\",
            `x01_abschluss`   = \"f\"; ";

       if (debug) { echo "datahndl.php 464 ==>query[Stab schreiben]===".$query."<br>";}

       $result = $dbaccess->query_table_iu ($query);
       protokolleintrag ("Stab-schreiben",$query);
       $query = "SELECT ".$conf_4f_tbl ["nachrichten"].".`00_lfd` FROM `".$conf_4f_tbl ["nachrichten"]."`
                 WHERE `04_nummer` = \"".$nachweis_A."\" AND `04_richtung` = \"A\" ;";
//echo "query[Stab schreiben2]===".$query."<br>";

       $result = $dbaccess->query_table_wert ($query);
       $lfd = $result [0] ;
       set_msg_read ($lfd) ;
    break;
    /****************************************************************************\
      SSSSS TTTTT AAAAA BBBB        GGGGG EEEEE SSSSS PPPP  RRRR  N   N  OOO  TTTTT IIIII
      S       T   A   A B   B       G     E     S     P   P R   R NN  N O   O   T     I
      SSSSS   T   AAAAA BBBB        G GGG EEEE  SSSSS PPPP  RRRR  N N N O   O   T     I
          S   T   A   A B   B       G   G E         S P     R  R  N  NN O   O   T     I
      SSSSS   T   A   A BBBB  _____ GGGG  EEEEE SSSSS P     R   R N   N  OOO    T   IIIII
    \****************************************************************************/
    case "Stab_gesprnoti":
      if ($data ["01_datum"] == "" )     { $data ["01_datum"]     = date ("Hi") ; }
      if ($data ["12_abfzeit"] == "" )   { $data ["12_abfzeit"]   = date ("Hi") ; }
      if ($data ["15_quitdatum"] == "" ) { $data ["15_quitdatum"] = date ("Hi") ; }

      if (validate){
         /*----------------------------------------------------*/
        if (debug){
          echo "DATAHNDL532====";
          var_dump ($data); echo "<br><br>";
        }

        $vali = new vali_data_form ( $data ) ;
        $result = $vali->validatethis (); //checkdata ();

        if (debug){
          echo "<b>DATA</b>";
          var_dump ($data); echo "<br>";

          echo "DATAHNDL512=";
          echo "<b>RESULT</b>";
          var_dump ($result); echo "<br><br>";

          echo "<b>vali-data</b>";
          var_dump ($vali->i_data); echo "<br><br>";

          echo "<b>vali-VALIDATE</b>";
          var_dump ($vali->validate); echo "<br>";
        }

        $data = $vali->i_data ;

        if (!$result) {
          $form = new nachrichten4fach ($data, $data["task"], $vali->validate);
        exit ;
         }
         /*----------------------------------------------------*/
      }

      if ($data ["02_zeit"] == "" ) {
        $data ["02_zeit"] = convtodatetime ( date ("dm"),   date ("Hi") )  ;
      }

       for (  $i = 1 ; $i <= 5 ; $i++ ){
         for ( $j = 1 ; $j <= 5 ; $j++ ){
           if ( isset ( $data ["16_".$i.$j] ) ) {
             list ($ord, $pos, $fkt) = explode ("_", $data ["16_".$i.$j]);
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_".$fkt.",";
           }
           if ( $data ["16_gncopy"] == "16_".$i.$j."_gn" ) {
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_gn,";
           }
         }
       }
       $data ["11_gesprnotiz"] = "t" ;
       $nachweis_E     = get_last_nachw_num ("E") + 1; // E weil Gspraechsnotiz als Eingang
       $data ["16_empf"] .= $redcopy2."_rt,".$data ["14_funktion"]."_gn"; // Der Verfasser bekommt den gruenen
       $query = "INSERT into `".$conf_4f_tbl ["nachrichten"]."` SET
            `01_medium`       = \"".$data ["01_medium"]      ."\",
            `01_datum`        = \"".konv_taktime_datetime ($data ["01_datum"])."\",
            `01_zeichen`      = \"".$data ["01_zeichen"]     ."\",
            `04_nummer`       = \"".$nachweis_E              ."\",
            `04_richtung`     = \"E\",
            `07_durchspruch`  = \"".$data ["07_durchspruch"] ."\",
            `08_befhinweis`   = \"".$data ["08_befhinweis"]  ."\",
            `08_befhinwausw`  = \"".$data ["08_befhinwausw"] ."\",
            `09_vorrangstufe` = \"".$data ["09_vorrangstufe"]."\",
            `10_anschrift`    = \"".$data ["10_anschrift"]   ."\",
            `11_gesprnotiz`   = \"".$data ["11_gesprnotiz"]  ."\",
            `12_anhang`       = \"".$data ["12_anhang"]      ."\",
            `12_inhalt`       = \"".$data ["12_inhalt"]      ."\",
            `12_abfzeit`      = \"".konv_taktime_datetime ($data ["12_abfzeit"])     ."\",

            `13_abseinheit`   = \"".$data ["13_abseinheit"]  ."\",
            `14_zeichen`      = \"".$data ["14_zeichen"]     ."\",
            `14_funktion`     = \"".$data ["14_funktion"]    ."\",
            `16_empf`         = \"".$data ["16_empf"]        ."\",
            `17_vermerke`     =  \"".$data ["17_vermerke"]."\",

            `x00_status`      = \"8\",
            `x01_abschluss`   = \"t\",
            `x02_sperre`      = \"f\",
            `x03_sperruser`   = \"\" ";
/*
            `15_quitdatum`    = \"".konv_taktime_datetime ($data ["15_quitdatum"])   ."\",
            `15_quitzeichen`  =  \"".$data ["15_quitzeichen"]."\",

*/

       $result = $dbaccess->query_table_iu ($query);
       protokolleintrag ("Stab-gesprnoti",$query);
       $query = "SELECT ".$conf_4f_tbl ["nachrichten"].".`00_lfd` FROM `".$conf_4f_tbl ["nachrichten"]."`
                 WHERE `04_nummer` = \"".$nachweis_E."\" AND `04_richtung` = \"E\" ;";
       $result = $dbaccess->query_table_wert ($query);
       $lfd = $result [0];
       set_msg_read ($lfd) ;

    break;

    case "FM-Ausgang":

      if ($data ["03_datum"] == "" ) { $data ["03_datum"] = date ("Hi") ; }


      if (validate){
         /*----------------------------------------------------*/
        if (debug){
          echo "DATAHNDL658=";
          var_dump ($data); echo "<br><br>";
        }
        $vali = new vali_data_form ( $data ) ;
        $result = $vali->validatethis (); //checkdata ();
        if (debug){
          echo "<b>DATA</b>";
          var_dump ($data); echo "<br>";

          echo "DATAHNDL667=";
          echo "<b>RESULT</b>";
          var_dump ($result); echo "<br><br>";

          echo "<b>vali-data</b>";
          var_dump ($vali->i_data); echo "<br><br>";

          echo "<b>vali-VALIDATE</b>";
          var_dump ($vali->validate); echo "<br>";
        }

        $data = $vali->i_data ;

         if (!$result) {
           $form = new nachrichten4fach ($data, $data["task"], $vali->validate);
           exit ;
         }
      }



       $query = "UPDATE `".$conf_4f_tbl ["nachrichten"]."` SET
            `03_datum`        = \"".konv_taktime_datetime ($data ["03_datum"]) ."\",
            `03_zeichen`      = \"".$data ["03_zeichen"]  ."\",
            `05_gegenstelle`  = \"".$data ["05_gegenstelle"] ."\",
            `06_befweg`       = \"".$data ["06_befweg"]."\",
            `06_befwegausw`   = \"".$data ["06_befwegausw"]   ."\",
            `x00_status`      = \"4\",
            `x01_abschluss`   = \"f\",
            `x02_sperre`      = \"f\",
            `x03_sperruser`   = \"\"
             WHERE `00_lfd` = \"".$data ["00_lfd"]."\"";
       $result = $dbaccess->query_table_iu ($query);
       protokolleintrag ("FM-Ausgang",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    break;

    case "FM-Ausgang_Sichter":

      if ($data ["15_quitdatum"] == "" ) { $data ["15_quitdatum"] = date ("Hi") ; }


       $data ["16_empf"] = $redcopy2."_rt,";

       for (  $i = 1 ; $i <= 5 ; $i++ ){
         for ( $j = 1 ; $j <= 5 ; $j++ ){
           if ( isset ( $data ["16_".$i.$j] ) ) {
             list ($ord, $pos, $fkt) = explode ("_", $data ["16_".$i.$j]);
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_".$fkt.",";
           } // if
           if ( $data ["16_gncopy"] == "16_".$i.$j."_gn" ) {
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_gn,";
           }
         } // for 2.
       } // for 1.

      if ($data ["03_datum"] == "" ) { $data ["03_datum"] = date ("Hi") ; }

      if (validate){
        if (debug){
          echo "DATAHNDL FM-Ausgang_Sichter =";
          var_dump ($data); echo "<br><br>";
        }
        $vali = new vali_data_form ( $data ) ;
        $result = $vali->validatethis (); //checkdata ();
        if (debug){
          echo "<b>DATA</b>";
          var_dump ($data); echo "<br>";

          echo "DATAHNDL667=";
          echo "<b>RESULT</b>";
          var_dump ($result); echo "<br><br>";

          echo "<b>vali-data</b>";
          var_dump ($vali->i_data); echo "<br><br>";

          echo "<b>vali-VALIDATE</b>";
          var_dump ($vali->validate); echo "<br>";
        }

        $data = $vali->i_data ;

         if (!$result) {
           $form = new nachrichten4fach ($data, $data["task"], $vali->validate);
           exit ;
         }
      }
      $query = "UPDATE `".$conf_4f_tbl ["nachrichten"]."` SET
            `03_datum`        = \"".konv_taktime_datetime ($data ["03_datum"])."\",
            `03_zeichen`      = \"".$data ["03_zeichen"]  ."\",
            `05_gegenstelle`  = \"".$data ["05_gegenstelle"] ."\",
            `06_befweg` = \"".$data ["06_befweg"]."\",
            `06_befwegausw`    = \"".$data ["06_befwegausw"]   ."\",
            `15_quitdatum`   = \"".konv_taktime_datetime ($data ["15_quitdatum"])."\",
            `15_quitzeichen` =  \"".$data ["15_quitzeichen"]."\",
            `16_empf`          =  \"".$data ["16_empf"]."\",
            `17_vermerke`   =  \"".$data ["17_vermerke"]."\",
            `x00_status`      = \"8\",
            `x01_abschluss`   = \"t\",
            `x02_sperre`      = \"f\",
            `x03_sperruser`   = \"\"
             WHERE `00_lfd` = \"".$data ["00_lfd"]."\";";
       $result = $dbaccess->query_table_iu ($query);
        protokolleintrag ("FM-Ausgang-Sichter",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    break;


   case "Stab_sichten":
/*
          15_quitdatum;
          15_quitzeichen;
          16_empf;
          17_vermerke;
*/
       $data ["16_empf"] = $redcopy2."_rt,";

       for (  $i = 1 ; $i <= 5 ; $i++ ){
         for ( $j = 1 ; $j <= 5 ; $j++ ){
           if ( isset ( $data ["16_".$i.$j] ) ) {
             list ($ord, $pos, $fkt) = explode ("_", $data ["16_".$i.$j]);
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_".$fkt.",";
           }
           if ( $data ["16_gncopy"] == "16_".$i.$j."_gn" ) {
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_gn,";
           }
         }
       }


       if ($data ["15_quitdatum"] == "" ) {
         $data ["15_quitdatum"] = date ("Hi")  ;
       }  else {
         $data ["15_quitdatum"] = $data ["15_quitdatum"] ;
       }
       $query = "UPDATE `".$conf_4f_tbl ["nachrichten"]."` SET
            `15_quitdatum`   = \"".convtodatetime (date ("dm"), $data ["15_quitdatum"]) ."\",
            `15_quitzeichen` =  \"".$data ["15_quitzeichen"]."\",
            `16_empf`           =  \"".$data ["16_empf"]."\",
            `17_vermerke`   =  \"".$data ["17_vermerke"]."\",
            `x00_status`      = \"8\",
            `x01_abschluss`   = \"t\",
            `x02_sperre`      = \"f\",
            `x03_sperruser`   = \"\"
             WHERE `00_lfd` = \"".$data ["00_lfd"]."\";";
       $result = $dbaccess->query_table_iu ($query);
        protokolleintrag ("Stab_sichten",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    break;

    case "Nachweis":
/*
          04_richtung;
          04_nummer;
*/
    break;

    case "FM-Admin":
    case "SI-Admin":
       // Holen wir erst einmal die nicht sichtbaren Datumsangaben
       $query    = "SELECT `01_datum`, `02_zeit`, `03_datum`, `12_abfzeit`, `15_quitdatum`
                    FROM ".$conf_4f_tbl ["nachrichten"]." WHERE `00_lfd` = \"".$data ['00_lfd']."\"; ";
       $result   = $dbaccess->query_table ($query);
       $db_datum = $result [1];
       $convdate ['01_datum']     = convdbdatetimeto ($db_datum ['01_datum']);
       $convdate ['02_zeit']      = convdbdatetimeto ($db_datum ['02_zeit']);
       $convdate ['03_datum']     = convdbdatetimeto ($db_datum ['03_datum']);
       $convdate ['12_abfzeit']   = convdbdatetimeto ($db_datum ['12_abfzeit']);
       $convdate ['15_quitdatum'] = convdbdatetimeto ($db_datum ['15_quitdatum']);
       $data ["16_empf"] = $redcopy2."_rt,";
       for (  $i = 1 ; $i <= 5 ; $i++ ){
         for ( $j = 1 ; $j <= 5 ; $j++ ){
           if ( isset ( $data ["16_".$i.$j] ) ) {
             list ($ord, $pos, $fkt) = explode ("_", $data ["16_".$i.$j]);
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_".$fkt.",";
           }
           if ( $data ["16_gncopy"] == "16_".$i.$j."_gn" ) {
             $data ["16_empf"] .= $empf_matrix [$i][$j]["fkt"]."_gn,";
           }
         }
       }



       $query = "UPDATE  `".$conf_4f_tbl ["nachrichten"]."` SET
            `15_quitdatum`    = \"".$convdate ['15_quitdatum']['datum']." ".$convdate ['15_quitdatum']['zeit'] ."\",
            `15_quitzeichen`  =  \"".$data ["15_quitzeichen"]."\",
            `16_empf`         =  \"".$data ["16_empf"]."\",
            `17_vermerke`     =  \"".$data ["17_vermerke"]."\"
             WHERE `00_lfd` = \"".$data ["00_lfd"]."\"";
       $result = $dbaccess->query_table_iu ($query);
       if ($data["task"] == "FM-Admin") {
         protokolleintrag ("++ FM-Admin",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
       } else {
         protokolleintrag ("++ SI-Admin",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
       }
    break;
  }
}

/*****************************************************************************
 $_SESSION
 [vStab_kuerzel] => LKW
 [vStab_funktion] => S2
 [vStab_rolle] => Stab
 *****************************************************************************/

  function legere_nuntium ($lfd) {

    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
     // Gibt es einen Eintrag zu der Nachricht mit der Nummer $lfd
    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"] );
    $tblusername   = $conf_4f_tbl ["usrtblprefix"].strtolower ($_SESSION["vStab_funktion"])."_".strtolower ($_SESSION["vStab_kuerzel"]);
    $query = "SELECT count(*) FROM $tblusername"."_read WHERE `nachnum` = $lfd;";
    $result = $dbaccess->query_table_wert ($query);
    return ($result [0]);
  }


/*****************************************************************************

  set_msg_read ($lfd)

\*****************************************************************************/
  function set_msg_read ($lfd) {

    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
     // Gibt es einen Eintrag zu der Nachricht mit der Nummer $lfd
    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"] );

    $tblusername   = $conf_4f_tbl ["usrtblprefix"].strtolower ($_SESSION["vStab_funktion"])."_".strtolower ($_SESSION["vStab_kuerzel"]);
    $query = "SELECT count(*) FROM $tblusername"."_read WHERE `nachnum` = $lfd;";
    $result = $dbaccess->query_table_wert ($query);
    if ($result [0] == 0){
       $query = "INSERT into ".$tblusername."_read SET
            `nachnum`      = \"".$lfd."\",
            `gelesen`      = \"".convtodatetime (date ("dmY"), date ("Hi"))."\"";
       $result = $dbaccess->query_table_iu ($query);
        protokolleintrag ("Stab_".$_SESSION["vStab_funktion"]." gelesen_".$lfd,$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);

    }
  }

/*****************************************************************************

  unset_msg_read ($lfd)

  DELETE FROM `usr_ls_ls_read` WHERE `usr_ls_ls_read`.`lfd` = 3 LIMIT 1
\*****************************************************************************/
  function unset_msg_read ($lfd) {

    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
    include_once ("../4fach/protokoll.php");
     // Gibt es einen Eintrag zu der Nachricht mit der Nummer $lfd
    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"] );
    $tblusername   = $conf_4f_tbl ["usrtblprefix"].strtolower ($_SESSION["vStab_funktion"])."_".strtolower ($_SESSION["vStab_kuerzel"]);
    $query = "SELECT count(*) FROM $tblusername"."_read WHERE `nachnum` = $lfd;";
    $result = $dbaccess->query_table_wert ($query);
    if ($result [0] != 0){
       $query = "DELETE FROM ".$tblusername."_read
                        WHERE ".$tblusername."_read.nachnum = $lfd;";
       $result = $dbaccess->query_table_iu ($query);
        protokolleintrag ("Stab_".$_SESSION["vStab_funktion"]." ungelesen_".$lfd,$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    }
  }



/*****************************************************************************

   set_msg_done ($lfd)

 *****************************************************************************/
  function set_msg_done ($lfd) {

    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
    include_once ("../4fach/protokoll.php");

     // Gibt es einen Eintrag zu der Nachricht mit der Nummer $lfd
    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"] );
    $fkttblname  = $conf_4f_tbl ["usrtblprefix"]."_fkt_".strtolower ($_SESSION["vStab_funktion"]);
    $query = "SELECT count(*) FROM $fkttblname"."_erl WHERE `nachnum` = $lfd;";
    $result = $dbaccess->query_table_wert ($query);
    if ($result [0] == 0){
       $query = "INSERT into ".$fkttblname."_erl SET
            `nachnum`      = \"".$lfd."\",
            `erledigt`     = \"".convtodatetime (date ("dmY"), date ("Hi"))."\"";
       $result = $dbaccess->query_table_iu ($query);
       protokolleintrag ("Stab_".$_SESSION["vStab_funktion"]." erledigt_".$lfd,$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    }
  }



/*****************************************************************************

   unset_msg_done ($lfd)

 *****************************************************************************/
function unset_msg_done ($lfd) {

    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
    include_once ("../4fach/protokoll.php");
     // Gibt es einen Eintrag zu der Nachricht mit der Nummer $lfd
    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"] );
    $fkttblname  = $conf_4f_tbl ["usrtblprefix"]."_fkt_".strtolower ($_SESSION["vStab_funktion"]);
    $query = "SELECT count(*) FROM $fkttblname"."_erl WHERE `nachnum` = $lfd;";
    $result = $dbaccess->query_table_wert ($query);
    if ($result [0] != 0){
       $query = "DELETE FROM ".$fkttblname."_erl
                        WHERE ".$fkttblname."_erl.nachnum = $lfd;";
       $result = $dbaccess->query_table_iu ($query);
       protokolleintrag ("Stab_".$_SESSION["vStab_funktion"]." unerledigt_".$lfd,$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
    }
  }




/*****************************************************************************\
 select nv_nachrichten.00_lfd

 from nv_nachrichten, usr_ls_ls_read

 where nv_nachrichten.00_lfd = usr_ls_ls_read.nachnum

 ==> Liste der gelesenen Nachrichten
\*****************************************************************************/
  function list_of_readed_msg (){

    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");

    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"] );

    $tblusername   = $conf_4f_tbl ["usrtblprefix"].
                     strtolower ($_SESSION["vStab_funktion"])."_".
                     strtolower ($_SESSION["vStab_kuerzel"]);

    $query = "select ".$conf_4f_tbl ["nachrichten"].".00_lfd from ".
              $conf_4f_tbl ["nachrichten"].", ".$tblusername."_read where ".
              $conf_4f_tbl ["nachrichten"].".00_lfd = ".$tblusername."_read.nachnum ;";
    $result = $dbaccess->query_usrtable ($query);
    return ($result);
  }

/*****************************************************************************\
 select nv_nachrichten.00_lfd

 from nv_nachrichten, usr_ls_ls_erl

 where nv_nachrichten.00_lfd = usr_ls_ls_erl.nachnum

 ==> Liste der erledigten Nachrichten
\*****************************************************************************/
function list_of_done_msg (){

  include ("../4fcfg/config.inc.php");
  include ("../4fcfg/dbcfg.inc.php");
  include ("../4fcfg/e_cfg.inc.php");

  $dbaccess = new db_access ($conf_4f_db ["server"],
                             $conf_4f_db ["datenbank"],
                             $conf_4f_tbl ["benutzer"],
                             $conf_4f_db ["user"],
                             $conf_4f_db ["password"] );
  $fkttblname  = $conf_4f_tbl ["usrtblprefix"]."_fkt_".strtolower ($_SESSION["vStab_funktion"]);
  $query = "select ".$conf_4f_tbl ["nachrichten"].".00_lfd from ".
           $conf_4f_tbl ["nachrichten"].", ".$fkttblname."_erl where ".
           $conf_4f_tbl ["nachrichten"].".00_lfd = ".$fkttblname."_erl.nachnum ;";
  $result = $dbaccess->query_usrtable ($query);
  return ($result);
}


  function get_flt_gelesen (){

    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"]);

    $tblusername = $conf_4f_tbl ["usrtblprefix"].strtolower ($_SESSION["vStab_funktion"])."_".strtolower ($_SESSION["vStab_kuerzel"]);

    $fkttblname  = $conf_4f_tbl ["usrtblprefix"]."_fkt_".strtolower ($_SESSION["vStab_funktion"]);

    $tblusername_r = $tblusername."_read";
    $tblusername_e = $fkttblname."_erl";

    $query_r = "SELECT COUNT(*) FROM ".$conf_4f_tbl ["nachrichten"]."
                WHERE (`".$conf_4f_tbl ["nachrichten"]."`.`04_nummer` IN ( select `".$tblusername_r."`.`nachnum` from `".$tblusername_r."` where 1))";
    $result = $dbaccess->query_table_wert ($query_r);
    $flt_gelesene = $result [0];
  }

  function get_flt_erledigt (){
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");
    $dbaccess = new db_access ($conf_4f_db ["server"],
                               $conf_4f_db ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db ["user"],
                               $conf_4f_db ["password"]);

    $tblusername  = $conf_4f_tbl ["usrtblprefix"].strtolower ($_SESSION["vStab_funktion"])."_".strtolower ($_SESSION["vStab_kuerzel"]);

    $fkttblname  = $conf_4f_tbl ["usrtblprefix"]."_fkt_".strtolower ($_SESSION["vStab_funktion"]);

    $tblusername_r = $tblusername."_read";
    $tblusername_e = $fkttblname."_erl";

    $query_e = "SELECT COUNT(*) FROM ".$conf_4f_tbl ["nachrichten"]."
                WHERE (`".$conf_4f_tbl ["nachrichten"]."`.`04_nummer` IN ( select `".$tblusername_e."`.`nachnum` from `".$tblusername_e."` where 1))";

    $result = $dbaccess->query_table_wert ($query_e);
    $flt_erledigte = $result [0];
  }

/*****************************************************************************\
 get_msg_by_lfd ( $lfd )
\*****************************************************************************/
  function get_msg_by_lfd ( $lfd ){

    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");

    $dbaccess = new db_access ($conf_4f_db ["server"], $conf_4f_db ["datenbank"],$conf_4f_tbl ["benutzer"], $conf_4f_db ["user"],  $conf_4f_db ["password"]);
    $query = "SELECT * FROM `".$conf_4f_tbl ["nachrichten"]."` where 00_lfd = ".$lfd; //$_GET["00_lfd"];
    $result = $dbaccess->query_table ($query);
    $data = $result [1];

    if ($data ["01_datum"] == "0000-00-00 00:00:00")     { $data["01_datum"] = ""; }
    if ($data ["02_zeit"] == "0000-00-00 00:00:00")      { $data ["02_zeit"] = ""; }
    if ($data ["03_datum"] == "0000-00-00 00:00:00")     { $data ["03_datum"] = ""; }
    if ($data ["12_abfzeit"] == "0000-00-00 00:00:00")   { $data ["12_abfzeit"] = ""; }
    if ($data ["15_quitdatum"] == "0000-00-00 00:00:00") { $data ["15_quitdatum"] = ""; }

     //  Umwandlung Datenbankdatum --> taktischer Zeit falls erforderlich
    if (strlen ($data["01_datum"]) != ""){
      $data["01_datum"]   = konv_datetime_taktime ($data["01_datum"]);
    }
    if (strlen ($data["02_zeit"]) != ""){
      $data["02_zeit"]   = konv_datetime_taktime ($data["02_zeit"]);
    }
    if (strlen ($data["12_abfzeit"]) != ""){
      $data["12_abfzeit"] = konv_datetime_taktime ($data["12_abfzeit"]);
    }
    if (strlen ($data["15_quitdatum"]) != ""){
      $data["15_quitdatum"] = konv_datetime_taktime ($data["15_quitdatum"]);
    }

    return ($data);

  }

  /**************************************************************************\

  \**************************************************************************/
  function reset_record_lock ( $lfd ){

    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");

    $dbaccess = new db_access ($conf_4f_db ["server"], $conf_4f_db ["datenbank"],$conf_4f_tbl ["benutzer"], $conf_4f_db ["user"],  $conf_4f_db ["password"]);

    $query = "UPDATE `".$conf_4f_tbl ["nachrichten"]."` SET
            `x02_sperre`      = \"f\",
            `x03_sperruser`   = \"\"
             WHERE `00_lfd` = \"".$lfd."\";";

    $result = $dbaccess->query_table_iu ($query);
    protokolleintrag ("Fernmelder Free_record",$query.";".session_id().";".$_SERVER["REMOTE_ADDR"]);
  }

?>
