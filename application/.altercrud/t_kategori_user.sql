SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `t_kategori_user` (
  `id` mediumint(9) NOT NULL,
  `nama` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `t_kategori_user` (`id`, `nama`) VALUES
(1, 'Admin');


ALTER TABLE `t_kategori_user`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `t_kategori_user`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;