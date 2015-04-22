<?php

class KayttajaController extends BaseController {

    public static function index() {
        self::check_logged_in();
        $kayttajat = Kayttaja::kaikkiKayttajat();
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/kayttajienListaukset.html', array('kayttajat' => $kayttajat,
            'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat));
    }

//    public static function tarkoitukset() {
//        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
//        View::make('kayttaja/rekisterointi.html', array('tarkoitukset' => $tarkoitukset));
//    }

    public static function store() {
        $arvot = $_POST;
        $hakutarkoitus = $arvot['etsin'];
        $attributes = array(
            'kayttajatunnus' => $arvot['kayttajatunnus'],
            'nimi' => $arvot['nimi'],
            'salasana' => $arvot['salasana2'],
            'syntymaaika' => date('Y-m-d', strtotime($arvot['vuosi'] . '-' . $arvot['kuukausi'] . '-' . $arvot['paiva'])),
            'sukupuoli' => $arvot['sukupuoli'],
            'paikkakunta' => $arvot['paikkakunta'],
            'omattiedot' => $arvot['omattiedot'],
            'kuva' => $arvot['kuva'],
            'hakutarkoitusid' => $hakutarkoitus
        );

        $synttarit = array(
            'vuosi' => $arvot['vuosi'],
            'kuukausi' => $arvot['kuukausi'],
            'paiva' => $arvot['paiva']
        );

        $kayttaja = new Kayttaja($attributes);
        $errors = $kayttaja->errors();

        if (!$kayttaja->kaksiSanaaTarkoittaaSamaa($arvot['salasana'], $arvot['salasana2'])) {
            array_push($errors, "Kirjoitit kaksi eri salasanaa. Anna salasana uudelleen.");
        }

        if (count($errors) == 0) {
//            Kint::dump($kayttaja);
            $kayttaja->talleta();
            $_SESSION['kayttajatunnus'] = $kayttaja->id;
            Redirect::to('/omaProfiilisivu/' . $kayttaja->id, array('message' => 'Käyttäjätunnus luotu!'));
        } else {
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            View::make('kayttaja/rekisterointi.html', array('errors' => $errors, 'attributes' => $attributes,
                'tarkoitukset' => $tarkoitukset, 'synttarit' => $synttarit));
        }
    }

    public static function luo() {
        View::make('kayttaja/rekisterointi.html');
    }

    public static function etusivu() {
        if (self::get_user_logged_in() != null) {
            $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
            View::make('kayttaja/etusivu.html', array('maara' => $lukemattomat));
        } else {
            View::make('kayttaja/etusivu.html');
        }
    }

    public static function nayta($id) {
        self::check_logged_in();
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/julkinenProfiilisivu.html', array('kayttaja' => $kayttaja,
            'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat));
    }

    public static function naytaOmaSivu($id) {
        self::check_logged_in();
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/omaProfiilisivu.html', array('kayttaja' => $kayttaja,
            'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat));
    }

    public static function edit($id) {
        self::check_logged_in();
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/muokkaa.html', array('kayttaja' => $kayttaja,
            'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat));
    }

    public static function update($id) {
        self::check_logged_in();
        $kayttajaAlkuperainen = Kayttaja::etsi($id);
        $arvot = $_POST;

        $attributes = array(
            'id' => $id,
            'kayttajatunnus' => $arvot['kayttajatunnus'],
            'nimi' => $arvot['nimi'],
            'syntymaaika' => date('Y-m-d', strtotime($arvot['vuosi'] . '-' . $arvot['kuukausi'] . '-' . $arvot['paiva'])),
            'sukupuoli' => $kayttajaAlkuperainen->sukupuoli,
            'paikkakunta' => $arvot['paikkakunta'],
            'omattiedot' => $arvot['omattiedot'],
            'kuva' => $arvot['kuva'],
            'hakutarkoitusid' => $arvot['etsin']
        );
//        Kint::dump($arvot);
        $kayttaja = new Kayttaja($attributes);
        $error = null;
        if (!$kayttaja->kaksiSanaaTarkoittaaSamaa($kayttajaAlkuperainen->salasana, $arvot['salasana'])) {
            $error = "Väärä salasana. Anna salasana uudelleen.";
        }
        if (!empty($error)) {            
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            View::make('kayttaja/muokkaa.html', array('errors' => $error, 'tarkoitukset' => $tarkoitukset,
                'kayttaja' => $attributes));
        } else {
        $kayttaja->muokkaa($id);
        Redirect::to('/omaProfiilisivu/' . $kayttaja->id, array('message' => 'Profiilisivun muokkaus onnistui!'));
        }
    }

    public static function poistaTunnus($id) {
        self::check_logged_in();
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/poistaTunnus.html', array('kayttaja' => $kayttaja,
            'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat));
    }

    public static function poista($id) {
        self::check_logged_in();
        $kayttaja = Kayttaja::etsi($id);
        $arvot = $_POST;
        $error = null;
        if (!$kayttaja->kaksiSanaaTarkoittaaSamaa($kayttaja->salasana, $arvot['salasana'])) {
            $error = "Väärä salasana. Anna salasana uudelleen.";
        }
        if (!empty($error)) {
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
            View::make('kayttaja/poistaTunnus.html', array('kayttaja' => $kayttaja,
                'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat, 'errors' => $error));
        } else {
            $kaikkiSaapuneetViestit = Vastaanottaja::haeSaapuneetViestit($id);
            $kaikkiLahetetytViestit = Viesti::etsiLahettajanViestit($id);
            Vastaanottaja::poistaListanKaikkiKytkokset($kaikkiSaapuneetViestit);
            Vastaanottaja::poistaListanKaikkiKytkokset($kaikkiLahetetytViestit);
            Viesti::poistaListanKaikkiViestit($kaikkiLahetetytViestit);
            Viesti::poistaListanKaikkiViestit($kaikkiSaapuneetViestit);
            $kayttaja = new Kayttaja(array('id' => $id));
            $kayttaja->poistaTunnus($id);
//        Kint::dump($kayttaja);
            Redirect::to('/kirjautuminen', array('message' => 'Käyttäjätunnus poistettu.'));
        }
    }

}
