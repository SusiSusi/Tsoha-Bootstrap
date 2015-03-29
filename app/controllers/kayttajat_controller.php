<?php

class KayttajaController extends BaseController {

    public static function index() {
        $kayttajat = Kayttaja::kaikkiKayttajat();
        View::make('kayttaja/kayttajienListaukset.html', array('kayttajat' => $kayttajat));
    }

    public static function luo() {
        View::make('kayttaja/rekisterointi.html');
    }

    public static function store() {
        $arvot = $_POST;
        $hakutarkoitus = $arvot['etsin'];
        $kayttaja = new Kayttaja(array(
            'kayttajatunnus' => $arvot['kayttajatunnus'],
            'nimi' => $arvot['nimi'],
            'salasana' => $arvot['salasana'],
            'syntymaaika' => date('Y-m-d', strtotime($arvot['vuosi'] . ' ' . $arvot['kuukausi'] . ' ' . $arvot['paiva'])),
            'sukupuoli' => $arvot['sukupuoli'],
            'paikkakunta' => $arvot['paikkakunta'],
            'omattiedot' => $arvot['omattiedot'],
            'kuva' => $arvot['kuva'],
            'hakutarkoitusid' => $hakutarkoitus,
//             'oikeudet' => false
        ));
//        Kint::dump($arvot);
        $kayttaja->talleta();
        Redirect::to('/julkinenProfiilisivu/' . $kayttaja->id, array('message' => 'Käyttäjätunnus luotu!'));
    }

    public static function nayta($id) {
        $kayttaja = Kayttaja::etsi($id);
        View::make('kayttaja/julkinenProfiilisivu.html', array('kayttaja' => $kayttaja));
    }

}
