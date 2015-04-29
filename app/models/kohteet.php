<?php

class Kohteet extends BaseModel {

    public $kayttajaid, $kiinnostusid, $nakyvyys;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function haeKayttajanKiinnostukset($kayttajaid) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kohteet WHERE kayttajaid = :kayttajaid
                ORDER BY id desc');
        $kysely->execute(array('kayttajaid' => $kayttajaid));
        $rivit = $kysely->fetchAll();
        $kiinnostukset = array();
        foreach ($rivit as $rivi) {
            $kiinnostukset[] = $rivi;
        }
        return $kiinnostukset;
    }

    public function yhdistaKiinnostusKayttajaan() {
        $kysely = DB::connection()->prepare('INSERT INTO Kohteet (kayttajaid, kiinnostusid, nakyvyys)
                 VALUES (:kayttajaid, :kiinnostusid, :nakyvyys) RETURNING id');
        $kysely->execute(array('kayttajaid' => $this->kayttajaid, 'kiinnostusid' => $this->kiinnostusid,
            'nakyvyys' => $this->nakyvyys));
        $rivi = $kysely->fetch();
        $this->id = $rivi['id'];
    }

    public static function onkoKiinnostusYhdistettyJoKayttajaan($arvot) {
        $kysely = DB::connection()->prepare('SELECT kiinnostusid FROM Kohteet WHERE kayttajaid = :kayttajaid');
        $kysely->execute(array('kayttajaid' => $arvot['kayttajaid']));
        $rivit = $kysely->fetchAll();
        if ($rivit) {
            foreach ($rivit as $rivi) {
                if ($rivi[0] == $arvot['kiinnostusid']) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function poistaKiinnostuksenYhdistys($kiinnostusid) {
        $kysely = DB::connection()->prepare('DELETE FROM Kohteet
                WHERE kiinnostusid = :kiinnostusid RETURNING id');
        $kysely->execute(array('kiinnostusid' => $kiinnostusid));
        $rivi = $kysely->fetch();
    }

    public static function poistaListanKaikkiKytkokset($lista) {
        Kint::dump($lista);
        if ($lista) {
            foreach ($lista as $kiinnostus) {
                Kint::dump($kiinnostus);
                Kohteet::poistaKiinnostuksenYhdistys($kiinnostus['kiinnostusid']);
            }
        }
    }

    public static function muutaKiinnostuksenNakyvyys($arvot) {
        $kysely = DB::connection()->prepare('UPDATE Kohteet SET nakyvyys = :nakyvyys
                WHERE kiinnostusid = :kiinnostusid AND kayttajaid = :kayttajaid RETURNING id');
        $kysely->execute(array('kiinnostusid' => $arvot['kiinnostusid'],
            'kayttajaid' => $arvot['kayttajaid'], 'nakyvyys' => $arvot['nakyvyys']));
        $rivi = $kysely->fetch();
    }

}
