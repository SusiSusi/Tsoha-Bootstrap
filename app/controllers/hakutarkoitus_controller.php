<?php

class HakutarkoitusController extends BaseController {
    
    public static function tarkoitukset() {
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        View::make('kayttaja/rekisterointi.html', array('tarkoitukset' => $tarkoitukset));
    }
    
        public static function naytaTarkoitus($id) {
        $tarkoitus = Hakutarkoitus::etsiHakutarkoitus($id);
        View::make('kayttaja/julkinenProfiilisivu.html', array('tarkoitus' => $tarkoitus));
    }
}

