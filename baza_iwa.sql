CREATE USER 'iwa_2019'@'localhost' IDENTIFIED BY 'foi2019';
CREATE DATABASE iwa_2019_kz_projekt;
GRANT ALL PRIVILEGES ON 'iwa_2019_kz_projekt'.* TO 'iwa_2019'@'localhost';
FLUSH PRIVILEGES;
USE iwa_2019_kz_projekt;
CREATE TABLE tip_korisnika
(
tip_id INT(10) NOT NULL AUTO_INCREMENT,
naziv VARCHAR(50),
PRIMARY KEY (tip_id)
);
CREATE TABLE korisnik
(
korisnik_id INT(10) NOT NULL AUTO_INCREMENT,
tip_id INT(10),
korisnicko_ime VARCHAR(50),
lozinka VARCHAR(50),
ime VARCHAR(50),
prezime VARCHAR(50),
email VARCHAR(50),
slika TEXT,
PRIMARY KEY (korisnik_id),
FOREIGN KEY (tip_id) REFERENCES tip_korisnika(tip_id)
);
CREATE TABLE kategorija
(
kategorija_id INT(10) NOT NULL AUTO_INCREMENT,
moderator_id INT(10),
naziv VARCHAR(50),
opis TEXT,
`limit` INT(10),
PRIMARY KEY (kategorija_id),
FOREIGN KEY (moderator_id) REFERENCES korisnik(korisnik_id)
);
CREATE TABLE proizvod
(
proizvod_id INT(10) NOT NULL AUTO_INCREMENT,
kategorija_id INT(10),
naziv VARCHAR(50),
opis TEXT,
cijena DOUBLE,
slika TEXT,
video TEXT,
PRIMARY KEY (proizvod_id),
FOREIGN KEY (kategorija_id) REFERENCES kategorija(kategorija_id)
);
CREATE TABLE narudzba
(
narudzba_id INT(10) NOT NULL AUTO_INCREMENT,
korisnik_id INT(10),
proizvod_id INT(10),
kolicina INT(10),
blokirana BOOLEAN,
prihvacena BOOLEAN,
datum_kreiranja DATETIME,
PRIMARY KEY (narudzba_id),
FOREIGN KEY (korisnik_id) REFERENCES korisnik(korisnik_id),
FOREIGN KEY (proizvod_id) REFERENCES proizvod(proizvod_id)
);
INSERT INTO `tip_korisnika` (`tip_id`,`naziv`) VALUES (1,'Administrator');
INSERT INTO `tip_korisnika` (`tip_id`,`naziv`) VALUES (2,'Moderator');
INSERT INTO `tip_korisnika` (`tip_id`,`naziv`) VALUES (3,'Registrirani korisnik');
INSERT INTO `korisnik` (`korisnik_id`,`tip_id`,`korisnicko_ime`,`lozinka`,`ime`,`prezime`,`email`,`slika`) VALUES (1,1,'mpopovic','admin','Martina','Popovic','mpopovic@foi.hr','https://studentski.hr/system/pictures/images/m/original/c087d97efe0958d6ee340c769cef12c3104edba8.jpg?1471691114');
INSERT INTO `korisnik` (`korisnik_id`,`tip_id`,`korisnicko_ime`,`lozinka`,`ime`,`prezime`,`email`,`slika`) VALUES (2,2,'mod','mod','Mod','Moderator','moderator@gmail.com','https://studentski.hr/system/pictures/images/m/original/c087d97efe0958d6ee340c769cef12c3104edba8.jpg?1471691114');
INSERT INTO `korisnik` (`korisnik_id`,`tip_id`,`korisnicko_ime`,`lozinka`,`ime`,`prezime`,`email`,`slika`) VALUES (3,3,'user','user','user','user','user@gmail.com','https://studentski.hr/system/pictures/images/m/original/c087d97efe0958d6ee340c769cef12c3104edba8.jpg?1471691114');
INSERT INTO `kategorija` (`kategorija_id`,`moderator_id`,`naziv`,`opis`,`limit`) VALUES (1,2,'Umivaonici','Širok izbor umivaonika....',200);
INSERT INTO `kategorija` (`kategorija_id`,`moderator_id`,`naziv`,`opis`,`limit`) VALUES (2,2,'WC školjke','Širok izbor WC školjki....',300);
INSERT INTO `kategorija` (`kategorija_id`,`moderator_id`,`naziv`,`opis`,`limit`) VALUES (3,2,'Perilice rublja','Širok izbor perilica rublja....',800);
INSERT INTO `kategorija` (`kategorija_id`,`moderator_id`,`naziv`,`opis`,`limit`) VALUES (4,2,'Elektronika','Širok izbor elektronike....',50);
INSERT INTO `kategorija` (`kategorija_id`,`moderator_id`,`naziv`,`opis`,`limit`) VALUES (5,2,'Ogledala','Širok izbor ogledala....',25);
INSERT INTO `proizvod` (`proizvod_id`,`kategorija_id`,`naziv`,`opis`,`cijena`,`slika`,`video`) VALUES (1,1,'Umivaonik 1','Umivaonik 1 opis',100,'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSpODBmDFQFN2WFA1KNt-cMKKbPX5oeWridfEDotXC21s77tkwR','https://www.youtube.com/embed/RPoCsWIyZWg');
INSERT INTO `proizvod` (`proizvod_id`,`kategorija_id`,`naziv`,`opis`,`cijena`,`slika`,`video`) VALUES (2,2,'WC Školjka 1','WC Školjka 1 opis',150,'https://s3.eu-central-1.amazonaws.com/cnj-img/images/Sd/SdPzKUYawhCJ','https://www.youtube.com/embed/nUmQJw-TSv8');
INSERT INTO `proizvod` (`proizvod_id`,`kategorija_id`,`naziv`,`opis`,`cijena`,`slika`,`video`) VALUES (3,2,'WC Školjka 2','WC Školjka 2 opis',250.29,'https://www.keramika.com/images/sanindusa/proget_f_d_close_coupled_wc_w_fixing_kit.jpg','https://www.youtube.com/embed/zYE17Wlep80');
INSERT INTO `proizvod` (`proizvod_id`,`kategorija_id`,`naziv`,`opis`,`cijena`,`slika`,`video`) VALUES (4,1,'Umivaonik 2','Umivaonik 2 opis',862.25,'https://www.bauhaus.hr/media/catalog/product/cache/1/image/800x/9df78eab33525d08d6e5fb8d27136e95/2/0/20074447.jpg','https://www.youtube.com/embed/JAN-I25bN4g');
INSERT INTO `proizvod` (`proizvod_id`,`kategorija_id`,`naziv`,`opis`,`cijena`,`slika`,`video`) VALUES (5,3,'Perilica1','Perilica1 opis',4999.99,'https://www.links.hr/content/images/thumbs/006/0064344_perilica-rublja-electrolux-ew6s226cx-1200okretaja-min-6kg-crna-a-750401192.jpg','https://www.youtube.com/embed/z-0_gHSZRak');
INSERT INTO `narudzba` (`narudzba_id`,`korisnik_id`,`proizvod_id`,`kolicina`,`blokirana`,`prihvacena`,`datum_kreiranja`) VALUES (6,2,3,1,1,0,'2020-03-20 18:57:31');
INSERT INTO `narudzba` (`narudzba_id`,`korisnik_id`,`proizvod_id`,`kolicina`,`blokirana`,`prihvacena`,`datum_kreiranja`) VALUES (7,2,3,1,1,0,'2020-03-20 18:57:32');
INSERT INTO `narudzba` (`narudzba_id`,`korisnik_id`,`proizvod_id`,`kolicina`,`blokirana`,`prihvacena`,`datum_kreiranja`) VALUES (8,2,3,1,1,0,'2020-03-20 18:57:33');
INSERT INTO `narudzba` (`narudzba_id`,`korisnik_id`,`proizvod_id`,`kolicina`,`blokirana`,`prihvacena`,`datum_kreiranja`) VALUES (9,2,4,1,1,0,'2020-03-20 18:57:34');
INSERT INTO `narudzba` (`narudzba_id`,`korisnik_id`,`proizvod_id`,`kolicina`,`blokirana`,`prihvacena`,`datum_kreiranja`) VALUES (10,2,2,1,1,0,'2020-03-20 18:57:35');
INSERT INTO `narudzba` (`narudzba_id`,`korisnik_id`,`proizvod_id`,`kolicina`,`blokirana`,`prihvacena`,`datum_kreiranja`) VALUES (11,2,2,1,1,1,'2020-03-20 18:57:38');
INSERT INTO `narudzba` (`narudzba_id`,`korisnik_id`,`proizvod_id`,`kolicina`,`blokirana`,`prihvacena`,`datum_kreiranja`) VALUES (12,1,3,1,1,0,'2020-03-21 19:04:22');