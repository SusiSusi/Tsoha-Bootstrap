-- Lisää INSERT INTO lauseet tähän tiedostoon
-- Hakutarkoitus-taulun testidata
INSERT INTO Hakutarkoitus (nimi) VALUES
('Kavereita'),
('Seurustelua'),
('Harrastusseuraa'),
('Matkustusseuraa'),
('Jotain ihan muuta');

-- Kayttaja-taulun testidata
INSERT INTO Kayttaja (kayttajatunnus, nimi, salasana, syntymaaika, sukupuoli, paikkakunta, 
omatTiedot, hakutarkoitusID, oikeudet) VALUES
('PieniSuuri', 'Sami', 'sami1', '1991-03-07', 'M', 'Helsinki', 'Moi!', 2, false),
('Misuliina', 'Miisa', 'Miisa3', '1990-04-15', 'N', 'Helsinki', 'Iloinen ja eläinrakas ...', 1, false),
('Salamanteri', 'Saku', 'Saku1', '1991-03-02', 'M', 'Turku', 'Persoonallinen', 3, false),
('Kisuviini', 'Miia', 'kisu', '1995-03-11', 'N', 'Tampere', 'Kissat on kivoi!', 2, false);

-- Kiinnostukset-taulun testidata
INSERT INTO Kiinnostukset (nimi) VALUES
('Eläimet'),
('Jääkiekko'),
('Matkustelu');

-- Kohteet-taulun testidata
INSERT INTO Kohteet (kayttajaID, kiinnostusID) VALUES
(2, 1),
(2, 3),
(3, 2);

-- Viestit-taulun testidata
INSERT INTO Viesti (aihe, sisalto, aika, luettu, lahettajaID) VALUES
('Terve', 'Lähtisitkö 15.5 klo 12 jonnekkin ...', NOW(), false, 3),
('Profiilisi herätti minussa toiveen', '.. siitä että ihmisiä on olemassa.', NOW(), false, 2),
('Moi?', 'Miten menee?', NOW(), false, 1),
('Lenkille?', 'Mennäänkö lenkille?', NOW(), false, 3),
('Vaalit', 'Meetkö äänestää?', NOW(), false, 4),
('Hei!', 'Moi :)))', NOW(), false, 4); 

-- Vastaanottaja-taulun testidata
INSERT INTO Vastaanottaja (viestiID, kayttajaID) VALUES
(1, 2),
(2, 3),
(3, 4),
(4, 1),
(5, 2),
(6, 3);
