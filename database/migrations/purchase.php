<?php

return ["

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `receipt_code` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `expire_date` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_device_id` (`device_id`);

ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `purchases`
  ADD CONSTRAINT `id_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
"];
