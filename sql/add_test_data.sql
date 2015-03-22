-- Lisää INSERT INTO lauseet tähän tiedostoon
-- Hakutarkoitus-taulun testidata
INSERT INTO Hakutarkoitus (nimi) VALUES
('Kavereita'),
('Seurustelua'),
('Harrastusseuraa'),
('Jotain ihan muuta');

-- Kayttaja-taulun testidata
INSERT INTO Kayttaja (kayttajatunnus, nimi, salasana, syntymaaika, sukupuoli, paikkakunta, 
omatTiedot, hakutarkoitusID, oikeudet) VALUES
('Misu', 'Miisa', 'Miisa3', '1990-04-15', 'N', 'Helsinki', 'Iloinen ja eläinrakas ...', 1, false),
('Salamanteri', 'Saku', 'Saku1', '1991-03-02', 'M', 'Turku', 'Persoonallinen', 3, false);

-- JulkinenProfiilisivu-taulun testidata
INSERT INTO JulkinenProfiilisivu (kayttajaID, sisalto) VALUES
(5, 'Minun profiilisivut: ja mitä kaikkea tänne sitten voi tulostella..'),
(6, 'Minun profiilisivut: ja mitä kaikkea tänne sitten voi tulostella..');

-- SalainenSivu-taulun testidata
INSERT INTO SalainenSivu (kayttajaID, sisalto) VALUES
(5, 'Salaisuuteni: En ole alunperin Helsingistä');

-- Kiinnostukset-taulun testidata
INSERT INTO Kiinnostukset (nimi) VALUES
('Eläimet'),
('Jääkiekko'),
('Matkustelu');

-- Kohteet-taulun testidata
INSERT INTO Kohteet (kayttajaID, kiinnostusID) VALUES
(5, 1),
(5, 3),
(6, 2);

-- Viestit-taulun testidata
INSERT INTO Viesti (aihe, sisalto, aika, luettu) VALUES
('Terve', 'Lähtisitkö 15.5 klo 12 jonnekkin ...', NOW(), false),
('Profiilisi herätti minussa toiveen', '.. siitä että ihmisiä on olemassa.', '2015-03-15', true),
('Lenkille?', 'Mennäänkö lenkille?', '2015-03-22', false); 

-- Vastaanottaja-taulun testidata
INSERT INTO Vastaanottaja (viestiID, kayttajaID) VALUES
(1, 6),
(2, 5),
(3, 5);
