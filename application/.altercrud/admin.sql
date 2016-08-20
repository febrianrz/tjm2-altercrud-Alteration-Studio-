SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `theme` varchar(30) NOT NULL DEFAULT 'sbadmin1',
  `id_kategori_user` mediumint(9) NOT NULL DEFAULT '1',
  `gambar` varchar(255) NOT NULL DEFAULT 'user.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `admin` (`id`, `username`, `nama`, `password`, `theme`, `id_kategori_user`, `gambar`) VALUES
(1, 'admin', 'admin', 'admin', 'sbadmin2', 1, 'user.png');


ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori_user` (`id_kategori_user`);


ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`id_kategori_user`) REFERENCES `t_kategori_user` (`id`);