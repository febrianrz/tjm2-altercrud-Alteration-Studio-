SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `t_dashboard` (
  `id` int(11) NOT NULL,
  `id_kategori_user` mediumint(9) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `icon` varchar(30) NOT NULL,
  `panel` enum('panel-primary','panel-danger','panel-warning','panel-default','panel-info') NOT NULL DEFAULT 'panel-primary',
  `panel_width` enum('1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '3',
  `url` varchar(255) NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `t_dashboard` (`id`, `id_kategori_user`, `nama`, `icon`, `panel`, `panel_width`, `url`, `status`, `query`) VALUES
(1, 1, 'Total Admin', 'fa fa-users', 'panel-primary', '3', 'admin/tadmin', 'Aktif', '<p></p>'),
(2, 1, 'Total Menu', 'fa fa-gear', 'panel-danger', '3', 'admin/tmenu', 'Aktif', '<p></p>');


ALTER TABLE `t_dashboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori_user` (`id_kategori_user`);


ALTER TABLE `t_dashboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `t_dashboard`
  ADD CONSTRAINT `t_dashboard_ibfk_1` FOREIGN KEY (`id_kategori_user`) REFERENCES `t_kategori_user` (`id`);