
-- --------------------------------------------------------
ALTER TABLE `soraldo_ele_sede` ADD `id_ubicazione` INT(10) NULL DEFAULT NULL AFTER `longitudine`, ADD `ospedaliera` INT(2) NULL DEFAULT NULL AFTER `id_ubicazione`; --
-- Struttura della tabella `soraldo_access`
--

CREATE TABLE if not exists`soraldo_ubicazione` (
  `id` int(10) NOT NULL,
  `ubicazione` varchar(60) DEFAULT 'NULL'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `soraldo_access`
--

INSERT INTO `soraldo_ubicazione` (`id`, `ubicazione`) VALUES
(1, 'EDIFICI SCOLASTICI'),
(2, 'UFFICI COMUNALI'),
(3, 'OSPEDALI E CASE DI CURA CON ALMENO 200 POSTI-LETTO');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `soraldo_access`
--
ALTER TABLE `soraldo_ubicazione`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `soraldo_access`
--
ALTER TABLE `soraldo_ubicazione`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
