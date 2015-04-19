-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Hakutarkoitus(
id SERIAL PRIMARY KEY, -- pääavain kokonaisluku
nimi varchar(100) NOT NULL -- millaista seuraa haetaan
);

CREATE TABLE Kayttaja(
id SERIAL PRIMARY KEY, -- pääavain kokonaisluku
kayttajatunnus varchar(20) NOT NULL, -- käyttäjätunnus
nimi varchar(30) NOT NULL, -- käyttäjän etunimi
salasana varchar(20) NOT NULL, -- käyttäjätunnuksen salasana
syntymaaika DATE, -- käyttäjän syntymäaika
sukupuoli varchar(1) NOT NULL, -- N = Nainen, M = Mies
paikkakunta varchar(100) NOT NULL, -- paikkakunnan nimi
omatTiedot varchar(300), -- käyttäjän vapaamuotoinen kuvaus itsestään
kuva BYTEA, -- profiilikuva
hakutarkoitusID INTEGER REFERENCES Hakutarkoitus(id), -- viiteavain Hakutarkoitus-tauluun
oikeudet boolean -- true = pääkäyttäjä, false = peruskäyttäjä
);

CREATE TABLE Kiinnostukset(
id SERIAL PRIMARY KEY, -- pääavain kokonaisluku
nimi varchar(100) -- kiinnostuksen nimi, esim. eläimet
);

CREATE TABLE Kohteet(
id SERIAL PRIMARY KEY, -- pääavain kokonaisluku
kayttajaID INTEGER REFERENCES Kayttaja(id), -- viiteavain Kayttaja-tauluun
kiinnostusID INTEGER REFERENCES Kiinnostukset(id) -- viiteavain Kiinnostukset-tauluun
);

CREATE TABLE Viesti(
id SERIAL PRIMARY KEY, -- pääavain kokonaisluku
aihe varchar(150), -- viestin aihe
sisalto varchar(500), -- viestin sisältö
aika TIMESTAMP, -- milloin viesti on lähetetty
luettu boolean, -- true = luettu, false = lukematon
lahettajaID INTEGER REFERENCES Kayttaja(id) -- viiteavain Kayttaja-tauluun
);

CREATE TABLE Vastaanottaja(
id SERIAL PRIMARY KEY, -- pääavain kokonaisluku
viestiID INTEGER REFERENCES Viesti(id), -- viiteavain Viesti-tauluun
kayttajaID INTEGER REFERENCES Kayttaja(id) -- viiteavain Kayttaja-tauluun
);
