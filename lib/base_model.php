<?php

class BaseModel {

    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null) {
        // Käydään assosiaatiolistan avaimet läpi
        foreach ($attributes as $attribute => $value) {
            // Jos avaimen niminen attribuutti on olemassa...
            if (property_exists($this, $attribute)) {
                // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
                $this->{$attribute} = $value;
            }
        }
    }

    public function errors() {
        // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
        $errors = array();

        foreach ($this->validators as $validator) {
            // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
            $errors = array_merge($errors, $validator);
        }

        return $errors;
    }

    public function validate_string_length($sana, $minPituus, $maxPituus) {
        $errors = array();
        if ($sana == '' || $sana == null) {
            $errors[] = 'Nimi ei saa olla tyhjä!';
        }
        if (strlen($sana) < $minPituus) {
            $errors[] = ' pituuden tulee olla vähintään ' . $minPituus . ' merkkiä pitkä';
        }
        if ($maxPituus != -1) {
            if (strlen($sana) > $maxPituus) {
                $errors[] = ' pituus saa olla enintään ' . $maxPituus . ' merkkiä pitkä';
            }
        }
        return $errors;
    }

}
