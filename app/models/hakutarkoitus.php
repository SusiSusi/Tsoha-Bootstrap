<?php

class Hakutarkoitus extends BaseModel {

    public $id, $nimi;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public function getNimi() {
        return $this->nimi;
    }

    public static function pieninHakutarkoitusId() {
        $kysely = DB::connection()->prepare('SELECT min(id) FROM Hakutarkoitus LIMIT 1');
        $kysely->execute();
        $rivi = $kysely->fetch();
        if ($rivi) {
            $minArvo = $rivi[0];
            return $minArvo;
        }
        return null;
    }

    public static function suurinHakutarkoitusId() {
        $kysely = DB::connection()->prepare('SELECT max(id) FROM Hakutarkoitus LIMIT 1');
        $kysely->execute();
        $rivi = $kysely->fetch();
        if ($rivi) {
            $maxArvo = $rivi[0];
            return $maxArvo;
        }
        return null;
    }

    public static function kaikkiHakutarkoitukset() {
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Hakutarkoitus ORDER BY nimi');
        $kysely->execute();
        $rivit = $kysely->fetchAll();
        $tarkoitukset = array();

        foreach ($rivit as $rivi) {
            $tarkoitukset[] = new Hakutarkoitus(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']
            ));
        }

        return $tarkoitukset;
    }

    public static function etsiHakutarkoitus($id) {
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Hakutarkoitus
                WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $tarkoitus = new Hakutarkoitus(array(
                'id' => $rivi['id'],
                'nimi' => $rivi['nimi']
            ));
            return $tarkoitus;
        }
        return null;
    }

}
