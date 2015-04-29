<?php

class KiinnostuksetController extends BaseController {

    public static function poistaKiinnostus($kiinnostusid) {
        self::check_logged_in();
        Kohteet::poistaKiinnostuksenYhdistys($kiinnostusid);
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $kiinnostukset = Kohteet::haeKayttajanKiinnostukset(self::get_user_logged_in()->id);
        $kiinnostustenNimet = Kiinnostukset::haeKaikkiKiinnostukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/omaProfiilisivu.html', array('tarkoitukset' => $tarkoitukset,
            'maara' => $lukemattomat, 'kiinnostukset' => $kiinnostukset,
            'kiinnostustenNimet' => $kiinnostustenNimet, 'message' => 'Kiinnostus poistettu!'));
    }

    public static function lisaaKiinnostus() {
        self::check_logged_in();
        $arvot = $_GET;
        $options = array();
        $error = null;
        $message = null;
        if (isset($arvot['kiinnostus'])) {
            $options['kayttajaid'] = self::get_user_logged_in()->id;
            $options['kiinnostusid'] = $arvot['kiinnostus'];
            $onkoKiinnostusJoKayttajalla = Kohteet::onkoKiinnostusYhdistettyJoKayttajaan($options);
            if (!$onkoKiinnostusJoKayttajalla) {
                $error = 'Sinulla on jo tämä kiinnostus!';
            } else {
                if ($arvot['nakyvyys'] == 'K') {
                    $options['nakyvyys'] = 'true';
                } else {
                    $options['nakyvyys'] = 'false';
                }
                $kohde = new Kohteet($options);
                $kohde->yhdistaKiinnostusKayttajaan();
                $message = 'Kiinnostus lisätty!';
            }
        }
        $kiinnostukset = Kiinnostukset::haeKaikkiKiinnostukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kiinnostukset/lisaaKiinnostus.html', array('kiinnostukset' => $kiinnostukset,
            'maara' => $lukemattomat, 'error' => $error, 'message' => $message));
    }

    public static function muutaKiinnostuksenNakyvyys($kiinnostusid) {
        self::check_logged_in();
        $arvot = $_POST;
        $options = array();
        if ($arvot) {
            $options['kiinnostusid'] = $kiinnostusid;
            $options['kayttajaid'] = self::get_user_logged_in()->id;
            if ($arvot['nakyvyys'] == 1) {
                $options['nakyvyys'] = 'false';
                Kohteet::muutaKiinnostuksenNakyvyys($options);
            } else {
                $options['nakyvyys'] = 'true';
                Kohteet::muutaKiinnostuksenNakyvyys($options);
            }
        }
        $tarkoitukset = Hakutarkoitus::kaikkiHakutarkoitukset();
        $kiinnostukset = Kohteet::haeKayttajanKiinnostukset(self::get_user_logged_in()->id);
        $kiinnostustenNimet = Kiinnostukset::haeKaikkiKiinnostukset();
        $lukemattomat = Vastaanottaja::lukemattomienMaara(self::get_user_logged_in());
        View::make('kayttaja/omaProfiilisivu.html', array('tarkoitukset' => $tarkoitukset,
            'maara' => $lukemattomat, 'kiinnostukset' => $kiinnostukset,
            'kiinnostustenNimet' => $kiinnostustenNimet));
    }

}
