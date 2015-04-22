<?php

class Kayttaja extends BaseModel {

    public $id, $kayttajatunnus, $nimi, $salasana, $syntymaaika, $sukupuoli,
            $paikkakunta, $omattiedot, $kuva, $hakutarkoitusid, $oikeudet;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('onkoKayttajatunnusVapaana', 'validateKayttajatunnus');
    }

    public function getKayttajatunnus() {
        return $this->kayttajatunnus;
    }

    public function getKuva() {
        $kuva = asset($this->kuva);
        return $kuva;
    }

    public function getSukupuoli() {
        if ($this->sukupuoli === 'N') {
            return 'Nainen';
        } else {
            return 'Mies';
        }
    }

    public function tulostaPaiva() {
        $paiva = date('d', strtotime($this->syntymaaika));
        return $paiva;
    }

    public function tulostaKuukausi() {
        $kuukausi = date('m', strtotime($this->syntymaaika));
        return $kuukausi;
    }

    public function tulostaSyntymavuosi() {
        $vuosi = date('Y', strtotime($this->syntymaaika));
        return $vuosi;
    }

    public function tulostaSyntymaaika() {
        $kirjaus = date('d.m.Y', strtotime($this->syntymaaika));
        return $kirjaus;
    }

    public function validateKayttajatunnus() {
        $errors = array();
        if (!$this->validatePieninPituus($this->kayttajatunnus, 4)) {
            $errors[] = 'Käyttäjätunnuksen pituuden tulee olla vähintään kuusi merkkiä pitkä';
        }
        if (!$this->validateSuurinPituus($this->kayttajatunnus, 20)) {
            $errors[] = 'Käyttäjätunnuksen pituus saa olla enintään 20 merkkiä pitkä';
        }
        return $errors;
    }

    public function onkoKayttajatunnusVapaana() {
        $errors = array();
        $kysely = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE kayttajatunnus = :kayttajatunnus LIMIT 1');
        $kysely->execute(array('kayttajatunnus' => $this->kayttajatunnus));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $errors[] = 'Käyttäjätunnus ' . $this->kayttajatunnus . ' on jo käytössä. Valitse joku muu.';
        }
        return $errors;
    }

    public function salasananVarmistusKirjoitettuOikein($salasana, $salasana2) {
        if (!$this->kaksiSanaaTarkoittaaSamaa($salasana, $salasana2)) {
            return false;
        }
        return true;
    }

    public function validateSalasana() {
        $errors = array();
        if (!$this->validatePieninPituus($this->salasana, 6)) {
            $errors[] = 'Salasanan pituuden tulee olla vähintään kuusi merkkiä pitkä';
        }
        return $errors;
    }

    public function talleta() {
        $kysely = DB::connection()->prepare('INSERT INTO Kayttaja (kayttajatunnus, nimi,
                salasana, syntymaaika, sukupuoli, paikkakunta, omattiedot, hakutarkoitusid,
                kuva, oikeudet) 
                VALUES (:kayttajatunnus, :nimi, :salasana, :syntymaaika, :sukupuoli, 
                :paikkakunta, :omattiedot, :hakutarkoitusid, :kuva, :oikeudet) RETURNING id ');
        $kysely->execute(array('kayttajatunnus' => $this->kayttajatunnus, 'nimi' => $this->nimi,
            'salasana' => $this->salasana, 'syntymaaika' => $this->syntymaaika,
            'sukupuoli' => $this->sukupuoli, 'paikkakunta' => $this->paikkakunta,
            'omattiedot' => $this->omattiedot, 'hakutarkoitusid' => $this->hakutarkoitusid,
            'kuva' => $this->kuva, 'oikeudet' => 'false'));
        $rivi = $kysely->fetch();
//        Kint::trace();
//        Kint::dump($rivi);
        $this->id = $rivi['id'];
    }

    public static function kaikkiKayttajat() {
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Kayttaja ORDER BY id desc');
        $kysely->execute();
        $rivit = $kysely->fetchAll();
        $kayttajat = array();

        foreach ($rivit as $rivi) {
            $kayttajat[] = new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus'],
                'nimi' => $rivi['nimi'],
                'salasana' => $rivi['salasana'],
                'syntymaaika' => $rivi['syntymaaika'],
                'sukupuoli' => $rivi['sukupuoli'],
                'paikkakunta' => $rivi['paikkakunta'],
                'omattiedot' => $rivi['omattiedot'],
                'kuva' => $rivi['kuva'],
                'hakutarkoitusid' => $rivi['hakutarkoitusid'],
                'oikeudet' => $rivi['oikeudet']
            ));
        }

        return $kayttajat;
    }

    public static function etsi($id) {
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Kayttaja WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $kayttaja = new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus'],
                'nimi' => $rivi['nimi'],
                'salasana' => $rivi['salasana'],
                'syntymaaika' => $rivi['syntymaaika'],
                'sukupuoli' => $rivi['sukupuoli'],
                'paikkakunta' => $rivi['paikkakunta'],
                'omattiedot' => $rivi['omattiedot'],
                'kuva' => $rivi['kuva'],
                'hakutarkoitusid' => $rivi['hakutarkoitusid'],
                'oikeudet' => $rivi['oikeudet']
            ));

            return $kayttaja;
        }

        return null;
    }

    public function muokkaa($id) {
        $kysely = DB::connection()->prepare('UPDATE Kayttaja
                SET nimi = :nimi, syntymaaika = :syntymaaika,  
                paikkakunta = :paikkakunta, omattiedot = :omattiedot, 
                hakutarkoitusid = :hakutarkoitusid, kuva = :kuva
                WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id, 'nimi' => $this->nimi,
            'syntymaaika' => $this->syntymaaika,
            'paikkakunta' => $this->paikkakunta,
            'omattiedot' => $this->omattiedot, 'hakutarkoitusid' => $this->hakutarkoitusid,
            'kuva' => $this->kuva));
        $rivi = $kysely->fetch();
//        Kint::trace();
//        Kint::dump($rivi);
        $this->id = $rivi['id'];
    }

    public function poistaTunnus($id) {
        $kysely = DB::connection()->prepare('DELETE FROM Kayttaja
                WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();
//        Kint::trace();
//        Kint::dump($rivi);
    }

    public function authenticate($kayttajatunnus, $salasana) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kayttaja
                WHERE kayttajatunnus = :kayttajatunnus AND salasana = :salasana LIMIT 1');
        $kysely->execute(array('kayttajatunnus' => $kayttajatunnus, 'salasana' => $salasana));
        $rivi = $kysely->fetch();
//        Kint::trace();
//        Kint::dump($rivi);
        if ($rivi) {
            $kayttaja = new Kayttaja(array(
                'id' => $rivi['id'],
                'kayttajatunnus' => $rivi['kayttajatunnus'],
                'nimi' => $rivi['nimi'],
                'salasana' => $rivi['salasana'],
                'syntymaaika' => $rivi['syntymaaika'],
                'sukupuoli' => $rivi['sukupuoli'],
                'paikkakunta' => $rivi['paikkakunta'],
                'omattiedot' => $rivi['omattiedot'],
                'kuva' => $rivi['kuva'],
                'hakutarkoitusid' => $rivi['hakutarkoitusid'],
                'oikeudet' => $rivi['oikeudet']
            ));
            return $kayttaja;
        } else {
            return null;
        }
    }

}
