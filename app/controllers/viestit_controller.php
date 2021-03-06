<?php

class ViestitController extends BaseController {

    public static function kaikkiViestit() {
        self::check_logged_in();
        $viestit = Vastaanottaja::haeSaapuneetViestit(self::get_user_logged_in()->id);
        $kayttajatunnus = Kayttaja::kaikkiKayttajatPerus();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('viestit/saapuneetViestit.html', array('viestit' => $viestit,
            'kayttajatunnus' => $kayttajatunnus, 'maara' => $lukemattomat));
    }

    public static function kaikkiLahetetytViestit() {
        self::check_logged_in();
        $viestit = Viesti::etsiLahettajanViestit(self::get_user_logged_in()->id);
        $vastaanottajat = Vastaanottaja::kaikkiLahetetytViestit($viestit);
        $kayttajatunnus = Kayttaja::kaikkiKayttajatPerus();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('viestit/lahetetytViestit.html', array('viestit' => $viestit,
            'vastaanottaja' => $vastaanottajat, 'kayttajatunnus' => $kayttajatunnus, 'maara' => $lukemattomat));
    }

    public static function lueViesti($id) {
        self::check_logged_in();
        $viesti = Viesti::etsiViesti($id);
        $vastaanottajanid = Vastaanottaja::etsiViestinVastaanottaja($id);
        if ($vastaanottajanid[1] == self::get_user_logged_in()->id) {
            $viesti->viestiLuettu($id);
        }
        $vastaanottaja = Kayttaja::etsi($vastaanottajanid[1]);
        $lahettaja = Kayttaja::etsi($viesti->lahettajaid);
//        $kaikkiViestit = Vastaanottaja::kaikkiViestitKayttajanKanssa(self::get_user_logged_in()->id, $lahettaja->id);
//        $kaikkiViestitToisinpain = Vastaanottaja::kaikkiViestitKayttajanKanssa($lahettaja->id, self::get_user_logged_in()->id);
//        if ($kaikkiViestitToisinpain) {
//            foreach ($kaikkiViestitToisinpain as $viestini) {
//                array_push($kaikkiViestit, $viestini);
//            }
//        }
        $kayttajatunnus = Kayttaja::kaikkiKayttajatPerus();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('viestit/viestinSisalto.html', array('viesti' => $viesti,
            'kayttajatunnus' => $kayttajatunnus, 'maara' => $lukemattomat, 'vastaanottaja' => $vastaanottaja,
            'lahettaja' => $lahettaja));
    }

    public static function lahetaViesti() {
        self::check_logged_in();
        $arvot = $_POST;

        $viesti = new Viesti(array(
            'aihe' => $arvot['aihe'],
            'sisalto' => $arvot['sisalto'],
            'aika' => date('Y-m-d H:i:s', strtotime('now')),
            'lahettajaid' => $arvot['lahettajaid']
        ));
        $viesti->tallennaViesti();

        $vastaanottaja = new Vastaanottaja($viesti->id, $arvot['vastaanottajaid']);
        $vastaanottaja->yhdistaViestiKayttajaan();
        if ($arvot['viestiMuualta'] == 1) {
            Redirect::to('/julkinenProfiilisivu/' . $arvot['vastaanottajaid'], array('message' => 'Viesti lähetetty!'));
        } else {
            Redirect::to('/saapuneetViestit', array('message' => 'Viesti lähetetty!'));
        }
    }
//
//    public static function muokkaaViestia($id) {
//        self::check_logged_in();
//        $arvot = $_POST;
//
//        $attributes = array(
//            'id' => $id,
//            'aihe' => $arvot['aihe'],
//            'sisalto' => $arvot['viestinSisalto'] . \n . $arvot['viestinAika'] . \n . $arvot['aikaisempiSisalto']
//                . \n . $arvot['sisalto'],
//            'aika' => date('Y-m-d H:i:s', strtotime('now')),
//            'lahettajaid' => $arvot['lahettajaid']
//        );
//        Kint::dump($attributes);
//        $viesti = new Viesti($attributes);
////        $viesti->muokkaa($id);
//        Kint::dump($viesti);
//        $vastaanottaja = new Vastaanottaja($viesti->id, $arvot['vastaanottajaid']);
//        Kint::dump($vastaanottaja);
////        $vastaanottaja->yhdistaViestiKayttajaan();
//        
//    }

    public static function poistaViesti($id) {
        self::check_logged_in();
        Vastaanottaja::poistaViestiKytkos($id);
        $viesti = new Viesti(array('id' => $id));
        $viesti->poistaViesti($id);
        Redirect::to('/saapuneetViestit', array('message' => 'Viesti poistettu!'));
    }

    public static function asetaLukemattomaksi($id) {
        self::check_logged_in();
        Viesti::asetaViestiLukemattomaksi($id);
        Redirect::to('/saapuneetViestit');
    }
}
