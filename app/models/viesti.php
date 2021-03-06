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
        $this->id = $rivi['id'];
    }

    public static function asetaViestiLukemattomaksi($id) {
        $kysely = DB::connection()->prepare('UPDATE Viesti SET luettu = :luettu WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id, 'luettu' => 'false'));
        $rivi = $kysely->fetch();
    }

    public static function haeKaikkiViestit() {
        $kysely = DB::connection()->prepare('SELECT * FROM Viesti ORDER BY aika');
        $kysely->execute();
        $rivit = $kysely->fetchAll();
        $viestit = array();
        foreach ($rivit as $rivi) {
            $viestit[] = new Viesti($rivi);
        }
        return $viestit;
    }

    public static function etsiViesti($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Viesti WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $viesti = new Viesti($rivi);
            return $viesti;
        }
        return null;
    }

    public static function etsiLahettajanViestit($lahettajaid) {
        $kysely = DB::connection()->prepare('SELECT * FROM Viesti WHERE lahettajaid = :lahettajaid ORDER BY aika');
        $kysely->execute(array('lahettajaid' => $lahettajaid));
        $rivit = $kysely->fetchAll();

        if ($rivit) {
            foreach ($rivit as $rivi) {
                $viestit[] = new Viesti($rivi);
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

    public function viestinLahettaja() {
        return $this->lahettajaid;
    }

    public function tallennaViesti() {
        $kysely = DB::connection()->prepare('INSERT INTO Viesti (aihe, sisalto, aika, luettu, 
                lahettajaid) VALUES (:aihe, :sisalto, :aika, :luettu, :lahettajaid) RETURNING id');
        $kysely->execute(array('aihe' => $this->aihe, 'sisalto' => $this->sisalto,
            'aika' => $this->aika, 'luettu' => 'false', 'lahettajaid' => $this->lahettajaid));
        $rivi = $kysely->fetch();
        $this->id = $rivi['id'];
    }

    // tätä metodia ei käytetä (vielä?) mihinkään
    public function muokkaa($id) {
        $kysely = DB::connection()->prepare('UPDATE Viesti
                SET aihe = :aihe, sisalto = :sisalto,  
                aika = :aika, luettu = :luettu, 
                lahettajaid = :lahettajaid
                WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id, 'aihe' => $this->aihe,
            'sisalto' => $this->sisalto,
            'aika' => $this->aika,
            'luettu' => 'false', 'lahettajaid' => $this->lahettajaid));
        $rivi = $kysely->fetch();
        $this->id = $rivi['id'];
    }
}
