<?php

  class BaseController{

    public static function get_user_logged_in(){
      // Toteuta kirjautuneen käyttäjän haku tähän
        if (isset($_SESSION['kayttajatunnus'])) {
            $kayttajaid = $_SESSION['kayttajatunnus'];
            $kayttaja = Kayttaja::etsi($kayttajaid);
            return $kayttaja;
        }
      return null;
    }

    public static function check_logged_in(){
      // Jos käyttäjä ei ole kirjautunut sisään, ohjaa hänet toiselle sivulle (esim. kirjautumissivulle).
        if(!isset($_SESSION['kayttajatunnus'])) {
            Redirect::to('/kirjautuminen', array('message' => 'Kirjaudu ensin sisään!'));
        }
    }

  }
