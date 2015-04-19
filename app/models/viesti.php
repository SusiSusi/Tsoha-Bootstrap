<?php

class Viesti extends BaseModel {

    public $id, $aihe, $sisalto, $aika, $luettu, $lahettajaid;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public function viestiLuettu($id) {
        $kysely = DB::connection()->prepare('UPDATE Viesti SET luettu = :luettu WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id, 'luettu' => 'true'));
        $rivi = $kysely->fetch();
//        Kint::trace();
//        Kint::dump($rivi);
        $this->id = $rivi['id'];
    }

    public static function asetaViestiLukemattomaksi($id) {
        $kysely = DB::connection()->prepare('UPDATE Viesti SET luettu = :luettu WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id, 'luettu' => 'false'));
        $rivi = $kysely->fetch();
        $this->id = $rivi['id'];
    }

    public static function haeKaikkiViestit() {
        $kysely = DB::connection()->prepare('SELECT * FROM Viesti ORDER BY aika');
        $kysely->execute();
        $rivit = $kysely->fetchAll();
        $viestit = array();

        foreach ($rivit as $rivi) {
            $viestit[] = new Viesti(array(
                'id' => $rivi['id'],
                'aihe' => $rivi['aihe'],
                'sisalto' => $rivi['sisalto'],
                'aika' => $rivi['aika'],
                'luettu' => $rivi['luettu'],
                'lahettajaID' => $rivi['lahettajaID']
            ));
        }

        return $viestit;
    }

    public static function etsiViesti($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Viesti WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $viesti = new Viesti(array(
                'id' => $id,
                'aihe' => $rivi['aihe'],
                'sisalto' => $rivi['sisalto'],
                'aika' => $rivi['aika'],
                'luettu' => $rivi['luettu'],
                'lahettajaid' => $rivi['lahettajaid']
            ));

            return $viesti;
        }

        Kint::trace();
        Kint::dump($viesti);

        return null;
    }

    public static function etsiLahettajanViestit($lahettajaid) {
        $kysely = DB::connection()->prepare('SELECT * FROM Viesti WHERE lahettajaid = :lahettajaid ORDER BY aika');
        $kysely->execute(array('lahettajaid' => $lahettajaid));
        $rivit = $kysely->fetchAll();

        if ($rivit) {
            foreach ($rivit as $rivi) {
                $viestit[] = new Viesti(array(
                    'id' => $rivi['id'],
                    'aihe' => $rivi['aihe'],
                    'sisalto' => $rivi['sisalto'],
                    'aika' => $rivi['aika'],
                    'luettu' => $rivi['luettu'],
                    'lahettajaid' => $rivi['lahettajaid']
                ));
            }
            krsort($viestit);
            return $viestit;
        }

        return null;
    }

    public static function poistaViesti($id) {
        $kysely = DB::connection()->prepare('DELETE FROM Viesti
                WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();
//        Kint::trace();
//        Kint::dump($rivi);
    }

    public static function poistaListanKaikkiViestit($lista) {
        if ($lista) {
            foreach ($lista as $viesti) {
                Viesti::poistaViesti($viesti->id);
            }
        }
    }

    public function viestinAika() {
        $viestinAika = date('H:i:s', strtotime($this->aika));
        return $viestinAika;
    }

    public function viestinPaiva() {
        $viestinPaiva = date('d.m.Y', strtotime($this->aika));
        return $viestinPaiva;
    }

    public function viestinTila() {
        $onkoLuettu = $this->luettu;
        return $onkoLuettu;
    }

    public function tallennaViesti() {
        $kysely = DB::connection()->prepare('INSERT INTO Viesti (aihe, sisalto, aika, luettu, 
                lahettajaid) VALUES (:aihe, :sisalto, :aika, :luettu, :lahettajaid) RETURNING id');
        $kysely->execute(array('aihe' => $this->aihe, 'sisalto' => $this->sisalto,
            'aika' => $this->aika, 'luettu' => 'false', 'lahettajaid' => $this->lahettajaid));
        $rivi = $kysely->fetch();
//        Kint::trace();
//        Kint::dump($rivi);
        $this->id = $rivi['id'];
    }

}
