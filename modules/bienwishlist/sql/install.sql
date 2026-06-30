CREATE TABLE IF NOT EXISTS `PREFIX_real_estate_wishlist` (
  `id_wishlist` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_customer` INT UNSIGNED NOT NULL,
  `id_property` INT UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_wishlist`),
  UNIQUE KEY `unique_customer_property` (`id_customer`, `id_property`),
  KEY `idx_customer` (`id_customer`),
  KEY `idx_property` (`id_property`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
