-- Create new tables for the improved structure
CREATE TABLE IF NOT EXISTS `survey_improvement_categories` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `header_id` bigint(20) UNSIGNED NOT NULL,
    `category_name` varchar(255) NOT NULL,
    `is_other` tinyint(1) NOT NULL DEFAULT 0,
    `other_comments` text NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `survey_improvement_categories_header_id_foreign` (`header_id`),
    CONSTRAINT `survey_improvement_categories_header_id_foreign` FOREIGN KEY (`header_id`) REFERENCES `survey_response_headers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `survey_improvement_details` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` bigint(20) UNSIGNED NOT NULL,
    `detail_text` text NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `survey_improvement_details_category_id_foreign` (`category_id`),
    CONSTRAINT `survey_improvement_details_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `survey_improvement_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Recreate the old table structure to maintain compatibility
CREATE TABLE IF NOT EXISTS `survey_improvement_areas` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `header_id` bigint(20) UNSIGNED NOT NULL,
    `area_category` varchar(255) NOT NULL,
    `area_details` text NULL,
    `is_other` tinyint(1) NOT NULL DEFAULT 0,
    `other_comments` text NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `survey_improvement_areas_header_id_foreign` (`header_id`),
    CONSTRAINT `survey_improvement_areas_header_id_foreign` FOREIGN KEY (`header_id`) REFERENCES `survey_response_headers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
