<?php

class Kiinnostukset extends BaseModel {

    public $id, $nimi;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function haeKaikkiKiinnostukset() {
        $kysely = DB::connection()->prepare('SELECT * FROM Kiinnostukset ORDER BY nimi');
        $kysely->execute();
        $rivit = $kysely->fetchAll();
        $kiinnostukset = array();
        foreach ($rivit as $rivi) {
            $kiinnostukset[] = new Kiinnostukset($rivi);
        }
        return $kiinnostukset;
    }

    public static function etsiKiinnostus($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kiinnostukset WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $kiinnostus = new Kiinnostukset($rivi);
            return $kiinnostus;
        }
        return null;
    }

    public function uusiKiinnostus() {
        $kysely = DB::connection()->prepare('INSERT INTO Kiinnostukset (nimi) 
                VALUES (:nimi) RETURNING id');
        $kysely->execute(array('nimi' => $this->nimi));
        $rivi = $kysely->fetch();
        $this->id = $rivi['id'];
    }

    public static function poistaKiinnostus($id) {
        $kysely = DB::connection()->prepare('DELETE FROM Kiinnostukset
                WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();
    }

//    public static function poistaListanKaikkiKiinnostukset($lista) {
//        if ($lista) {
//            foreach ($lista as $kiinnostus) {
//                Kiinnostukset::poistaKiinnostus($kiinnostus->id);
//            }
//        }
//    }

}
