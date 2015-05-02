<?php

class Kayttaja extends BaseModel {

    public $id, $kayttajatunnus, $nimi, $salasana, $syntymaaika, $sukupuoli,
            $paikkakunta, $omattiedot, $kuva, $hakutarkoitusid, $oikeudet;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('onkoKayttajatunnusVapaana', 'validateKayttajatunnus',
            'validateSalasana');
    }

    public function getKayttajatunnus() {
        return $this->kayttajatunnus;
    }

    public function getKuva() {
        $kuva = $this->kuva;
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
            $errors[] = 'Käyttäjätunnuksen pituuden tulee olla vähintään neljä merkkiä pitkä.';
        }
        if (!$this->validateSuurinPituus($this->kayttajatunnus, 20)) {
            $errors[] = 'Käyttäjätunnuksen pituus saa olla enintään 20 merkkiä pitkä.';
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

    public function uudenSalasananPituusOk($salasana) {
        if (!$this->validatePieninPituus($salasana, 4)) {
            return false;
        }
        return true;
    }

    public function validateSalasana() {
        $errors = array();
        if (!$this->validatePieninPituus($this->salasana, 4)) {
            $errors[] = 'Salasanan pituuden tulee olla vähintään neljä merkkiä pitkä.';
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
        $this->id = $rivi['id'];
    }

    // hakee kaikki käyttäjät ja lajittelee ne omille sivuilleen, yhdellä sivulla 10 käyttäjää
    public static function kaikkiKayttajat($options) {
        if (isset($options['sivu']) && isset($options['sivunKoko'])) {
            $sivu = $options['sivu'];
            $sivunKoko = $options['sivunKoko'];
        } else {
            $sivu = 1;
            $sivunKoko = 10;
        }
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Kayttaja ORDER BY id desc 
                LIMIT :limit OFFSET :offset');
        $kysely->execute(array('limit' => $sivunKoko, 'offset' => $sivunKoko * ($sivu - 1)));
        $rivit = $kysely->fetchAll();
        $kayttajat = array();
        foreach ($rivit as $rivi) {
            $kayttajat[] = new Kayttaja($rivi);
        }
        return $kayttajat;
    }

    // hakee kaikki käyttäjät tietokannasta - tätä metodia käyttää muut metodit käyttäjätietojen tulostukseen
    public static function kaikkiKayttajatPerus() {
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Kayttaja ORDER BY id desc');
        $kysely->execute();
        $rivit = $kysely->fetchAll();
        $kayttajat = array();

        foreach ($rivit as $rivi) {
            $kayttajat[] = new Kayttaja($rivi);
        }
        return $kayttajat;
    }

    // hakee tietokannasta kaikki ne käyttäjät, jotka toteuttavat hakukriteerin
    public static function kaikkiKayttajatHaulla($options) {
        $kyselyHakusanalla = 'SELECT distinct * FROM Kayttaja';
        $vaihtoehdot = array();
        if (isset($options)) {
            if ($options['sukupuoli'] == 'E') {
                $options['sukupuoli'] = '';
            }
            if ($options['hakutarkoitus'] == -1) { // haun tulokseksi sallittu käyttäjät kaikilta hakutarkoituksilta
                $kyselyHakusanalla .= ' WHERE kayttajatunnus LIKE :tunnus AND sukupuoli LIKE :puoli
                     AND syntymaaika BETWEEN :vuosi1 AND :vuosi2 AND paikkakunta LIKE :kunta
                     AND hakutarkoitusid BETWEEN :minArvo AND :maxArvo';
                $vaihtoehdot['minArvo'] = Hakutarkoitus::pieninHakutarkoitusId();
                $vaihtoehdot['maxArvo'] = Hakutarkoitus::suurinHakutarkoitusId();
            } else {
                $kyselyHakusanalla .= ' WHERE kayttajatunnus LIKE :tunnus AND sukupuoli LIKE :puoli
                     AND syntymaaika BETWEEN :vuosi1 AND :vuosi2 AND paikkakunta LIKE :kunta
                     AND hakutarkoitusid = :tarkoitus';
                $vaihtoehdot['tarkoitus'] = $options['hakutarkoitus'];
            }
            $vaihtoehdot['tunnus'] = '%' . $options['kayttajatunnus'] . '%';
            $vaihtoehdot['puoli'] = '%' . $options['sukupuoli'] . '%';
            $vaihtoehdot['vuosi1'] = '01.01.' . $options['vuosi1'];
            $vaihtoehdot['vuosi2'] = '31.12.' . $options['vuosi2'];
            $vaihtoehdot['kunta'] = '%' . $options['paikkakunta'] . '%';
        }
        $kyselyHakusanalla .= ' ORDER BY kayttajatunnus';
        $kysely = DB::connection()->prepare($kyselyHakusanalla);
        if ($options == null) {
            $kysely->execute();
        } else {
            $kysely->execute($vaihtoehdot);
        }
        $rivit = $kysely->fetchAll();
        $kayttajat = array();
        foreach ($rivit as $rivi) {
            $kayttajat[] = new Kayttaja($rivi);
        }
        return $kayttajat;
    }

    public static function etsi($id) {
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Kayttaja WHERE id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $kayttaja = new Kayttaja($rivi);
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
        $this->id = $rivi['id'];
    }

    public static function muutaSalasana($tiedot) {
        $kysely = DB::connection()->prepare('UPDATE Kayttaja
                SET salasana = :salasana WHERE id = :id RETURNING id');
        $kysely->execute(array('salasana' => $tiedot['salasana'], 'id' => $tiedot['id']));
        $rivi = $kysely->fetch();
    }

    public function poistaTunnus($id) {
        $kysely = DB::connection()->prepare('DELETE FROM Kayttaja
                WHERE id = :id RETURNING id');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();
    }

    public function authenticate($kayttajatunnus, $salasana) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kayttaja
                WHERE kayttajatunnus = :kayttajatunnus AND salasana = :salasana LIMIT 1');
        $kysely->execute(array('kayttajatunnus' => $kayttajatunnus, 'salasana' => $salasana));
        $rivi = $kysely->fetch();
        if ($rivi) {
            $kayttaja = new Kayttaja($rivi);
            return $kayttaja;
        } else {
            return null;
        }
    }

    public static function laskeKayttajienMaara() {
        $kysely = DB::connection()->prepare('SELECT count(id) FROM Kayttaja');
        $kysely->execute();
        $rivi = $kysely->fetch();
        if ($rivi) {
            return $rivi[0];
        }
        return null;
    }

    // haetaan kirjautuneen etusivulle käyttäjät jotka etsivät seuraa samalla hakutarkoituksella
    // sivulle tulostetaan max. 5 uusinta käyttäjää ko. hakutarkoituksella
    public static function haeKayttajatHakuperusteenMukaan($hakutarkoitusid) {
        $kysely = DB::connection()->prepare('SELECT distinct * FROM Kayttaja WHERE 
                 hakutarkoitusid = :hakutarkoitusid ORDER BY id desc LIMIT 5');
        $kysely->execute(array('hakutarkoitusid' => $hakutarkoitusid));
        $rivit = $kysely->fetchAll();
        $kayttajat = array();
        if ($rivit) {
            foreach ($rivit as $rivi) {
                $kayttajat[] = new Kayttaja($rivi);
            }
            return $kayttajat;
        }
        return null;
    }

}
