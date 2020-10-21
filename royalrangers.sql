-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Čtv 27. srp 2020, 23:57
-- Verze serveru: 10.1.36-MariaDB
-- Verze PHP: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `royalrangers`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `akce`
--

CREATE TABLE `akce` (
  `id_akce` int(11) NOT NULL,
  `zacatek` datetime NOT NULL,
  `konec` datetime NOT NULL,
  `nazev` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci,
  `vedouci_akce` int(11) DEFAULT NULL,
  `pridal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `akce`
--

INSERT INTO `akce` (`id_akce`, `zacatek`, `konec`, `nazev`, `popis`, `vedouci_akce`, `pridal`) VALUES
(4, '2020-02-29 00:00:00', '2020-03-02 00:00:00', 'Chata', 'Košařiska', 3, 1),
(6, '2020-03-21 00:00:00', '2020-03-23 00:00:00', 'Nová akce', 'popis nové akce', 2, 1),
(7, '2020-03-29 00:00:00', '2020-03-31 00:00:00', 'Víkendovka', 'Víkendová akce', 3, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `historie_bodu`
--

CREATE TABLE `historie_bodu` (
  `id_bodu` int(11) NOT NULL,
  `id_rangera` int(11) DEFAULT NULL,
  `datum` date NOT NULL,
  `pocet_bodu` int(11) NOT NULL,
  `duvod` text COLLATE utf8_czech_ci,
  `pridal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `historie_bodu`
--

INSERT INTO `historie_bodu` (`id_bodu`, `id_rangera`, `datum`, `pocet_bodu`, `duvod`, `pridal`) VALUES
(2, 3, '2020-02-29', 1, 'test', 1),
(3, 3, '2020-02-29', -4, 'test mazání', 1),
(4, 4, '2020-03-01', 150, 'Test', 1),
(5, 5, '2020-03-01', 1, 'test', 1),
(6, 5, '2020-03-02', -5, 'nula', 1),
(7, 5, '2020-03-02', -6, 'znova nula', 1),
(8, 4, '2020-03-02', -60, 'test', 1),
(9, 4, '2020-03-03', -106, 'test', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `kmen`
--

CREATE TABLE `kmen` (
  `id_kmenu` int(11) NOT NULL,
  `nazev` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `min_vek` int(2) NOT NULL,
  `max_vek` int(2) NOT NULL,
  `popis` text COLLATE utf8_czech_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `kmen`
--

INSERT INTO `kmen` (`id_kmenu`, `nazev`, `min_vek`, `max_vek`, `popis`) VALUES
(1, 'Adventure Rangers', 12, 18, 'Nejstarší věková kategorie'),
(2, 'Discovery Rangers', 8, 12, 'Prostřední věková kategorie'),
(3, 'Ranger Kids', 3, 8, 'Nejmladší věková kategorie');

-- --------------------------------------------------------

--
-- Struktura tabulky `odborka`
--

CREATE TABLE `odborka` (
  `id_odborky` int(11) NOT NULL,
  `nazev` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci,
  `id_kmenu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `odeslana_posta`
--

CREATE TABLE `odeslana_posta` (
  `id_posty` int(11) NOT NULL,
  `prijemce` text COLLATE utf8_czech_ci NOT NULL,
  `predmet` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `hlavicka` text COLLATE utf8_czech_ci,
  `zprava` text COLLATE utf8_czech_ci NOT NULL,
  `datum` datetime NOT NULL,
  `odeslal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `program`
--

CREATE TABLE `program` (
  `id_programu` int(11) NOT NULL,
  `id_schuzky` int(11) DEFAULT NULL,
  `hry` int(11) DEFAULT NULL,
  `odborka` int(11) DEFAULT NULL,
  `vedeni_odborky` int(11) DEFAULT NULL,
  `vedeni_druhe_odborky` int(11) DEFAULT NULL,
  `id_kmenu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `ranger`
--

CREATE TABLE `ranger` (
  `id_rangera` int(11) NOT NULL,
  `jmeno` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `prezdivka` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `prijmeni` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `datum_narozeni` date NOT NULL,
  `kontaktni_email` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `kontaktni_tel` varchar(17) COLLATE utf8_czech_ci NOT NULL,
  `pocet_bodu` int(11) NOT NULL,
  `id_kmenu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `ranger`
--

INSERT INTO `ranger` (`id_rangera`, `jmeno`, `prezdivka`, `prijmeni`, `datum_narozeni`, `kontaktni_email`, `kontaktni_tel`, `pocet_bodu`, `id_kmenu`) VALUES
(1, 'Jiří', 'Georgo', 'Horák', '2006-02-21', 'jirka.h@gmail.com', '+420 123 456 789', 65, 1),
(2, 'Antonín', 'Tonda', 'Pajdla', '2007-04-02', 'tonda.p@gmail.com', '+420 123 456 789', 17, 2),
(3, 'David', 'Davídek', 'Zapletal', '2012-10-28', 'david.z@gmail.com', '+420 123 456 789', 22, 3),
(4, 'Mikuláš', 'Miki', 'Černý', '2020-02-21', 'miki.cerny@emai.cz', '789 456 123', 22, 1),
(5, 'Karolína', 'Kája', 'Floryková', '2020-02-08', 'k.florykova@email.cz', '123 789 456', 29, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `schuzka`
--

CREATE TABLE `schuzka` (
  `id_schuzky` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  `popis` text COLLATE utf8_czech_ci,
  `pridal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `schuzka`
--

INSERT INTO `schuzka` (`id_schuzky`, `datum`, `popis`, `pridal`) VALUES
(2, '2020-04-15 00:00:00', 'Běžná schůzka', 1),
(12, '2020-04-22 00:00:00', 'Běžná schůzka', 1),
(13, '2020-04-29 00:00:00', 'Běžná schůzka', 1),
(14, '2020-05-06 00:00:00', 'Běžná schůzka', 1),
(15, '2020-05-13 00:00:00', 'Běžná schůzka', 1),
(16, '2020-05-20 00:00:00', 'Běžná schůzka', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `tabulka`
--

CREATE TABLE `tabulka` (
  `id_tabulky` int(11) NOT NULL,
  `nazev` text COLLATE utf8_czech_ci NOT NULL,
  `sloupec` int(11) NOT NULL,
  `cizi_klic` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `tabulka2`
--

CREATE TABLE `tabulka2` (
  `id_tabulky2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `ucast_akce`
--

CREATE TABLE `ucast_akce` (
  `id_akce` int(11) NOT NULL,
  `id_rangera` int(11) NOT NULL,
  `pocet_bodu` int(11) NOT NULL,
  `stav` varchar(200) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `ucast_schuzka`
--

CREATE TABLE `ucast_schuzka` (
  `id_schuzky` int(11) NOT NULL,
  `id_rangera` int(11) NOT NULL,
  `pocet_bodu` int(11) NOT NULL,
  `stav` varchar(200) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `ucast_schuzka`
--

INSERT INTO `ucast_schuzka` (`id_schuzky`, `id_rangera`, `pocet_bodu`, `stav`) VALUES
(2, 1, 3, 'pritomen-pozde'),
(2, 4, 4, 'pritomen'),
(2, 5, 3, 'pritomen-pozde'),
(12, 1, 3, 'pritomen-pozde'),
(12, 4, 2, 'omluven'),
(12, 5, 3, 'pritomen-pozde'),
(13, 1, 4, 'pritomen'),
(13, 4, 4, 'pritomen'),
(13, 5, 2, 'omluven'),
(14, 1, 4, 'pritomen'),
(14, 4, 4, 'pritomen'),
(14, 5, 4, 'pritomen'),
(15, 1, 3, 'pritomen-pozde'),
(15, 4, 3, 'pritomen-pozde'),
(15, 5, 4, 'pritomen'),
(16, 1, 4, 'pritomen'),
(16, 4, 4, 'pritomen'),
(16, 5, 3, 'pritomen-pozde');

-- --------------------------------------------------------

--
-- Struktura tabulky `vedouci`
--

CREATE TABLE `vedouci` (
  `id_vedouciho` int(11) NOT NULL,
  `jmeno` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `prezdivka` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `prijmeni` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `datum_narozeni` date NOT NULL,
  `email` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `tel` varchar(17) COLLATE utf8_czech_ci NOT NULL,
  `login` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `heslo` text COLLATE utf8_czech_ci NOT NULL,
  `posledni_prihlaseni` datetime NOT NULL,
  `uroven` int(1) NOT NULL,
  `id_kmenu` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `vedouci`
--

INSERT INTO `vedouci` (`id_vedouciho`, `jmeno`, `prezdivka`, `prijmeni`, `datum_narozeni`, `email`, `tel`, `login`, `heslo`, `posledni_prihlaseni`, `uroven`, `id_kmenu`) VALUES
(1, 'Matěj', 'Matýsek', 'Eliáš', '2000-11-19', 'elias.matysek@gmail.com', '+420 731 144 669', 'matejelias', '$2y$10$jTV.643UcXiMXfOfcy76zOZuIerSHen92det4dQiI.w1qkjU1ZU6O', '2020-08-26 10:19:24', 9, 1),
(2, 'Matěj', '', 'Vrubel', '2000-09-30', 'vrubel.m@email.cz', '+420 731 144 669', 'mates', '$2y$10$UeAWtyY9H.rCKk.3WC8u1e5Gi.iTYzxZBnSp9cMEv2iqZTHWRUKXO', '2020-03-17 14:10:16', 4, 2),
(3, 'Honza', '', 'Bužga', '2002-07-24', 'Honza@gmail.com', '+420 731 144 669', 'honza', '$2y$10$jTV.643UcXiMXfOfcy76zOZuIerSHen92det4dQiI.w1qkjU1ZU6O', '2020-03-02 12:05:11', 4, 3);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `akce`
--
ALTER TABLE `akce`
  ADD PRIMARY KEY (`id_akce`),
  ADD KEY `akce_vedouci_vedouci_akce` (`vedouci_akce`),
  ADD KEY `akce_vedouci_pridal` (`pridal`);

--
-- Klíče pro tabulku `historie_bodu`
--
ALTER TABLE `historie_bodu`
  ADD PRIMARY KEY (`id_bodu`),
  ADD KEY `historie_bodu_ranger` (`id_rangera`),
  ADD KEY `historie_bodu_vedouci_pridal` (`pridal`);

--
-- Klíče pro tabulku `kmen`
--
ALTER TABLE `kmen`
  ADD PRIMARY KEY (`id_kmenu`);

--
-- Klíče pro tabulku `odborka`
--
ALTER TABLE `odborka`
  ADD PRIMARY KEY (`id_odborky`),
  ADD KEY `odborka_kmen` (`id_kmenu`);

--
-- Klíče pro tabulku `odeslana_posta`
--
ALTER TABLE `odeslana_posta`
  ADD PRIMARY KEY (`id_posty`),
  ADD KEY `odeslana_posta_vedouci_odeslal` (`odeslal`);

--
-- Klíče pro tabulku `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id_programu`),
  ADD KEY `program_kmen` (`id_kmenu`),
  ADD KEY `program_vedouci_hry` (`hry`),
  ADD KEY `program_vedouci_vedeni_odborky` (`vedeni_odborky`),
  ADD KEY `program_vedouci_vedeni_druhe_odborky` (`vedeni_druhe_odborky`),
  ADD KEY `program_odborka` (`odborka`),
  ADD KEY `program_schuzka` (`id_schuzky`);

--
-- Klíče pro tabulku `ranger`
--
ALTER TABLE `ranger`
  ADD PRIMARY KEY (`id_rangera`),
  ADD KEY `ranger_kmen` (`id_kmenu`);

--
-- Klíče pro tabulku `schuzka`
--
ALTER TABLE `schuzka`
  ADD PRIMARY KEY (`id_schuzky`),
  ADD KEY `schuzka_vedouci` (`pridal`);

--
-- Klíče pro tabulku `tabulka`
--
ALTER TABLE `tabulka`
  ADD PRIMARY KEY (`id_tabulky`);

--
-- Klíče pro tabulku `tabulka2`
--
ALTER TABLE `tabulka2`
  ADD PRIMARY KEY (`id_tabulky2`);

--
-- Klíče pro tabulku `ucast_akce`
--
ALTER TABLE `ucast_akce`
  ADD PRIMARY KEY (`id_akce`,`id_rangera`),
  ADD KEY `ucast_akce_ranger` (`id_rangera`);

--
-- Klíče pro tabulku `ucast_schuzka`
--
ALTER TABLE `ucast_schuzka`
  ADD PRIMARY KEY (`id_schuzky`,`id_rangera`),
  ADD KEY `ucast_schuzka_ranger` (`id_rangera`);

--
-- Klíče pro tabulku `vedouci`
--
ALTER TABLE `vedouci`
  ADD PRIMARY KEY (`id_vedouciho`),
  ADD KEY `vedouci_kmen` (`id_kmenu`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `akce`
--
ALTER TABLE `akce`
  MODIFY `id_akce` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pro tabulku `historie_bodu`
--
ALTER TABLE `historie_bodu`
  MODIFY `id_bodu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pro tabulku `kmen`
--
ALTER TABLE `kmen`
  MODIFY `id_kmenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `odborka`
--
ALTER TABLE `odborka`
  MODIFY `id_odborky` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `odeslana_posta`
--
ALTER TABLE `odeslana_posta`
  MODIFY `id_posty` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `program`
--
ALTER TABLE `program`
  MODIFY `id_programu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `ranger`
--
ALTER TABLE `ranger`
  MODIFY `id_rangera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pro tabulku `schuzka`
--
ALTER TABLE `schuzka`
  MODIFY `id_schuzky` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pro tabulku `tabulka`
--
ALTER TABLE `tabulka`
  MODIFY `id_tabulky` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `tabulka2`
--
ALTER TABLE `tabulka2`
  MODIFY `id_tabulky2` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `vedouci`
--
ALTER TABLE `vedouci`
  MODIFY `id_vedouciho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `akce`
--
ALTER TABLE `akce`
  ADD CONSTRAINT `akce_vedouci_pridal` FOREIGN KEY (`pridal`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `akce_vedouci_vedouci_akce` FOREIGN KEY (`vedouci_akce`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Omezení pro tabulku `historie_bodu`
--
ALTER TABLE `historie_bodu`
  ADD CONSTRAINT `historie_bodu_ranger` FOREIGN KEY (`id_rangera`) REFERENCES `ranger` (`id_rangera`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `historie_bodu_vedouci_pridal` FOREIGN KEY (`pridal`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Omezení pro tabulku `odborka`
--
ALTER TABLE `odborka`
  ADD CONSTRAINT `odborka_kmen` FOREIGN KEY (`id_kmenu`) REFERENCES `kmen` (`id_kmenu`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Omezení pro tabulku `odeslana_posta`
--
ALTER TABLE `odeslana_posta`
  ADD CONSTRAINT `odeslana_posta_vedouci_odeslal` FOREIGN KEY (`odeslal`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Omezení pro tabulku `program`
--
ALTER TABLE `program`
  ADD CONSTRAINT `program_kmen` FOREIGN KEY (`id_kmenu`) REFERENCES `kmen` (`id_kmenu`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `program_odborka` FOREIGN KEY (`odborka`) REFERENCES `odborka` (`id_odborky`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `program_schuzka` FOREIGN KEY (`id_schuzky`) REFERENCES `schuzka` (`id_schuzky`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `program_vedouci_hry` FOREIGN KEY (`hry`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `program_vedouci_vedeni_druhe_odborky` FOREIGN KEY (`vedeni_druhe_odborky`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `program_vedouci_vedeni_odborky` FOREIGN KEY (`vedeni_odborky`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Omezení pro tabulku `ranger`
--
ALTER TABLE `ranger`
  ADD CONSTRAINT `ranger_kmen` FOREIGN KEY (`id_kmenu`) REFERENCES `kmen` (`id_kmenu`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Omezení pro tabulku `schuzka`
--
ALTER TABLE `schuzka`
  ADD CONSTRAINT `schuzka_vedouci` FOREIGN KEY (`pridal`) REFERENCES `vedouci` (`id_vedouciho`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Omezení pro tabulku `ucast_akce`
--
ALTER TABLE `ucast_akce`
  ADD CONSTRAINT `ucast_akce_akce` FOREIGN KEY (`id_akce`) REFERENCES `akce` (`id_akce`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ucast_akce_ranger` FOREIGN KEY (`id_rangera`) REFERENCES `ranger` (`id_rangera`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `ucast_schuzka`
--
ALTER TABLE `ucast_schuzka`
  ADD CONSTRAINT `ucast_schuzka_ranger` FOREIGN KEY (`id_rangera`) REFERENCES `ranger` (`id_rangera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ucast_schuzka_schuzka` FOREIGN KEY (`id_schuzky`) REFERENCES `schuzka` (`id_schuzky`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `vedouci`
--
ALTER TABLE `vedouci`
  ADD CONSTRAINT `vedouci_kmen` FOREIGN KEY (`id_kmenu`) REFERENCES `kmen` (`id_kmenu`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
