SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `t_options` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `t_options` (`id`, `nama`, `keterangan`) VALUES
(1, 'Judul Admin', 'Alteration Studios');


ALTER TABLE `t_options`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `t_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;