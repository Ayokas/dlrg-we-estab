<?php
/*****************************************************************************

                      Definitionen fuer die Kats - Menue

  Menuestruktur des Startmenues

******************************************************************************/
include ("./4fcfg/config.inc.php");

    //$conf_menue["background_color"] = "rgb(240, 100, 100)";
	$conf_menue["background_color"] = "#87CEEB";
 // $conf_menue["foreground_color"] = "rgb(240,  80,  80)";
    $conf_menue["foreground_color"] = "#87CEEB";
	
    $conf_menue["einrichtung"] = "EinsatzAbschnittsLeitung<br>DLRG Westfalen<br><b>eStab Server 1<b>";
    $conf_menue["titel"] = "eStab Webschnittstelle BETA Version 0.9";
//      $conf_menue["symbole"] = "./symbole/";
    $conf_menue["sym_top_left"] = $conf_menue ["symbole"]."dlrg1.gif";
    $conf_menue["sym_top_right"] = $conf_menue ["symbole"]."dlrg1.gif";


// Anordnung:
// 1            2
//3             4
//...

// links
    $menue[1]["text"] = "Nachrichtenvordruck";
    $menue[1]["pic"]  = $conf_menue ["symbole"]."4fach_aktiv.png";
    $menue[1]["link"] = "./4fach/index.php";

	$menue[3]["text"] = "Generierte Vordrucke<BR>(S2)";
    $menue[3]["pic"]  = "./4fach/design/mr/folder_global.gif";
//  $menue[3]["link"] = "./4fdata/" . $conf_4f_db["datenbank"] . "/vordruck/";
	$menue[3]["link"] = "./4fvordrucke/";

	$menue[5]["text"] = "Liste aller Meldungen";
    $menue[5]["pic"]  = $conf_menue ["symbole"]."all_msg.png";
    $menue[5]["link"] = "";
//	$menue[5]["link"] = "./4fueltg/ue_ltg.php";

    $menue[7]["text"] = "WICHTIGE DOKUMENTE";
    $menue[7]["pic"]  = $conf_menue ["symbole"]."merke32.gif";
    $menue[7]["link"] = "./stabinfo/index.php";

    $zusatz_menue[1]["text"] = "administrative Massnahme";
    $zusatz_menue[1]["pic"]  = $conf_menue ["symbole"]."adm_aktiv.png";
    $zusatz_menue[1]["link"] = "./4fadm/admin.php";
	
	$zusatz_menue[3]["text"] = "administrative Massnahme";
    $zusatz_menue[3]["pic"]  = $conf_menue ["symbole"]."adm_aktiv.png";
    $zusatz_menue[3]["link"] = "";
//	$zusatz_menue[3]["link"] = "./4fadm/admin.php";

// rechts
//  $menue[2]["text"] = "Einsatztagebuch<BR>[S2]";
//  $menue[2]["pic"]  = $conf_menue ["symbole"]."etb_aktiv.png";
//  $menue[2]["link"] = "./stabetb/etb.php";
	
	$menue[2]["text"] = "Liste aller Meldungen";
    $menue[2]["pic"]  = $conf_menue ["symbole"]."all_msg.png";
    $menue[2]["link"] = "./4fueltg/ue_ltg.php";

//  $menue[4]["text"] = "Technisches Betriebsbuch<BR>[A/W]";
//  $menue[4]["pic"]  = $conf_menue ["symbole"]."tbb_aktiv.png";
//  $menue[4]["link"] = "./fmtbb/tbb.php";
	
	$menue[4]["text"] = "Nachweisung";
    $menue[4]["pic"]  = $conf_menue ["symbole"]."nw.png";
    $menue[4]["link"] = "./4fach/nachwea.php?nwalle";

    $menue[6]["text"] = "FileWeb-Server";
    $menue[6]["pic"]  = $conf_menue ["symbole"]."merke32.gif";
    $menue[6]["link"] = "";
//	$menue[6]["link"] = "./4fach/nachwea.php?nwalle";

    $menue[8]["text"] = "FileWeb-Server";
    $menue[8]["pic"]  = $conf_menue ["symbole"]."merke32.gif";
    $menue[8]["link"] = "http://dlrgeals2:8080";

    $zusatz_menue[2]["text"] = "Kurzanleitung zur eStab Installation & Nutzung";
    $zusatz_menue[2]["pic"]  = $conf_menue ["symbole"]."icon_handbuch.gif";
    $zusatz_menue[2]["link"] = "./doku/Handbuch_eStab.pdf";

?>
