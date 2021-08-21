<?php
return ["
CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `u_id` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `os` varchar(255) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;
"];