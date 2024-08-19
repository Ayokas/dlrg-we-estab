<?php 
/******************************************************************************\ 
       Definition der Mitspieler 
|-----|-----|-----|-----|
| 1.1 | 2.1 | 3.1 | 4.1 |
| 1.2 | 2.2 | 3.2 | 4.2 |
| 1.3 | 2.3 | 3.3 | 4.3 |
| 1.4 | 2.4 | 3.4 | 4.4 |
| 1.5 | 2.5 | 3.5 | 4.5 |
|-----|-----|-----|-----|
\******************************************************************************/
//              lfd-Nr      PosForm  Fkt
//                       Spalte,Zeile          Roll


$empf_matrix = array ( 
1 => array ( 
	 1 => array ("typ" => "cb", "typ" => "cb", "fkt" => "EL", "rolle" => "Stab", "mode" => "ro" ),
	 2 => array ("typ" => "cb", "typ" => "cb", "fkt" => "UEL", "rolle" => "Stab", "mode" => "ro" ),
	 3 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 4 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" )
),
2 => array ( 
	 1 => array ("typ" => "cb", "typ" => "cb", "fkt" => "S1S4", "rolle" => "Stab", "mode" => "ro" ),
	 2 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 3 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 4 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" )
),
3 => array ( 
	 1 => array ("typ" => "cb", "typ" => "cb", "fkt" => "S2", "rolle" => "Stab", "mode" => "ro" ),
	 2 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 3 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 4 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" )
),
4 => array ( 
	 1 => array ("typ" => "cb", "typ" => "cb", "fkt" => "S3S6", "rolle" => "Stab", "mode" => "ro" ),
	 2 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 3 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 4 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" )
),
5 => array ( 
	 1 => array ("typ" => "cb", "typ" => "cb", "fkt" => "S5", "rolle" => "Stab", "mode" => "ro" ),
	 2 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 3 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" ),
	 4 => array ("typ" => "t", "typ" => "t", "fkt" => "", "rolle" => "leer", "mode" => "ro" )
)
);
 
	$conf_empf [1] = array ("fkt" => "EL", "rolle" => "Stab" ); 
	$conf_empf [2] = array ("fkt" => "S1S4", "rolle" => "Stab" ); 
	$conf_empf [3] = array ("fkt" => "S2", "rolle" => "Stab" ); 
	$conf_empf [4] = array ("fkt" => "S3S6", "rolle" => "Stab" ); 
	$conf_empf [5] = array ("fkt" => "S5", "rolle" => "Stab" ); 
	$conf_empf [6] = array ("fkt" => "UEL", "rolle" => "Stab" ); 
	$conf_empf [7] = array ("fkt" => "Si", "rolle" => "Stab" ); 
	$conf_empf [8] = array ("fkt" => "A/W", "rolle" => "Fernmelder" ); 

    $redcopy2 = "S2" ;



?>