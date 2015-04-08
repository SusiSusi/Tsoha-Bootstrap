<?php

class KayttajaController extends BaseController {

    public static function index() {
        $kayttajat = Kayttaja::kaikkiKayttajat();
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        View::make('kayttaja/kayttajienListaukset.html', array('kayttajat' => $kayttajat, 'tarkoitukset' => $tarkoitukset));
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
            'salasana' => $arvot['salasana'],
            'syntymaaika' => date('Y-m-d', strtotime($arvot['vuosi'] . '-' . $arvot['kuukausi'] . '-' . $arvot['paiva'])),
            'sukupuoli' => $arvot['sukupuoli'],
            'paikkakunta' => $arvot['paikkakunta'],
            'omattiedot' => $arvot['omattiedot'],
            'kuva' => $arvot['kuva'],
            'hakutarkoitusid' => $hakutarkoitus,
//             'oikeudet' => false
        );
        $kayttaja = new Kayttaja($attributes);
        $errors = $kayttaja->errors();

//        Kint::dump($arvot);
        if (count($errors) == 0) {
            $kayttaja->talleta();
            Redirect::to('/julkinenProfiilisivu/' . $kayttaja->id, array('message' => 'Käyttäjätunnus luotu!'));
        } else {
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            View::make('kayttaja/rekisterointi.html', array('errors' => $errors, 'attributes' => $attributes, 'tarkoitukset' => $tarkoitukset));
        }
    }

    public static function luo() {
        View::make('kayttaja/rekisterointi.html');
    }

    public static function nayta($id) {
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        View::make('kayttaja/julkinenProfiilisivu.html', array('kayttaja' => $kayttaja, 'tarkoitukset' => $tarkoitukset));
    }

    public static function edit($id) {
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        View::make('kayttaja/muokkaa.html', array('kayttaja' => $kayttaja, 'tarkoitukset' => $tarkoitukset));
    }

    public static function update($id) {
        $arvot = $_POST;

        $attributes = array(
            'id' => $id,
//        'kayttajatunnus' => $arvot['kayttajatunnus'],
            'nimi' => $arvot['nimi'],
            'syntymaaika' => date('Y-m-d', strtotime($arvot['vuosi'] . '-' . $arvot['kuukausi'] . '-' . $arvot['paiva'])),
//        'sukupuoli' => $arvot['sukupuoli'],
            'paikkakunta' => $arvot['paikkakunta'],
            'omattiedot' => $arvot['omattiedot'],
            'kuva' => $arvot['kuva'],
            'hakutarkoitusid' => $arvot['etsin']
        );
//        Kint::dump($arvot);
        $kayttaja = new Kayttaja($attributes);
//        $errors = $kayttaja->errors();
//        if(count($errors) > 0) {
//            View::make('kayttaja/muokkaa.html', array('errors' => $errors, 'attributes' => $attributes));
//        } else {
        $kayttaja->muokkaa($id);
        Redirect::to('/julkinenProfiilisivu/' . $kayttaja->id, array('message' => 'Profiilisivun muokkaus onnistui!'));
//        }
    }

    public static function poistaTunnus($id) {
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        View::make('kayttaja/poistaTunnus.html', array('kayttaja' => $kayttaja, 'tarkoitukset' => $tarkoitukset));
    }

    public static function poista($id) {
        $kayttaja = new Kayttaja(array('id' => $id));
        $kayttaja->poistaTunnus($id);
//        Kint::dump($kayttaja);
        Redirect::to('/kayttajienListaukset', array('message' => 'Käyttäjätunnus poistettu.'));
    }

}
