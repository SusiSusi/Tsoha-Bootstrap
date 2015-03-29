<?php

class HelloWorldController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        echo 'Tämä on etusivu!';
    }

    public static function sandbox() {
        $miisis = Hakutarkoitus::etsiHakutarkoitus(1);
        $kayttajat = Hakutarkoitus::kaikkiHakutarkoitukset();

        Kint::dump($kayttajat);
        Kint::dump($miisis);
    }

    public static function login() {
        View::make('suunnitelmat/kirjautumissivu.html');
    }

    public static function rekisterointi() {
        View::make('suunnitelmat/rekisterointiMalli.html');
    }

    public static function etusivu() {
        View::make('suunnitelmat/etusivu.html');
    }

    public static function listaus() {
        View::make('suunnitelmat/kayttajienListaus.html');
    }

    public static function muokkaa() {
        View::make('suunnitelmat/muokkaaProfiilia.html');
    }

}
