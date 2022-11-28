<?php

use immodvisor\Api;

include __DIR__ . "immodvisor/immodvisor.php" ;

class ImmodvisorMain
{

    public function review(){
        $api = new Api ( 'N869-XQPW-HNS2P-GHTDG2-V6J7G' ,
            'JBGKcZ#!AT3!phss)ddB' ,
            'acài}=[QrXélkUPKRZ$T' );
        $content = $api -> test ()-> parse ();
if ( $api -> check ()) {
           echo $content -> datas -> message ;
} else {
           echo $api -> getError ();
}
    }

}