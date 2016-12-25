SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `walletjournal` (
  `id` int(11) NOT NULL,
  `dateJ` datetime DEFAULT NULL,
  `weekno` int(2) NOT NULL,
  `refID` bigint(11) DEFAULT NULL,
  `refTypeID` int(11) DEFAULT NULL,
  `ownerName1` varchar(50) DEFAULT NULL,
  `ownerID1` int(11) DEFAULT NULL,
  `ownerName2` varchar(50) DEFAULT NULL,
  `ownerID2` int(11) DEFAULT NULL,
  `argName1` varchar(50) DEFAULT NULL,
  `argID1` int(11) DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `balance` decimal(16,2) DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `owner1TypeID` int(11) DEFAULT NULL,
  `owner2TypeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `walletjournal`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `walletjournal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `daily` (
  `id` int(11) NOT NULL,
  `dateD` date NOT NULL,
  `refTypeID` int(4) NOT NULL,
  `amount` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `daily`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `daily`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;