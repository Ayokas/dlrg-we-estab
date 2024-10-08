<?php

define ("colorschema",aknz);
/*// iuk, thw, aknz     */

switch (colorschema){
  case "alt":   //  alte Einstellung
    $this->bg_color_fm_a   = "rgb(255, 224, 200)"; // rosa Fernmelder aktiv
    $this->bg_color_fmp_a  = "rgb(100, 255, 100)"; // hell gr�n Fernmelderpflichtfeld  aktiv
    $this->bg_color_nw_a   = "rgb(255, 204, 51)";  // orange
    $this->bg_color_tx_a   = "rgb(224, 255, 255)"; // hell blau
    $this->bg_color_si_a   = "rgb(255, 224, 255)"; // hell violett
    $this->bg_color_inaktv = "rgb(255, 255, 255)";  // weiss
    $this->bg_color_aktv   = "rgb(255, 255, 255)";  // weiss
    $this->rbl_bg_color    = "rgb(255, 255, 255)";  // weiss
    $this->bg_color_aktv_must = "rgb(240, 20, 20)"; // rot
  break;
  case "iuk":
   // Gruenes Blatt oben
    $this->bg_color_fm_a   = "rgb(  0, 255,   0)"; // rosa Fernmelder aktiv
    $this->bg_color_fmp_a  = "rgb(200, 255, 200)"; // hell gr�n Fernmelderpflichtfeld  aktiv
    $this->bg_color_nw_a   = "rgb(255, 204, 51)";  // orange
    $this->bg_color_tx_a   = "rgb(  0, 255,   0)"; // hell blau
    $this->bg_color_si_a   = "rgb(  0, 255,   0)"; // hell violett
    $this->bg_color_inaktv = "rgb(100, 255, 100)";  // weiss
    $this->bg_color_aktv   = "rgb(255, 255, 255)";  // weiss
    $this->rbl_bg_color    = "rgb(255, 255, 255)";  // weiss
    $this->bg_color_aktv_must = "rgb(240, 20, 20)"; // rot
  break;
  case "thw":
     // Blau Blatt oben
    $this->bg_color_fm_a   = "rgb(  0,   0, 255)"; // rosa Fernmelder aktiv
    $this->bg_color_fmp_a  = "rgb(200, 200, 255)"; // hell gr�n Fernmelderpflichtfeld  aktiv
    $this->bg_color_nw_a   = "rgb(255, 204,  51)";  // orange
    $this->bg_color_tx_a   = "rgb(  0,   0, 255)"; // hell blau
    $this->bg_color_si_a   = "rgb(  0,   0, 255)"; // hell violett
    $this->bg_color_inaktv = "rgb(100, 100, 220)";  // weiss
    $this->bg_color_aktv   = "rgb(255, 255, 255)";  // weiss
    $this->rbl_bg_color    = "rgb(255, 255, 255)";  // weiss
    $this->bg_color_aktv_must = "rgb(240, 20, 20)"; // rot
  break;
  case "aknz":
     // wei�es Blatt oben
    $this->bg_color_fm_a   = "rgb(200, 200, 200)"; // rosa Fernmelder aktiv
    $this->bg_color_fmp_a  = "rgb(255, 255, 255)"; // hell gr�n Fernmelderpflichtfeld  aktiv
    $this->bg_color_nw_a   = "rgb(255, 204,  51)";  // orange
    $this->bg_color_tx_a   = "rgb(200, 200, 200)"; // hell blau
    $this->bg_color_si_a   = "rgb(255, 255, 255)"; // hell violett
    $this->bg_color_inaktv = "rgb(120, 120, 120)";  // weiss
    $this->bg_color_aktv   = "rgb(255, 255, 255)";  // weiss
    $this->rbl_bg_color    = "rgb(255, 255, 255)";  // weiss
    $this->bg_color_aktv_must = "rgb(240, 20, 20)"; // rot
  break;
}
?>
