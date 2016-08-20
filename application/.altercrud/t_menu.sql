SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `t_menu` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `urutan` int(3) NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL,
  `id_tkategori_user` mediumint(9) NOT NULL,
  `tabel` varchar(255) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `t_menu` (`id`, `nama`, `url`, `icon`, `parent_id`, `urutan`, `status`, `id_tkategori_user`, `tabel`) VALUES
(1, 'root', '', 'fa fa-users', 1, 99, 'Aktif', 1, 'default.png'),
(13, 'Dashboard', 'admin/dashboard', 'fa fa-tachometer', 1, 0, 'Aktif', 1, 'default.png'),
(14, 'Setting Dev', '', 'fa fa-gear', 1, 99, 'Aktif', 1, 'default.png'),
(15, 'Menu', 'admin/tmenu', '', 14, 1, 'Aktif', 1, 'default.png'),
(16, 'Kategori User', 'admin/tkategori', '', 14, 2, 'Aktif', 1, 'default.png'),
(25, 'Option', 'admin/toption', '', 14, 3, 'Aktif', 1, 'deffault.png'),
(26, 'View Dashboard', 'admin/tdashboard', '', 14, 4, 'Aktif', 1, 'deffault.png'),
(27, 'Admin', 'admin/tadmin', '', 14, 5, 'Aktif', 1, 'deffault.png'),
(30, 'Export All Database', 'admin/texport', '', 14, 30, 'Aktif', 1, '');


ALTER TABLE `t_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tkategori_user` (`id_tkategori_user`);


ALTER TABLE `t_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

ALTER TABLE `t_menu`
  ADD CONSTRAINT `t_menu_ibfk_1` FOREIGN KEY (`id_tkategori_user`) REFERENCES `t_kategori_user` (`id`);
