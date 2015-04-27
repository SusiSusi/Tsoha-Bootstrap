<?php

class KayttajaController extends BaseController {

    // metodi palauttaa sivulistan edellisen, nykyisen ja seuraavan sivun numeron
    public static function sivunLaskija($arvot, $sivut) {
        $palautus = array();
        $palautus['prev_page'] = null;
        $palautus['curr_page'] = 1;
        if ($sivut == 1) {
            $palautus['next_page'] = null;
        } else {
            $palautus['next_page'] = 2;
        }
        if (isset($arvot['page'])) {
            if ($arvot['page'] > 0 && $arvot['page'] <= $sivut) {
                $palautus['curr_page'] = $arvot['page'];
                if ($palautus['curr_page'] == 1) {
                    $palautus['prev_page'] = null;
                } else {
                    $palautus['prev_page'] = $arvot['page'] - 1;
                }
                if ($palautus['curr_page'] == $sivut) {
                    $palautus['next_page'] = null;
                } else {
                    $palautus['next_page'] = $arvot['page'] + 1;
                }
            }
        }
        return $palautus;
    }

    // metodi luo kayttajienListaus-sivun hakemalla käyttäjän ja tekemällä listaussivun
    // yhdellä sivulla näytetään 10 käyttäjää
    public static function index() {
        self::check_logged_in();
        $arvot = $_GET;
        $options = array();
        $kayttajienMaara = Kayttaja::laskeKayttajienMaara();
        $sivunKoko = 10;
        $sivut = ceil($kayttajienMaara / $sivunKoko);
        if (isset($arvot['page'])) {
            if ($arvot['page'] > 0 && $arvot['page'] <= $sivut) {
                $options['sivu'] = $arvot['page'];
                $options['sivunKoko'] = $sivunKoko;
            }
        }
        $palautus = KayttajaController::sivunLaskija($arvot, $sivut);
        $kayttajat = Kayttaja::kaikkiKayttajat($options);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/kayttajienListaukset.html', array('kayttajat' => $kayttajat,
            'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat, 'pages' => $sivut,
            'prev_page' => $palautus['prev_page'], 'next_page' => $palautus['next_page'],
            'curr_page' => $palautus['curr_page']));
    }

    // metodi luo haku-sivun ja kuuntelee käyttäjän antamat hakukriteerit
    public static function haku() {
        self::check_logged_in();
        $arvot = $_GET;
        $options = array();
        if (isset($arvot['kayttajatunnus'])) {
            $options['kayttajatunnus'] = $arvot['kayttajatunnus'];
            $options['vuosi1'] = $arvot['vuosi1'];
            $options['vuosi2'] = $arvot['vuosi2'];
            $options['sukupuoli'] = $arvot['sukupuoli'];
            $options['paikkakunta'] = $arvot['paikkakunta'];
            $options['hakutarkoitus'] = $arvot['hakutarkoitus'];
            $kayttajat = Kayttaja::kaikkiKayttajatHaulla($options);
        } else {
            $kayttajat = 'EnsimmainenKerta';
        }
        if (empty($kayttajat)) {
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
            View::make('kayttaja/haku.html', array('kayttajat' => $kayttajat,
                'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat,
                'message' => 'Hakuehdoilla ei löytynyt käyttäjiä!'));
        } else {
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
            View::make('kayttaja/haku.html', array('kayttajat' => $kayttajat,
                'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat));
        }
    }

    // metodi kuuntelee käyttäjän syötteet ja luo uuden käyttäjätunnuksen
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
//            'kuva' => $arvot['kuva'],
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
            $kayttaja->talleta();
            $_SESSION['kayttajatunnus'] = $kayttaja->id;
            Redirect::to('/omaProfiilisivu', array('message' => 'Käyttäjätunnus luotu!'));
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
        $kayttajienMaara = Kayttaja::laskeKayttajienMaara();
            View::make('kayttaja/etusivu.html', array('kayttajienMaara' => $kayttajienMaara));
    }

    public static function kirjautuneenEtusivu() {
        self::check_logged_in();
        $kayttajienMaara = Kayttaja::laskeKayttajienMaara();
        $kayttajatSamallaHakutarkoituksella = Kayttaja::haeKayttajatHakuperusteenMukaan(self::get_user_logged_in()->hakutarkoitusid);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/kirjautuneenEtusivu.html', array('maara' => $lukemattomat,
            'kayttajienMaara' => $kayttajienMaara, 'kayttajat' => $kayttajatSamallaHakutarkoituksella,
            'tarkoitukset' => $tarkoitukset));
    }

    public static function nayta($id) {
        self::check_logged_in();
        $kayttaja = Kayttaja::etsi($id);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/julkinenProfiilisivu.html', array('kayttaja' => $kayttaja,
            'tarkoitukset' => $tarkoitukset, 'maara' => $lukemattomat));
    }

    // metodi luo käyttäjän oman profiilisivun mitä muut käyttäjät eivät näe
    public static function naytaOmaSivu() {
        self::check_logged_in();
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/omaProfiilisivu.html', array('tarkoitukset' => $tarkoitukset,
            'maara' => $lukemattomat));
    }

    public static function muutaSalasana() {
        self::check_logged_in();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/vaihdaSalasana.html', array('maara' => $lukemattomat));
    }

    public static function salasananMuutos() {
        $arvot = $_POST;
        $tiedot = array(
            'id' => self::get_user_logged_in()->id,
            'salasana' => $arvot['salasana2']
        );
        $errors = array();
        if (!self::get_user_logged_in()->kaksiSanaaTarkoittaaSamaa(self::get_user_logged_in()->salasana, $arvot['vanhaSalasana'])) {
            array_push($errors,"Annoit väärin vanhan salasanan. Anna salasana uudelleen.");
        }
        if (!self::get_user_logged_in()->kaksiSanaaTarkoittaaSamaa($arvot['salasana'], $arvot['salasana2'])) {
            array_push($errors, "Kirjoitit kaksi eri salasanaa. Anna salasana uudelleen.");
        }
        if (!empty($errors)) {
            View::make('kayttaja/vaihdaSalasana.html', array('errors' => $errors));
        } else {
            self::get_user_logged_in()->muutaSalasana($tiedot);
            Redirect::to('/kirjautuneenEtusivu', array('message' => 'Salasana vaihdettu!'));
        }
    }

    public static function edit() {
        self::check_logged_in();
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/muokkaa.html', array('tarkoitukset' => $tarkoitukset,
            'maara' => $lukemattomat));
    }

    public static function update() {
        self::check_logged_in();
        $arvot = $_POST;
        $attributes = array(
            'id' => self::get_user_logged_in()->id,
            'kayttajatunnus' => $arvot['kayttajatunnus'],
            'nimi' => $arvot['nimi'],
            'syntymaaika' => date('Y-m-d', strtotime($arvot['vuosi'] . '-' . $arvot['kuukausi'] . '-' . $arvot['paiva'])),
            'sukupuoli' => self::get_user_logged_in()->sukupuoli,
            'paikkakunta' => $arvot['paikkakunta'],
            'omattiedot' => $arvot['omattiedot'],
//            'kuva' => $arvot['kuva'],
            'hakutarkoitusid' => $arvot['etsin']
        );
        $kayttaja = new Kayttaja($attributes);
        $error = null;
        if (!$kayttaja->kaksiSanaaTarkoittaaSamaa(self::get_user_logged_in()->salasana, $arvot['salasana'])) {
            $error = "Väärä salasana. Anna salasana uudelleen.";
        }
        if (!empty($error)) {
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            View::make('kayttaja/muokkaa.html', array('errors' => $error, 'tarkoitukset' => $tarkoitukset,
                'kayttaja' => $attributes));
        } else {
            $kayttaja->muokkaa(self::get_user_logged_in()->id);
            Redirect::to('/omaProfiilisivu', array('message' => 'Profiilisivun muokkaus onnistui!'));
        }
    }

    public static function poistaTunnus() {
        self::check_logged_in();
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/poistaTunnus.html', array('tarkoitukset' => $tarkoitukset,
            'maara' => $lukemattomat));
    }

    public static function poista() {
        self::check_logged_in();
        $arvot = $_POST;
        $error = null;
        if (!self::get_user_logged_in()->kaksiSanaaTarkoittaaSamaa(
                        self::get_user_logged_in()->salasana, $arvot['salasana'])) {
            $error = "Väärä salasana. Anna salasana uudelleen.";
        }
        if (!empty($error)) {
            $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
            $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
            View::make('kayttaja/poistaTunnus.html', array('tarkoitukset' => $tarkoitukset,
                'maara' => $lukemattomat, 'errors' => $error));
        } else {
            $kaikkiSaapuneetViestit = Vastaanottaja::haeSaapuneetViestit(self::get_user_logged_in()->id);
            $kaikkiLahetetytViestit = Viesti::etsiLahettajanViestit(self::get_user_logged_in()->id);
            Vastaanottaja::poistaListanKaikkiKytkokset($kaikkiSaapuneetViestit);
            Vastaanottaja::poistaListanKaikkiKytkokset($kaikkiLahetetytViestit);
            Viesti::poistaListanKaikkiViestit($kaikkiLahetetytViestit);
            Viesti::poistaListanKaikkiViestit($kaikkiSaapuneetViestit);
            $kayttaja = new Kayttaja(array('id' => self::get_user_logged_in()->id));
            $kayttaja->poistaTunnus(self::get_user_logged_in()->id);
            Redirect::to('/kirjautuminen', array('message' => 'Käyttäjätunnus poistettu.'));
        }
    }

}
