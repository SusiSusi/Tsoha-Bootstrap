<?php

class Vastaanottaja extends BaseModel {

    public $viestiid, $kayttajaid;

    public function __construct($viestiid, $kayttajaid) {
        $this->viestiid = $viestiid;
        $this->kayttajaid = $kayttajaid;
    }

    public static function etsiViestinVastaanottaja($viestiid) {
        $kysely = DB::connection()->prepare('SELECT viestiid, kayttajaid FROM Vastaanottaja WHERE viestiid = :viestiid');
        $kysely->execute(array('viestiid' => $viestiid));
        $rivit = $kysely->fetch();
        if ($rivit) {
            return $rivit;
        }
        return null;
    }

    public static function kaikkiLahetetytViestit($lahetetytViestit) {
        $vastaanottajat = array();

        if ($lahetetytViestit) {
            foreach ($lahetetytViestit as $viesti) {
                $vastaanottajat[] = Vastaanottaja::etsiViestinVastaanottaja($viesti->id);
            }
//        Kint::dump($vastaanottajat);
            return $vastaanottajat;
        }
        return null;
    }

    public static function haeSaapuneetViestit($kayttajaid) {
        $kysely = DB::connection()->prepare('SELECT * FROM Vastaanottaja WHERE kayttajaid = :kayttajaid');
        $kysely->execute(array('kayttajaid' => $kayttajaid));
        $rivit = $kysely->fetchAll();
        $viestit = array();
//        Kint::dump($rivit);

        foreach ($rivit as $rivi) {
            $viestit[] = Viesti::etsiViesti($rivi['viestiid']);
        }

//         Kint::trace();
//        Kint::dump($viestit);
        krsort($viestit);
        return $viestit;
    }

    public function yhdistaViestiKayttajaan() {
        $kysely = DB::connection()->prepare('INSERT INTO Vastaanottaja (viestiid, kayttajaid)
                 VALUES (:viestiid, :kayttajaid) RETURNING id');
        $kysely->execute(array('viestiid' => $this->viestiid, 'kayttajaid' => $this->kayttajaid));
        $rivi = $kysely->fetch();
//        Kint::dump($kysely);
//        Kint::trace();
//        Kint::dump($rivi);
        $this->id = $rivi['id'];
    }

    public static function lukemattomienMaara($kayttajaid) {
        $viestit = Vastaanottaja::haeSaapuneetViestit($kayttajaid->id);
        $lukemattomat = 0;
        if ($viestit) {
            foreach ($viestit as $viestit) {
                if ($viestit->viestinTila() == FALSE) {
                    $lukemattomat++;
                }
            }
        }
        return $lukemattomat;
    }

    public static function poistaViestiKytkos($viestiid) {
        $kysely = DB::connection()->prepare('DELETE FROM Vastaanottaja
                WHERE viestiid = :viestiid RETURNING id');
        $kysely->execute(array('viestiid' => $viestiid));
        $rivi = $kysely->fetch();
    }

    public static function poistaListanKaikkiKytkokset($lista) {
        if ($lista) {
            foreach ($lista as $viesti) {
                Vastaanottaja::poistaViestiKytkos($viesti->id);
            }
        }
    }

    public static function kaikkiViestitKayttajanKanssa($kayttaja1, $kayttaja2) {
        $kysely = DB::connection()->prepare('SELECT distinct Viesti.id, Viesti.aihe, Viesti.sisalto,
                Viesti.aika, Viesti.luettu, Viesti.lahettajaid, Vastaanottaja.kayttajaid AS vastot
                FROM Viesti, Vastaanottaja WHERE
                lahettajaid = :lahettajaid and 
                kayttajaid = :kayttajaid');
        $kysely->execute(array('lahettajaid' => $kayttaja1, 'kayttajaid' => $kayttaja2));
        $rivit = $kysely->fetchAll();
//        Kint::dump($kayttaja1);
//        Kint::dump($kayttaja2);
//        Kint::dump($rivit);
        
        $viestit = array();
        foreach ($rivit as $rivi) {
            $viestit[] = $rivi;
        }
//        Kint::dump($viestit);
        return $viestit;
    }
}
