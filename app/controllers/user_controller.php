<?php

class UserController extends BaseController {
   
    public static function login() {
        View::make('kayttaja/kirjautuminen.html');
    }
    
    public static function handle_login() {
        $arvot = $_POST;
        $kayttaja = Kayttaja::authenticate($arvot['kayttajatunnus'], $arvot['salasana']);
        if (!$kayttaja) {
            View::make('kayttaja/kirjautuminen.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'kayttajatunnus' => $arvot['kayttajatunnus']));
        } else {
            $_SESSION['kayttajatunnus'] = $kayttaja->id;
            Redirect::to('/kirjautuneenEtusivu', array('message' => 'Tervetuloa ' . $kayttaja->nimi . '!'));
        }
    }
    
    public static function logout() {
        $_SESSION['kayttajatunnus'] = null;
        Redirect::to('/kirjautuminen', array('message' => 'Olet kirjautunut ulos!'));
    }
}

