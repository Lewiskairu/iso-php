-- ============================================================
-- MySQL Migration – converted from PostgreSQL dump
-- Target DB : iso_compliance_hub
-- Generated : 2026-04-20
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Drop and recreate database
DROP DATABASE IF EXISTS `iso_compliance_hub`;
CREATE DATABASE `iso_compliance_hub`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `iso_compliance_hub`;

-- ============================================================
-- TABLE: about_us
-- ============================================================
CREATE TABLE `about_us` (
  `id`         INT            NOT NULL AUTO_INCREMENT,
  `tagline`    VARCHAR(255)   DEFAULT NULL,
  `vision`     TEXT           DEFAULT NULL,
  `mission`    TEXT           DEFAULT NULL,
  `services`   TEXT           DEFAULT NULL,
  `updated_at` DATETIME       DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: accounts
-- ============================================================
CREATE TABLE `accounts` (
  `id`                  VARCHAR(255) NOT NULL,
  `userId`              VARCHAR(255) NOT NULL,
  `type`                VARCHAR(255) NOT NULL,
  `provider`            VARCHAR(255) NOT NULL,
  `providerAccountId`   VARCHAR(255) NOT NULL,
  `refresh_token`       TEXT         DEFAULT NULL,
  `access_token`        TEXT         DEFAULT NULL,
  `expires_at`          INT          DEFAULT NULL,
  `token_type`          VARCHAR(255) DEFAULT NULL,
  `scope`               VARCHAR(255) DEFAULT NULL,
  `id_token`            TEXT         DEFAULT NULL,
  `session_state`       VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_provider_account` (`provider`, `providerAccountId`),
  KEY `idx_accounts_userid` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: answers
-- ============================================================
CREATE TABLE `answers` (
  `id`           VARCHAR(255)   NOT NULL,
  `assessmentId` VARCHAR(255)   NOT NULL,
  `questionId`   VARCHAR(255)   NOT NULL,
  `value`        VARCHAR(255)   NOT NULL,
  `textValue`    TEXT           DEFAULT NULL,
  `score`        DECIMAL(10,2)  DEFAULT NULL,
  `createdAt`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_assessment_question` (`assessmentId`, `questionId`),
  KEY `idx_answers_assessmentid` (`assessmentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: assessments
-- ============================================================
CREATE TABLE `assessments` (
  `id`              VARCHAR(255)  NOT NULL,
  `userId`          VARCHAR(255)  NOT NULL,
  `isoStandardId`   VARCHAR(255)  NOT NULL,
  `title`           VARCHAR(255)  DEFAULT NULL,
  `status`          VARCHAR(50)   NOT NULL DEFAULT 'IN_PROGRESS',
  `complianceScore` DECIMAL(5,2)  DEFAULT NULL,
  `maturityLevel`   VARCHAR(50)   DEFAULT NULL,
  `completedAt`     DATETIME      DEFAULT NULL,
  `createdAt`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_assessments_userid`       (`userId`),
  KEY `idx_assessments_isostandardid` (`isoStandardId`),
  KEY `idx_assessments_status`       (`status`),
  KEY `idx_assessments_created`      (`createdAt`),
  KEY `idx_assessments_completed`    (`completedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: categories
-- ============================================================
CREATE TABLE `categories` (
  `id`          VARCHAR(255) NOT NULL,
  `name`        VARCHAR(255) NOT NULL,
  `slug`        VARCHAR(255) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `imageUrl`    VARCHAR(500) DEFAULT NULL,
  `parentId`    VARCHAR(255) DEFAULT NULL,
  `order`       INT          NOT NULL DEFAULT 0,
  `active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `createdAt`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_key` (`slug`),
  KEY `idx_categories_parent`   (`parentId`),
  KEY `idx_categories_slug`     (`slug`),
  KEY `idx_categories_active`   (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: certification_requests
-- ============================================================
CREATE TABLE `certification_requests` (
  `id`             VARCHAR(255) NOT NULL,
  `companyName`    TEXT         NOT NULL,
  `contactName`    TEXT         NOT NULL,
  `contactEmail`   TEXT         NOT NULL,
  `contactPhone`   TEXT         DEFAULT NULL,
  `companySize`    TEXT         DEFAULT NULL,
  `currentStatus`  TEXT         DEFAULT NULL,
  `requirements`   TEXT         DEFAULT NULL,
  `status`         TEXT         DEFAULT 'NEW',
  `userId`         TEXT         DEFAULT NULL,
  `createdAt`      DATETIME(3)  NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `updatedAt`      DATETIME(3)  NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
  `documents`      JSON         DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: clauses
-- ============================================================
CREATE TABLE `clauses` (
  `id`            VARCHAR(255)  NOT NULL,
  `isoStandardId` VARCHAR(255)  NOT NULL,
  `number`        VARCHAR(50)   NOT NULL,
  `title`         VARCHAR(255)  NOT NULL,
  `description`   TEXT          DEFAULT NULL,
  `weight`        DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `order`         INT           NOT NULL,
  `createdAt`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_clauses_isostandard_order` (`isoStandardId`, `order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: iso_settings
-- ============================================================
CREATE TABLE `iso_settings` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `key`         VARCHAR(255) NOT NULL,
  `value`       TEXT         DEFAULT NULL,
  `standard_id` INT          DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: iso_standards
-- ============================================================
CREATE TABLE `iso_standards` (
  `id`          VARCHAR(255) NOT NULL,
  `code`        VARCHAR(50)  NOT NULL,
  `name`        VARCHAR(255) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `year`        INT          DEFAULT NULL,
  `active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `createdAt`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iso_standards_code_key` (`code`),
  KEY `idx_iso_standards_code`   (`code`),
  KEY `idx_iso_standards_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: leads
-- ============================================================
CREATE TABLE `leads` (
  `id`                  VARCHAR(255) NOT NULL,
  `userId`              VARCHAR(255) DEFAULT NULL,
  `isoStandardId`       VARCHAR(255) NOT NULL,
  `companyName`         VARCHAR(255) NOT NULL,
  `contactName`         VARCHAR(255) NOT NULL,
  `contactEmail`        VARCHAR(255) NOT NULL,
  `contactPhone`        VARCHAR(50)  DEFAULT NULL,
  `companySize`         VARCHAR(50)  DEFAULT NULL,
  `currentStatus`       TEXT         DEFAULT NULL,
  `requirements`        TEXT         DEFAULT NULL,
  `status`              VARCHAR(50)  NOT NULL DEFAULT 'NEW',
  `assignedPartnerId`   VARCHAR(255) DEFAULT NULL,
  `notes`               TEXT         DEFAULT NULL,
  `createdAt`           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastMessageAt`       DATETIME     DEFAULT NULL,
  `unreadMessagesCount` INT          NOT NULL DEFAULT 0,
  `companyLogo`         VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_leads_userid`           (`userId`),
  KEY `idx_leads_isostandardid`    (`isoStandardId`),
  KEY `idx_leads_status`           (`status`),
  KEY `idx_leads_assignedpartnerid`(`assignedPartnerId`),
  KEY `idx_leads_created`          (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: messages
-- ============================================================
CREATE TABLE `messages` (
  `id`         VARCHAR(255) NOT NULL,
  `leadId`     VARCHAR(255) NOT NULL,
  `senderId`   VARCHAR(255) NOT NULL,
  `senderRole` VARCHAR(50)  NOT NULL,
  `message`    TEXT         NOT NULL,
  `isInternal` TINYINT(1)   NOT NULL DEFAULT 0,
  `readAt`     DATETIME     DEFAULT NULL,
  `createdAt`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_leadid`    (`leadId`),
  KEY `idx_messages_senderid`  (`senderId`),
  KEY `idx_messages_createdat` (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: nominations
-- ============================================================
CREATE TABLE `nominations` (
  `id`             VARCHAR(255) NOT NULL,
  `nominatorName`  VARCHAR(255) NOT NULL,
  `nominatorEmail` VARCHAR(255) NOT NULL,
  `nomineeName`    VARCHAR(255) NOT NULL,
  `nomineeEmail`   VARCHAR(255) DEFAULT NULL,
  `nominationType` VARCHAR(50)  NOT NULL,
  `reason`         TEXT         NOT NULL,
  `status`         VARCHAR(50)  NOT NULL DEFAULT 'NEW',
  `createdAt`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nominations_email`  (`nominatorEmail`),
  KEY `idx_nominations_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: order_items
-- ============================================================
CREATE TABLE `order_items` (
  `id`        VARCHAR(255)  NOT NULL,
  `orderId`   VARCHAR(255)  NOT NULL,
  `productId` VARCHAR(255)  NOT NULL,
  `quantity`  INT           NOT NULL DEFAULT 1,
  `price`     DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_orderid` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: orders
-- ============================================================
CREATE TABLE `orders` (
  `id`              VARCHAR(255)  NOT NULL,
  `userId`          VARCHAR(255)  NOT NULL,
  `stripePaymentId` VARCHAR(255)  DEFAULT NULL,
  `total`           DECIMAL(10,2) NOT NULL,
  `currency`        VARCHAR(10)   NOT NULL DEFAULT 'USD',
  `status`          VARCHAR(50)   NOT NULL DEFAULT 'PENDING',
  `createdAt`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_stripepaymentid_key` (`stripePaymentId`),
  KEY `idx_orders_userid`   (`userId`),
  KEY `idx_orders_status`   (`status`),
  KEY `idx_orders_created`  (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: partners
-- ============================================================
CREATE TABLE `partners` (
  `id`         INT           NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)  NOT NULL,
  `url`        VARCHAR(512)  DEFAULT NULL,
  `logo_url`   VARCHAR(512)  NOT NULL,
  `created_at` DATETIME      DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: pending_orders
-- ============================================================
CREATE TABLE `pending_orders` (
  `id`                VARCHAR(255)  NOT NULL,
  `checkoutrequestid` VARCHAR(255)  NOT NULL,
  `merchantrequestid` VARCHAR(255)  NOT NULL,
  `userid`            VARCHAR(255)  NOT NULL,
  `orderitems`        JSON          NOT NULL,
  `total`             DECIMAL(10,2) NOT NULL,
  `currency`          VARCHAR(10)   NOT NULL DEFAULT 'KES',
  `phonenumber`       VARCHAR(20)   NOT NULL,
  `status`            VARCHAR(50)   NOT NULL DEFAULT 'PENDING',
  `createdat`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiresat`         DATETIME      NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pending_orders_checkoutrequestid_key` (`checkoutrequestid`),
  KEY `idx_pending_orders_checkout_request` (`checkoutrequestid`),
  KEY `idx_pending_orders_user`            (`userid`),
  KEY `idx_pending_orders_status`          (`status`),
  KEY `idx_pending_orders_expires`         (`expiresat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: product_category_recommendations
-- ============================================================
CREATE TABLE `product_category_recommendations` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `product_id`  VARCHAR(255) NOT NULL,
  `category_id` VARCHAR(255) NOT NULL,
  `sort_order`  INT          DEFAULT 0,
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_category_recommendations_product_id_category_id_key` (`product_id`, `category_id`),
  KEY `idx_product_category_recommendations_product`  (`product_id`),
  KEY `idx_product_category_recommendations_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: product_images
-- ============================================================
CREATE TABLE `product_images` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `product_id` VARCHAR(255) NOT NULL,
  `image_url`  TEXT         NOT NULL,
  `sort_order` INT          DEFAULT 0,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: product_recommendations
-- ============================================================
CREATE TABLE `product_recommendations` (
  `id`                     INT          NOT NULL AUTO_INCREMENT,
  `product_id`             VARCHAR(255) NOT NULL,
  `recommended_product_id` VARCHAR(255) NOT NULL,
  `sort_order`             INT          DEFAULT 0,
  `created_at`             DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_recommendations_product_id_recommended_product_id_key` (`product_id`, `recommended_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: products
-- ============================================================
CREATE TABLE `products` (
  `id`            VARCHAR(255)  NOT NULL,
  `name`          VARCHAR(255)  NOT NULL,
  `description`   TEXT          NOT NULL,
  `price`         DECIMAL(10,2) NOT NULL,
  `currency`      VARCHAR(10)   NOT NULL DEFAULT 'USD',
  `sku`           VARCHAR(100)  NOT NULL,
  `type`          VARCHAR(50)   NOT NULL,
  `fileUrl`       VARCHAR(500)  DEFAULT NULL,
  `imageurl`      VARCHAR(500)  DEFAULT NULL,
  `active`        TINYINT(1)    NOT NULL DEFAULT 1,
  `createdAt`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `categoryId`    VARCHAR(255)  DEFAULT NULL,
  `stock`         INT           DEFAULT 0,
  `maincategoryid`VARCHAR(64)   DEFAULT NULL,
  `subcategoryid` VARCHAR(64)   DEFAULT NULL,
  `previousprice` DECIMAL(10,2) DEFAULT NULL,
  `specialprice`  DECIMAL(10,2) DEFAULT NULL,
  `specialevent`  VARCHAR(255)  DEFAULT NULL,
  `specialactive` TINYINT(1)    DEFAULT 0,
  `specialstart`  DATETIME      DEFAULT NULL,
  `specialend`    DATETIME      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_key` (`sku`),
  KEY `idx_products_active`   (`active`),
  KEY `idx_products_category` (`categoryId`),
  KEY `idx_products_sku`      (`sku`),
  KEY `idx_products_created`  (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: questions
-- ============================================================
CREATE TABLE `questions` (
  `id`          VARCHAR(255)  NOT NULL,
  `clauseId`    VARCHAR(255)  NOT NULL,
  `text`        TEXT          NOT NULL,
  `description` TEXT          DEFAULT NULL,
  `type`        ENUM('YES_NO','SCALE','MULTIPLE_CHOICE','TEXT') NOT NULL,
  `options`     JSON          DEFAULT NULL,
  `weight`      DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `order`       INT           NOT NULL,
  `required`    TINYINT(1)    NOT NULL DEFAULT 1,
  `createdAt`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_questions_clause_order` (`clauseId`, `order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: sessions
-- ============================================================
CREATE TABLE `sessions` (
  `id`           VARCHAR(255) NOT NULL,
  `sessionToken` VARCHAR(255) NOT NULL,
  `userId`       VARCHAR(255) NOT NULL,
  `expires`      DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sessions_sessiontoken_key` (`sessionToken`),
  KEY `idx_sessions_sessiontoken` (`sessionToken`),
  KEY `idx_sessions_userid`       (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: site_settings
-- ============================================================
CREATE TABLE `site_settings` (
  `id`                VARCHAR(255) NOT NULL,
  `key`               VARCHAR(255) NOT NULL,
  `value`             TEXT         DEFAULT NULL,
  `createdAt`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category`          VARCHAR(100) DEFAULT NULL,
  `type`              VARCHAR(50)  DEFAULT 'string',
  `label`             VARCHAR(255) DEFAULT NULL,
  `description`       TEXT         DEFAULT NULL,
  `ispublic`          TINYINT(1)   DEFAULT 0,
  `requiresrestart`   TINYINT(1)   DEFAULT 0,
  `currency`          VARCHAR(8)   DEFAULT 'USD',
  `currencysymbol`    VARCHAR(8)   DEFAULT '$',
  `inventoryenabled`  TINYINT(1)   DEFAULT 1,
  `lowstockthreshold` INT          DEFAULT 5,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_key` (`key`),
  KEY `idx_site_settings_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: standards
-- ============================================================
CREATE TABLE `standards` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `version`     VARCHAR(32)  NOT NULL,
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: terms_and_conditions
-- ============================================================
CREATE TABLE `terms_and_conditions` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255) NOT NULL,
  `content`    TEXT         NOT NULL,
  `version`    VARCHAR(32)  NOT NULL,
  `is_active`  TINYINT(1)   DEFAULT 1,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: user_answers
-- ============================================================
CREATE TABLE `user_answers` (
  `id`              INT          NOT NULL AUTO_INCREMENT,
  `assessment_id`   VARCHAR(255) DEFAULT NULL,
  `question_id`     VARCHAR(255) DEFAULT NULL,
  `answer`          TEXT         DEFAULT NULL,
  `evidence_url`    TEXT         DEFAULT NULL,
  `evidence_status` VARCHAR(32)  DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: user_assessments
-- ============================================================
CREATE TABLE `user_assessments` (
  `id`               VARCHAR(255) NOT NULL,
  `user_id`          VARCHAR(36)  NOT NULL,
  `standard_id`      INT          DEFAULT NULL,
  `standard_version` VARCHAR(32)  NOT NULL,
  `started_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `completed_at`     DATETIME     DEFAULT NULL,
  `status`           VARCHAR(32)  DEFAULT 'draft',
  `score`            DECIMAL(10,2)DEFAULT NULL,
  `maturity_level`   VARCHAR(32)  DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: user_terms_acceptances
-- ============================================================
CREATE TABLE `user_terms_acceptances` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `user_id`    VARCHAR(36)  NOT NULL,
  `terms_id`   INT          NOT NULL,
  `accepted_at`DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `ip_address` VARCHAR(64)  DEFAULT NULL,
  `user_agent` TEXT         DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE `users` (
  `id`            VARCHAR(255) NOT NULL,
  `email`         VARCHAR(255) NOT NULL,
  `emailVerified` DATETIME     DEFAULT NULL,
  `password`      VARCHAR(255) DEFAULT NULL,
  `name`          VARCHAR(255) DEFAULT NULL,
  `image`         VARCHAR(500) DEFAULT NULL,
  `role`          ENUM('USER','ADMIN','PARTNER') NOT NULL DEFAULT 'USER',
  `createdAt`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_key` (`email`),
  KEY `idx_users_email`   (`email`),
  KEY `idx_users_role`    (`role`),
  KEY `idx_users_created` (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: verification_tokens
-- ============================================================
CREATE TABLE `verification_tokens` (
  `identifier` VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `expires`    DATETIME     NOT NULL,
  UNIQUE KEY `unique_identifier_token`      (`identifier`, `token`),
  UNIQUE KEY `verification_tokens_token_key`(`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA: about_us
-- ============================================================
INSERT INTO `about_us` (`id`, `tagline`, `vision`, `mission`, `services`, `updated_at`) VALUES
(1, '', 'lewis', '', '[""]', '2025-12-30 15:58:15');

-- ============================================================
-- DATA: partners
-- ============================================================
INSERT INTO `partners` (`id`, `name`, `url`, `logo_url`, `created_at`) VALUES
(2, 'iworth', NULL, '/uploads/partners/1767113189332-WhatsApp_Image_2025-11-13_at_4.31.03_AM.jpeg', '2025-12-30 11:46:29'),
(3, 'aposto logistics', NULL, '/uploads/partners/1767113634501-WhatsApp_Image_2025-12-30_at_11.39.28_AM.jpeg', '2025-12-30 11:53:54');

-- ============================================================
-- DATA: iso_standards
-- ============================================================
INSERT INTO `iso_standards` (`id`, `code`, `name`, `description`, `year`, `active`, `createdAt`, `updatedAt`) VALUES
('kw-iso-combined-v1', 'KW-ISO-COMBINED', 'Integrated Kingdom Way & ISO 9001 Assessment', 'A comprehensive assessment combining Kingdom Way Global principles with ISO 9001:2015 Quality Management standards.', 2025, 1, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('00d7aa7639f38f9892b5f763a3a9e8d1', '9001', 'iso', NULL, 2025, 0, '2025-12-28 13:04:55', '2026-01-01 12:34:03'),
('iso9001-2015', 'ISO 9001:2015', 'Quality Management Systems', 'Requirements for a quality management system', 2015, 0, '2025-12-29 10:13:28', '2026-01-01 12:34:11');

-- ============================================================
-- DATA: categories
-- ============================================================
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `imageUrl`, `parentId`, `order`, `active`, `createdAt`, `updatedAt`) VALUES
('ae3371bfb0006fdde9e556b3ece63dd4', 'screens', 'screens', NULL, NULL, NULL, 0, 1, '2025-12-28 12:56:56', '2025-12-28 12:56:56'),
('cat2', 'LCDd', 'lcd', NULL, NULL, NULL, 0, 1, '2025-12-28 14:15:12', '2025-12-29 06:00:13');

-- ============================================================
-- DATA: clauses
-- ============================================================
INSERT INTO `clauses` (`id`, `isoStandardId`, `number`, `title`, `description`, `weight`, `order`, `createdAt`, `updatedAt`) VALUES
('c4',  'iso9001-2015', '4',  'Context of the Organization', 'Understanding the organization and its context', 1.00, 1, '2025-12-29 10:13:28', '2025-12-29 10:13:28'),
('c5',  'iso9001-2015', '5',  'Leadership',            'Leadership and commitment to the QMS', 1.00, 2, '2025-12-29 10:13:28', '2025-12-29 10:13:28'),
('c6',  'iso9001-2015', '6',  'Planning',              'Actions to address risks and opportunities', 1.00, 3, '2025-12-29 10:13:28', '2025-12-29 10:13:28'),
('c7',  'iso9001-2015', '7',  'Support',               'Resources, competence, awareness, communication, documented information', 1.00, 4, '2025-12-29 10:13:28', '2025-12-29 10:13:28'),
('c8',  'iso9001-2015', '8',  'Operation',             'Operational planning and control', 1.00, 5, '2025-12-29 10:13:28', '2025-12-29 10:13:28'),
('c9',  'iso9001-2015', '9',  'Performance Evaluation','Monitoring, measurement, analysis, evaluation, audit, management review', 1.00, 6, '2025-12-29 10:13:28', '2025-12-29 10:13:28'),
('c10', 'iso9001-2015', '10', 'Improvement',           'Nonconformity, corrective action, continual improvement', 1.00, 7, '2025-12-29 10:13:28', '2025-12-29 10:13:28'),
('combined-clause-KW-1',  'kw-iso-combined-v1', 'KW-1',  'KW: PURPOSE & LEADERSHIP INTEGRITY',            'Evaluate the clarity of purpose and ethical conduct of leadership (Kingdom Way).', 15.00, 1,  '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-KW-2',  'kw-iso-combined-v1', 'KW-2',  'KW: GOVERNANCE & ACCOUNTABILITY',               'Assess the structure, policies, and accountability within the organisation (Kingdom Way).', 15.00, 2, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-KW-3',  'kw-iso-combined-v1', 'KW-3',  'KW: PEOPLE & WORK ENVIRONMENT',                 'Review fairness, safety, and development of employees (Kingdom Way).', 15.00, 3, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-KW-4',  'kw-iso-combined-v1', 'KW-4',  'KW: BUSINESS PRACTICES & OPERATIONS',           'Evaluate honesty, pricing, supplier ethics, and quality standards (Kingdom Way).', 15.00, 4, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-KW-5',  'kw-iso-combined-v1', 'KW-5',  'KW: CUSTOMER & STAKEHOLDER RESPONSIBILITY',     'Assess customer feedback, marketing truthfulness, and data privacy (Kingdom Way).', 15.00, 5, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-KW-6',  'kw-iso-combined-v1', 'KW-6',  'KW: ENVIRONMENTAL & SOCIAL RESPONSIBILITY',     'Review environmental impact and community contribution (Kingdom Way).', 15.00, 6, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-KW-7',  'kw-iso-combined-v1', 'KW-7',  'KW: CONTINUOUS IMPROVEMENT & INNOVATION',       'Evaluate process review, innovation, and commitment to improvement (Kingdom Way).', 10.00, 7, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-ISO-4', 'kw-iso-combined-v1', 'ISO-4', 'ISO: Context of the Organization',              'Understanding the organization and its context (ISO 9001:2015).', 10.00, 8,  '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-ISO-5', 'kw-iso-combined-v1', 'ISO-5', 'ISO: Leadership',                               'Leadership and commitment to the QMS (ISO 9001:2015).', 10.00, 9,  '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-ISO-6', 'kw-iso-combined-v1', 'ISO-6', 'ISO: Planning',                                 'Actions to address risks and opportunities (ISO 9001:2015).', 10.00, 10, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-ISO-7', 'kw-iso-combined-v1', 'ISO-7', 'ISO: Support',                                  'Resources, competence, awareness, communication, documented information (ISO 9001:2015).', 10.00, 11, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-ISO-8', 'kw-iso-combined-v1', 'ISO-8', 'ISO: Operation',                                'Operational planning and control (ISO 9001:2015).', 10.00, 12, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-ISO-9', 'kw-iso-combined-v1', 'ISO-9', 'ISO: Performance Evaluation',                   'Monitoring, measurement, analysis, evaluation, audit, management review (ISO 9001:2015).', 10.00, 13, '2026-01-01 12:15:25', '2026-01-01 12:15:25'),
('combined-clause-ISO-10','kw-iso-combined-v1', 'ISO-10','ISO: Improvement',                               'Nonconformity, corrective action, continual improvement (ISO 9001:2015).', 10.00, 14, '2026-01-01 12:15:25', '2026-01-01 12:15:25');

-- ============================================================
-- DATA: users
-- ============================================================
INSERT INTO `users` (`id`, `email`, `emailVerified`, `password`, `name`, `image`, `role`, `createdAt`, `updatedAt`) VALUES
('183e705b61e924dde99cf5c211a53b2e', 'kairu@gmail.com',         NULL, '$2a$12$/UCDmWYRRN55WZAyZ21xhOJxmmBQpUS/bESs3L3.x3vQWmfXx9Dku', 'kairu',        NULL, 'ADMIN', '2025-12-27 10:25:25', '2025-12-28 13:32:00'),
('86734699f0ca01ab73d485c0a88384bb', 'v@gmail.com',             NULL, '$2a$12$YvAwSq/QHF/n/i9nV8S7.uax2zhJpcZ95HmGX1/2fFesENh.5l4GO', 'vybz',         NULL, 'USER',  '2025-12-29 14:27:23', '2025-12-29 14:27:23'),
('32839eee11666cb6a0cdd4b3bffacfe2', 'microadsales@gmail.com',  NULL, '$2a$12$6hgJMKrZHT7PkhVBk1jhOue2VWRNv8Z3NuGHiLkUhanr2xjLWNESi', 'Microad',       NULL, 'USER',  '2025-12-30 04:16:00', '2025-12-30 04:16:00'),
('acfbe95d6945ffe66f39530e0a0b7006', 'lewis@gmail.com',         NULL, '$2a$12$1iempxoFal1mqg5LILxcj.US3GQiq7ciAgmKpCEplVSqsPdj0qOJm', 'lewis',         NULL, 'ADMIN', '2025-12-27 09:32:11', '2026-01-01 14:53:29'),
('b132d781d957b69f77070b3b77662043', 'lew@gmail.com',           NULL, '$2a$12$gl//pUd64o5u20IhXv1fBO0/EKN7aDSsp0r40RLmCcBxPtQUOrWTy', 'lewis kairu',   NULL, 'USER',  '2026-01-01 14:56:51', '2026-01-01 15:02:11'),
('ea99a5c24cf7f11e85e690d3ddd78a83', 'kairulewis649@gmail.com', NULL, '$2a$12$GQ0T8RQRp1Tlun6/kGPwbuC6XvbBy3X2FyRfvz1Hlh9r3.lZxEQsC', 'lewis kairu',   NULL, 'ADMIN', '2025-12-28 10:04:56', '2026-01-02 10:37:18');

-- ============================================================
-- DATA: assessments
-- ============================================================
INSERT INTO `assessments` (`id`, `userId`, `isoStandardId`, `title`, `status`, `complianceScore`, `maturityLevel`, `completedAt`, `createdAt`, `updatedAt`) VALUES
('ec4dc0072a9d517d4899a74be2125625', 'ea99a5c24cf7f11e85e690d3ddd78a83', '00d7aa7639f38f9892b5f763a3a9e8d1', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2025-12-29 03:37:03', '2025-12-29 03:37:03'),
('62c409ea154e0e500ed98443902f3cb6', 'ea99a5c24cf7f11e85e690d3ddd78a83', '00d7aa7639f38f9892b5f763a3a9e8d1', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2025-12-29 03:37:29', '2025-12-29 03:37:29'),
('825d444c90afd64fb97017744a90d376', 'acfbe95d6945ffe66f39530e0a0b7006', 'iso9001-2015', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2025-12-29 12:38:13', '2025-12-29 12:38:13'),
('19bdbf99e6794bbc7c27f8f3f8f315f2', 'acfbe95d6945ffe66f39530e0a0b7006', 'iso9001-2015', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2025-12-29 13:00:51', '2025-12-29 13:00:51'),
('8e4f8bc566a44dfe27a95b7bf70c994d', 'ea99a5c24cf7f11e85e690d3ddd78a83', 'iso9001-2015', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2025-12-29 13:13:34', '2025-12-29 13:13:34'),
('f4aa5b1635a8c36709d45b9d5c386f33', 'acfbe95d6945ffe66f39530e0a0b7006', 'iso9001-2015', NULL, 'COMPLETED', 68.00, 'Medium', '2025-12-29 23:43:55', '2025-12-29 13:22:51', '2025-12-29 18:43:55'),
('07eebf55e5695e59b9050b2e2cbda760', '32839eee11666cb6a0cdd4b3bffacfe2', 'iso9001-2015', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2025-12-30 04:17:32', '2025-12-30 04:17:32'),
('51942337b668c47200b624b162cbd3e6', '32839eee11666cb6a0cdd4b3bffacfe2', 'iso9001-2015', NULL, 'COMPLETED', 56.00, 'Medium', '2025-12-30 09:22:38', '2025-12-30 04:19:49', '2025-12-30 04:22:42'),
('dd0b95d5ec546b9d327057f46d40c960', 'acfbe95d6945ffe66f39530e0a0b7006', 'kw-iso-combined-v1', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2026-01-01 12:19:04', '2026-01-01 12:19:04'),
('95eff9fe07da75fb710212a91f1d656a', 'acfbe95d6945ffe66f39530e0a0b7006', 'kw-iso-combined-v1', NULL, 'COMPLETED', 11.03, 'Ad-hoc', '2026-01-01 19:02:03', '2026-01-01 13:51:52', '2026-01-01 14:02:03'),
('3b37ff1482bc260d93a9222b8d8d988f', 'ea99a5c24cf7f11e85e690d3ddd78a83', 'kw-iso-combined-v1', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2026-01-02 11:38:53', '2026-01-02 11:38:53'),
('1ba52eb3352913fb738f10edb1527054', 'acfbe95d6945ffe66f39530e0a0b7006', 'kw-iso-combined-v1', NULL, 'IN_PROGRESS', NULL, NULL, NULL, '2026-01-03 04:42:13', '2026-01-03 04:42:13');

-- ============================================================
-- DATA: products
-- ============================================================
INSERT INTO `products` (`id`, `name`, `description`, `price`, `currency`, `sku`, `type`, `fileUrl`, `imageurl`, `active`, `createdAt`, `updatedAt`, `categoryId`, `stock`, `maincategoryid`, `subcategoryid`, `previousprice`, `specialprice`, `specialevent`, `specialactive`, `specialstart`, `specialend`) VALUES
('54e5a172d92bcccfaecec4b84674727e', 'screens', 'screens ', 3000.00, 'USD', 'SKU-1767001341840-LYEPPY', 'digital', NULL, '/uploads/product_1767001341939_r7ddpuyy2i.png', 1, '2025-12-29 04:42:21', '2025-12-29 18:30:40', NULL, 20, 'ae3371bfb0006fdde9e556b3ece63dd4', NULL, NULL, NULL, NULL, 0, NULL, NULL),
('ff5b785a82f3c77e7fa4838cbe067a91', 'screen',  '20inch ',  3005503.00, 'USD', 'SKU-1767001537710-1XFIE3', 'digital', NULL, '/uploads/product_1767005943772_i927uwfgoc.png', 1, '2025-12-29 04:45:40', '2025-12-29 18:32:54', NULL, 270, 'cat2', NULL, NULL, NULL, NULL, 0, NULL, NULL),
('f1ef963a7aacc55277041fb794a80a87', 'car',     'reihgeqh', 1112121.00, 'USD', 'SKU-1767289650569-ZDV7VJ', 'physical', NULL, NULL, 1, '2026-01-01 12:47:30', '2026-01-02 11:15:46', NULL, 200, 'cat2', NULL, NULL, NULL, NULL, 0, NULL, NULL);

-- ============================================================
-- DATA: product_images
-- ============================================================
INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `sort_order`, `created_at`) VALUES
(1, 'ff5b785a82f3c77e7fa4838cbe067a91', '/uploads/product_1767050064451_sh1vwyzdat.jpeg', 0, '2025-12-29 18:14:24'),
(2, 'ff5b785a82f3c77e7fa4838cbe067a91', '/uploads/product_1767050064499_00bnv2anj8dfi.png',  1, '2025-12-29 18:14:24'),
(3, 'ff5b785a82f3c77e7fa4838cbe067a91', '/uploads/product_1767050064502_cvvckevcpuh.png',   2, '2025-12-29 18:14:24'),
(4, 'ff5b785a82f3c77e7fa4838cbe067a91', '/uploads/product_1767050064505_sq5cyopy71g.jpeg',  3, '2025-12-29 18:14:24'),
(5, '54e5a172d92bcccfaecec4b84674727e', '/uploads/product_1767051040549_a3ac5g8vtf.png',    0, '2025-12-29 18:30:40'),
(6, 'f1ef963a7aacc55277041fb794a80a87', '/uploads/product_1767289650634_dwna6dff6p4.jpeg',  0, '2026-01-01 12:47:30');

-- ============================================================
-- DATA: product_recommendations
-- ============================================================
INSERT INTO `product_recommendations` (`id`, `product_id`, `recommended_product_id`, `sort_order`, `created_at`) VALUES
(1, '54e5a172d92bcccfaecec4b84674727e', 'ff5b785a82f3c77e7fa4838cbe067a91', 0, '2025-12-29 18:30:03'),
(2, 'ff5b785a82f3c77e7fa4838cbe067a91', '54e5a172d92bcccfaecec4b84674727e', 0, '2025-12-29 18:33:27');

-- ============================================================
-- DATA: product_category_recommendations
-- ============================================================
INSERT INTO `product_category_recommendations` (`id`, `product_id`, `category_id`, `sort_order`, `created_at`) VALUES
(1, 'f1ef963a7aacc55277041fb794a80a87', 'cat2', 0, '2026-01-02 11:15:47');

-- ============================================================
-- DATA: leads
-- ============================================================
INSERT INTO `leads` (`id`, `userId`, `isoStandardId`, `companyName`, `contactName`, `contactEmail`, `contactPhone`, `companySize`, `currentStatus`, `requirements`, `status`, `assignedPartnerId`, `notes`, `createdAt`, `updatedAt`, `lastMessageAt`, `unreadMessagesCount`, `companyLogo`) VALUES
('3740a77d662bd36ed46dd979a6edac7f', 'acfbe95d6945ffe66f39530e0a0b7006', 'kw-iso-combined-v1', 'iworth t', 'lewis', 'lewis@gmail.com', '', NULL, NULL, NULL, 'New', NULL, NULL, '2026-01-01 14:19:47', '2026-01-01 14:53:29', NULL, 0, '/uploads/a5770ede-db46-47e3-bc9f-578b0f47be10-WhatsApp-Image-2025-11-13-at-4.31.03-AM.jpeg'),
('6de1fe99965e77b975adf93e9f345da3', 'b132d781d957b69f77070b3b77662043', 'kw-iso-combined-v1', 'lewis', 'lewis kairu', 'lew@gmail.com', '07243333333', NULL, NULL, 'logo', 'New', NULL, NULL, '2026-01-01 14:56:51', '2026-01-01 15:02:11', NULL, 0, '/uploads/89be093a-2a6e-405c-a721-a2898801047d-WhatsApp-Image-2025-11-13-at-4.31.03-AM.jpeg');

-- ============================================================
-- DATA: nominations
-- ============================================================
INSERT INTO `nominations` (`id`, `nominatorName`, `nominatorEmail`, `nomineeName`, `nomineeEmail`, `nominationType`, `reason`, `status`, `createdAt`, `updatedAt`) VALUES
('7d3756ad56248458c66bb9a1fd2e0e56', 'lawi', 'l@gmail.com', 'iworth', NULL, 'ORGANIZATION', 'bjhjdhdhjjhhjzxjhajk', 'NEW', '2026-01-02 08:37:06', '2026-01-02 08:37:06');

-- ============================================================
-- DATA: terms_and_conditions
-- ============================================================
INSERT INTO `terms_and_conditions` (`id`, `title`, `content`, `version`, `is_active`, `created_at`) VALUES
(1, 'ISO Compliance Platform Terms & Conditions', 'By using this platform, you agree to:\n\n1. Provide accurate and truthful information during all assessments.\n2. Upload only genuine and relevant evidence documents when required.\n3. Understand that self-assessment does not constitute official ISO certification.\n4. Accept that all assessment data may be reviewed by platform administrators for compliance and quality.\n5. Acknowledge that your assessment results are provisional until reviewed and approved by an authorized ISO officer.\n6. Comply with all applicable laws and regulations regarding data privacy and information security.\n7. Accept that the platform may update these terms and require re-acceptance for continued use.\n\nDisclaimer: This self-assessment tool is for internal readiness and gap analysis only. Final ISO certification requires an independent accredited audit.', '1.0', 1, '2025-12-29 06:20:36');

-- ============================================================
-- DATA: user_terms_acceptances
-- ============================================================
INSERT INTO `user_terms_acceptances` (`id`, `user_id`, `terms_id`, `accepted_at`, `ip_address`, `user_agent`) VALUES
(1, 'acfbe95d-6945-ffe6-6f39-530e0a0b7006', 1, '2025-12-29 16:29:17', '::ffff:127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0');

-- ============================================================
-- ============================================================
-- DATA: site_settings
-- ============================================================
INSERT INTO `site_settings` (`id`, `key`, `value`, `createdAt`, `updatedAt`, `category`, `type`, `label`, `description`, `ispublic`, `requiresrestart`, `currency`, `currencysymbol`, `inventoryenabled`, `lowstockthreshold`) VALUES
('c8ef83523cd0473348cee03f684a9038','theme_success_color','#0F766E','2025-12-28 12:00:18','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('4954a552-5549-4c92-b3da-aae700b9a228','login_attempt_limit','5','2025-12-28 12:21:38','2025-12-28 12:21:38','security','number','Login Attempt Limit','Max failed login attempts before lockout',0,0,'USD','$',1,5),
('72007864-ec76-4085-8b3a-106962b8a0af','login_lockout_duration','900','2025-12-28 12:21:38','2025-12-28 12:21:38','security','number','Lockout Duration (seconds)','Time to lock account after failed attempts (15 minutes default)',0,0,'USD','$',1,5),
('7e9fda67-07d5-4220-b92c-52b6dc399e7c','api_rate_limit','100','2025-12-28 12:21:38','2025-12-28 12:21:38','security','number','API Rate Limit','API requests per minute per IP',0,0,'USD','$',1,5),
('3d53e365-c20c-464e-835c-2810699e69df','force_password_reset_days','90','2025-12-28 12:21:38','2025-12-28 12:21:38','security','number','Force Password Reset (days)','Days before forcing password reset',0,0,'USD','$',1,5),
('d2bd791a-4cb2-4444-821b-02718c8b7721','audit_log_retention_days','365','2025-12-28 12:21:38','2025-12-28 12:21:38','security','number','Audit Log Retention (days)','Days to retain audit logs',0,0,'USD','$',1,5),
('55c20433-4d85-4edc-a310-a1584793f1b3','contact_email','','2025-12-30 15:30:36','2026-01-02 10:28:13','general','string','Contact Email','Main contact email address displayed in footer',0,0,'USD','$',1,5),
('e56a5e38-5096-49ef-869b-7a0dac43df31','contact_phone','0723849943','2025-12-30 15:30:36','2026-01-02 10:28:13','general','string','Contact Phone','Main contact phone number displayed in footer',0,0,'USD','$',1,5),
('b1c70433-4344-4a28-80a0-bf48337d87d3','social_facebook','','2025-12-30 15:30:36','2026-01-02 10:28:13','general','string','Facebook URL','Your Facebook page/profile URL',0,0,'USD','$',1,5),
('2acdac98-eb32-4d75-a28b-43b838d33ffe','social_instagram','','2025-12-30 15:30:36','2026-01-02 10:28:13','general','string','Instagram URL','Your Instagram profile URL',0,0,'USD','$',1,5),
('9f7129ee-86eb-439e-aaf7-5e835f5772d9','social_linkedin','','2025-12-30 15:30:36','2026-01-02 10:28:13','general','string','LinkedIn URL','Your LinkedIn company/profile URL',0,0,'USD','$',1,5),
('88c79174-3139-4c0d-9764-b2830a42c40c','social_twitter','','2025-12-30 15:30:36','2026-01-02 10:28:13','general','string','Twitter URL','Your Twitter/X profile URL',0,0,'USD','$',1,5),
('5236ca88-85e9-4387-af8f-41e95eab203a','social_youtube','','2025-12-30 15:30:36','2026-01-02 10:28:13','general','string','YouTube URL','Your YouTube channel URL',0,0,'USD','$',1,5),
('7a9ee3a9-4726-48a4-b3d0-e2d290922e7c','company_logo','/uploads/upload_1767296418660_y0c8b3.jpeg','2025-12-27 09:44:56','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('992ca477-51d2-4029-a07b-7965bedc10d7','company_name','Kingdom Way Global ','2025-12-27 09:44:56','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('de20c4e7-c652-4ce1-b631-9ce8feb486d1','footer_text','© 2024 Kingdom Way Global Organization All rights reserved.','2025-12-27 09:44:56','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('3073ca081ce41cd11965f24a05dbece2','theme_accent_color','#7C3AED','2025-12-28 12:00:18','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('993c8b785da6093318de5070c27d0958','theme_background_color','#FAFBFC','2025-12-28 12:00:18','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('1ba387df2a4cceb0884d837584c25779','theme_error_color','#DC2626','2025-12-28 12:00:18','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('33da05d4676c17d258e81be7ec890c95','theme_primary_color','#475569','2025-12-28 12:00:18','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('f3a697a1-fd67-45d1-9d48-f697b4e83731','contact_address','coffee Plaza ,6th floor,off Haile Sellasie Avenue -Nairobi','2025-12-30 15:30:36','2026-01-02 10:28:13','general','text','Contact Address','Physical address displayed in footer',0,0,'USD','$',1,5),
('f6337d8db54329a96e66adce3a6dbd33','theme_primary_hover_color','#334155','2025-12-28 12:00:18','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('cbcfa39cd33486a90e146cdb691e14f4','theme_primary_soft_color','#F1F5F9','2025-12-28 12:00:18','2026-01-02 10:28:13',NULL,'string',NULL,NULL,0,0,'USD','$',1,5),
('cd15c5e2-21fe-4f0a-a69a-b3b73469eef2','registration_enabled','true','2025-12-28 12:23:36','2025-12-28 12:23:36','users','boolean','Enable Registration','Allow new users to register',0,0,'USD','$',1,5),
('70f2845a-113c-47db-b050-514be5613aaa','default_user_role','USER','2025-12-28 12:23:36','2025-12-28 12:23:36','users','string','Default User Role','Role assigned to new users',0,0,'USD','$',1,5),
('5e1faf5b-e738-4064-872e-8d0291bc4646','password_min_length','8','2025-12-28 12:23:36','2025-12-28 12:23:36','users','number','Minimum Password Length','Minimum characters required for passwords',0,0,'USD','$',1,5),
('d1e71dcc-00f8-4a25-8c17-e26e3593b207','password_require_uppercase','true','2025-12-28 12:23:36','2025-12-28 12:23:36','users','boolean','Require Uppercase','Passwords must contain uppercase letters',0,0,'USD','$',1,5),
('b56e7d55-c4b6-4943-93e2-663a1627a185','password_require_lowercase','true','2025-12-28 12:23:36','2025-12-28 12:23:36','users','boolean','Require Lowercase','Passwords must contain lowercase letters',0,0,'USD','$',1,5),
('efcefbd6-e441-445b-9004-d62503d1c4e3','password_require_numbers','true','2025-12-28 12:23:36','2025-12-28 12:23:36','users','boolean','Require Numbers','Passwords must contain numbers',0,0,'USD','$',1,5),
('1c2fb450-f2e5-4c88-88c1-7fd89ea9523f','password_require_special','false','2025-12-28 12:23:36','2025-12-28 12:23:36','users','boolean','Require Special Characters','Passwords must contain special characters',0,0,'USD','$',1,5),
('dbfe0b20-b001-419f-b5a8-1de5fb429fb3','session_timeout','3600','2025-12-28 12:23:36','2025-12-28 12:23:36','users','number','Session Timeout (seconds)','Time before user session expires (in seconds)',0,0,'USD','$',1,5),
('00308686-cd2a-4aaa-be21-0c7b5ad16d21','two_factor_enabled','false','2025-12-28 12:23:36','2025-12-28 12:23:36','users','boolean','Enable Two-Factor Authentication','Require 2FA for admin accounts',0,0,'USD','$',1,5),
('b8dd6353-730d-4980-80d1-3b3766243f2f','email_verification_required','false','2025-12-28 12:23:36','2025-12-28 12:23:36','users','boolean','Require Email Verification','Users must verify email before accessing the platform',0,0,'USD','$',1,5),
('8e4d8a36-c61e-4b3c-ab7e-1550215cefd6','sku_format','SKU-{id}','2025-12-28 12:23:36','2025-12-28 12:23:36','inventory','string','SKU Format','Format for generating SKUs (use {id} for product ID)',0,0,'USD','$',1,5),
('e4da17c4-5e5e-4553-904b-edd16906b144','low_stock_threshold','10','2025-12-28 12:23:36','2025-12-28 12:23:36','inventory','number','Low Stock Threshold','Alert when stock falls below this number',0,0,'USD','$',1,5),
('32ecf937-0a89-412e-bdce-f7f5813be049','stock_reservation_timeout','900','2025-12-28 12:23:36','2025-12-28 12:23:36','inventory','number','Stock Reservation Timeout (seconds)','Time to hold reserved stock (15 minutes default)',0,0,'USD','$',1,5),
('e62a3e33-9d49-4c32-8710-757669ed0b70','stock_alerts_enabled','true','2025-12-28 12:23:36','2025-12-28 12:23:36','inventory','boolean','Enable Stock Alerts','Send alerts when stock is low',0,0,'USD','$',1,5),
('d1d52f2b-b18d-44a8-bedb-dedcf15fde6e','inventory_audit_frequency','monthly','2025-12-28 12:23:36','2025-12-28 12:23:36','inventory','string','Inventory Audit Frequency','How often to perform inventory audits (daily/weekly/monthly)',0,0,'USD','$',1,5),
('eaf21276-a30f-44f5-9da4-5fb82c571aa2','iso_scoring_mode','auto','2025-12-28 12:23:36','2025-12-28 12:23:36','iso_compliance','string','Scoring Mode','Scoring calculation mode: auto, manual, or hybrid',0,0,'USD','$',1,5),
('caeae143-088a-460d-bcdc-bbc6a7a62111','iso_readiness_threshold','70','2025-12-28 12:23:36','2025-12-28 12:23:36','iso_compliance','number','Readiness Threshold (%)','Minimum score percentage for ISO readiness',0,0,'USD','$',1,5),
('42befabb-d382-4870-bab8-79f5fdc9353c','certificate_validity_days','365','2025-12-28 12:23:36','2025-12-28 12:23:36','iso_compliance','number','Certificate Validity (days)','Number of days certificates remain valid',0,0,'USD','$',1,5),
('1a96f13e-bd21-4d98-b7c6-54305a0e4461','audit_workflow_stages','["preparation","planning","execution","reporting","followup"]','2025-12-28 12:23:36','2025-12-28 12:23:36','iso_compliance','json','Audit Workflow Stages','JSON array of audit workflow stages',0,0,'USD','$',1,5),
('5865cba6-ba73-4a3b-b5ab-57146e4387a6','currency_symbol','ksh','2025-12-28 12:23:36','2025-12-28 13:57:09','ecommerce','string','Currency Symbol','Currency symbol to display',0,0,'USD','$',1,5),
('b711bfc0-7124-4391-8c47-026661df6b5d','invoice_prefix','INV-','2025-12-28 12:23:36','2025-12-28 13:57:09','ecommerce','string','Invoice Prefix','Prefix for invoice numbers',0,0,'USD','$',1,5),
('8aeb6f1e-c7b1-43e7-8008-ae5f71e687c1','order_status_workflow','["pending","processing","shipped","delivered","cancelled"]','2025-12-28 12:23:36','2025-12-28 13:57:09','ecommerce','json','Order Status Workflow','JSON array of order statuses in workflow order',0,0,'USD','$',1,5),
('11de78e8-3f02-4cde-9971-a71443b31a6b','payment_paypal_enabled','false','2025-12-28 12:23:36','2025-12-28 13:57:09','ecommerce','boolean','Enable PayPal Payments','Allow PayPal payment processing',0,0,'USD','$',1,5),
('63bb5cc1-fc10-40ae-9b90-76d0ffa5f36b','payment_stripe_enabled','false','2025-12-28 12:23:36','2025-12-28 13:57:09','ecommerce','boolean','Enable Stripe Payments','Allow Stripe payment processing',0,0,'USD','$',1,5),
('56bd067a-3b9b-47f2-bbdf-404364512b1c','tax_rate','0','2025-12-28 12:23:36','2025-12-28 13:57:09','ecommerce','number','Tax Rate','Default tax rate (as decimal, e.g., 0.08 for 8%)',0,0,'USD','$',1,5),
('30fcd946-090d-4d29-b5f5-82f27b1152c5','document_max_size_mb','10','2025-12-28 12:23:36','2025-12-28 12:23:36','documents','number','Max Upload Size (MB)','Maximum file size for document uploads',0,0,'USD','$',1,5),
('cdacb3bf-04c4-41b6-a724-4b2dd7df605d','document_allowed_types','["pdf","doc","docx","xls","xlsx","png","jpg","jpeg"]','2025-12-28 12:23:36','2025-12-28 12:23:36','documents','json','Allowed File Types','JSON array of allowed file extensions',0,0,'USD','$',1,5),
('67b11054-cece-4790-9510-86658708222b','document_storage_location','local','2025-12-28 12:23:36','2025-12-28 12:23:36','documents','string','Storage Location','Storage backend: local or cloud',0,0,'USD','$',1,5),
('c4e3e112-f746-4fb9-9f15-87a688462e8b','document_versioning_enabled','true','2025-12-28 12:23:36','2025-12-28 12:23:36','documents','boolean','Enable Versioning','Keep version history for documents',0,0,'USD','$',1,5),
('2fa27c2e-369d-4677-9279-30ed7743137e','document_retention_days','365','2025-12-28 12:23:36','2025-12-28 12:23:36','documents','number','Retention Period (days)','Days to retain documents before deletion',0,0,'USD','$',1,5),
('ed71f4de-5873-497f-9af1-2edb858578e2','document_expiry_reminder_days','30','2025-12-28 12:23:36','2025-12-28 12:23:36','documents','number','Expiry Reminder (days)','Days before expiry to send reminder',0,0,'USD','$',1,5),
('f8cd35cd-3b4a-4794-85d9-8bbb6d869b85','notifications_email_enabled','true','2025-12-28 12:23:36','2025-12-28 12:23:36','notifications','boolean','Enable Email Notifications','Send notifications via email',0,0,'USD','$',1,5),
('5e4ff531-7e83-4061-80ed-33ce521cd58d','notifications_sms_enabled','false','2025-12-28 12:23:36','2025-12-28 12:23:36','notifications','boolean','Enable SMS Notifications','Send notifications via SMS',0,0,'USD','$',1,5),
('9afee316-c3e1-4509-9e44-cb089bcaf9e6','admin_alert_threshold','5','2025-12-28 12:23:36','2025-12-28 12:23:36','notifications','number','Admin Alert Threshold','Number of issues before admin alert',0,0,'USD','$',1,5),
('f7a19adc-e4b0-48ab-b55f-19fe2f638cd9','daily_summary_enabled','false','2025-12-28 12:23:36','2025-12-28 12:23:36','notifications','boolean','Enable Daily Summary','Send daily summary emails to admins',0,0,'USD','$',1,5),
('e206ef31-874d-4012-836a-b48ebd08266c','weekly_summary_enabled','true','2025-12-28 12:23:36','2025-12-28 12:23:36','notifications','boolean','Enable Weekly Summary','Send weekly summary emails to admins',0,0,'USD','$',1,5),
('9426fdba-3c58-484d-9539-ee96d37090bf','email_smtp_host','','2025-12-28 12:23:36','2025-12-28 12:23:36','integrations','string','SMTP Host','SMTP server hostname',0,0,'USD','$',1,5),
('22414bbc-3554-43b2-be3e-938786b91db7','email_smtp_port','587','2025-12-28 12:23:36','2025-12-28 12:23:36','integrations','number','SMTP Port','SMTP server port',0,0,'USD','$',1,5),
('6628e89e-ca5e-46be-98d6-59112d5a9f83','email_smtp_user','','2025-12-28 12:23:36','2025-12-28 12:23:36','integrations','string','SMTP Username','SMTP authentication username',0,0,'USD','$',1,5),
('09251885-6cc7-4ac8-abaf-401d8493562f','email_smtp_password','','2025-12-28 12:23:36','2025-12-28 12:23:36','integrations','string','SMTP Password','SMTP authentication password (encrypted)',0,0,'USD','$',1,5),
('da73632e-a52d-47c7-976d-7656ffbc2459','email_from_address','','2025-12-28 12:23:36','2025-12-28 12:23:36','integrations','string','From Email Address','Default sender email address',0,0,'USD','$',1,5),
('0b9767ae-68c5-4b26-aa54-adb08571c733','sms_provider','','2025-12-28 12:23:36','2025-12-28 12:23:36','integrations','string','SMS Provider','SMS service provider (twilio, etc.)',0,0,'USD','$',1,5),
('fc6794fc-efee-4ed1-956f-1497bc54f6ec','storage_provider','local','2025-12-28 12:23:36','2025-12-28 12:23:36','integrations','string','Storage Provider','Cloud storage provider (aws, gcp, azure, local)',0,0,'USD','$',1,5),
('29683a64-02b4-448a-b0fc-c9f19603b2e6','currency','ksh','2025-12-28 12:23:36','2025-12-28 13:57:09','ecommerce','string','Currency','Default currency code (ISO 4217)',0,0,'USD','$',1,5);

-- DATA: questions (ISO 9001:2015)
-- ============================================================
INSERT INTO `questions` (`id`, `clauseId`, `text`, `description`, `type`, `options`, `weight`, `order`, `required`, `createdAt`, `updatedAt`) VALUES
('q4_1','c4','Has the organization identified internal and external issues relevant to its purpose and strategic direction?',NULL,'YES_NO',NULL,1.00,1,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q4_2','c4','Are the needs and expectations of interested parties determined and reviewed?',NULL,'YES_NO',NULL,1.00,2,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q4_3','c4','Is the scope of the QMS documented and maintained?',NULL,'YES_NO',NULL,1.00,3,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q5_1','c5','Has top management demonstrated leadership and commitment to the QMS?',NULL,'YES_NO',NULL,1.00,1,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q5_2','c5','Is a quality policy established, implemented, and communicated?',NULL,'YES_NO',NULL,1.00,2,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q5_3','c5','Are roles, responsibilities, and authorities assigned and communicated?',NULL,'YES_NO',NULL,1.00,3,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q6_1','c6','Are risks and opportunities that could affect the QMS identified and addressed?',NULL,'YES_NO',NULL,1.00,1,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q6_2','c6','Are quality objectives established at relevant functions and levels?',NULL,'YES_NO',NULL,1.00,2,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q6_3','c6','Are changes to the QMS planned and managed?',NULL,'YES_NO',NULL,1.00,3,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q7_1','c7','Are resources determined and provided for the QMS?',NULL,'YES_NO',NULL,1.00,1,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q7_2','c7','Are personnel competent based on education, training, or experience?',NULL,'YES_NO',NULL,1.00,2,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q7_3','c7','Are employees aware of the quality policy and their contribution to the QMS?',NULL,'YES_NO',NULL,1.00,3,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q7_4','c7','Is documented information controlled and maintained?',NULL,'YES_NO',NULL,1.00,4,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q8_1','c8','Are processes for operational planning and control implemented?',NULL,'YES_NO',NULL,1.00,1,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q8_2','c8','Are customer requirements for products and services determined and reviewed?',NULL,'YES_NO',NULL,1.00,2,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q8_3','c8','Is design and development controlled (if applicable)?',NULL,'YES_NO',NULL,1.00,3,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q8_4','c8','Are controls in place for externally provided processes, products, and services?',NULL,'YES_NO',NULL,1.00,4,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q8_5','c8','Is production and service provision carried out under controlled conditions?',NULL,'YES_NO',NULL,1.00,5,0,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q8_6','c8','Are products and services released only after meeting requirements?',NULL,'YES_NO',NULL,1.00,6,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q8_7','c8','Are nonconforming outputs identified and controlled?',NULL,'YES_NO',NULL,1.00,7,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q9_1','c9','Are monitoring, measurement, analysis, and evaluation activities conducted?',NULL,'YES_NO',NULL,1.00,1,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q9_2','c9','Are internal audits planned and conducted?',NULL,'YES_NO',NULL,1.00,2,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q9_3','c9','Does management review the QMS at planned intervals?',NULL,'YES_NO',NULL,1.00,3,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q10_1','c10','Are nonconformities and corrective actions managed?',NULL,'YES_NO',NULL,1.00,1,1,'2025-12-29 10:13:28','2025-12-29 10:13:28'),
('q10_2','c10','Is there evidence of continual improvement of the QMS?',NULL,'YES_NO',NULL,1.00,2,1,'2025-12-29 10:13:28','2025-12-29 10:13:28');

-- ============================================================
-- DATA: questions (KW-ISO combined – SCALE type)
-- ============================================================
INSERT INTO `questions` (`id`, `clauseId`, `text`, `description`, `type`, `options`, `weight`, `order`, `required`, `createdAt`, `updatedAt`) VALUES
('srskaxtiqcfjuv8by8vw88','combined-clause-KW-1','Our organisation has a clearly defined purpose beyond profit.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('3swcs2dgct92tqct12vk4k','combined-clause-KW-1','Senior leadership demonstrates ethical conduct in decision-making.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('mensmr1fohekeas60e4rz8','combined-clause-KW-1','Leadership behaviours are consistent with stated values.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('95ppm4obxjapsbe0zanyc9','combined-clause-KW-1','Ethical leadership expectations are communicated across the organisation.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('alzubkpfnst0uwayiuniu4s','combined-clause-KW-1','Conflicts of interest are disclosed and managed transparently.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('vja0lqg7y1oy48fk75v6gc','combined-clause-KW-2','Roles, responsibilities, and authority levels are clearly defined.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('xma53nga6396ko6sfzxfma','combined-clause-KW-2','The organisation has documented policies guiding ethical conduct.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('ys9lbdlcb99tr3m24g9o','combined-clause-KW-2','Decisions are recorded and traceable.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('3yo6a79rhbegcp8r8uq7yi','combined-clause-KW-2','Performance is reviewed against agreed objectives.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('22wqksbbnvqis8jr22afsjl','combined-clause-KW-2','Leadership is accountable for organisational outcomes.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('wc0bw5redk5t19bm4ng','combined-clause-KW-3','Employees are treated fairly and respectfully.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('9whvxpv75ofvxxyxic6c7','combined-clause-KW-3','The organisation promotes a safe and healthy work environment.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('pc2aowfti62zxgq6piiut','combined-clause-KW-3','Equal opportunity and inclusion are actively supported.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('fk1xlwzokimp5rvcpg2dop','combined-clause-KW-3','Staff development and capacity building are prioritised.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('89nd28dz5ybw57r7wm1lx','combined-clause-KW-3','Workplace grievances are handled transparently and fairly.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('hoc52w2etydetdlfvpz8lw','combined-clause-KW-4','Products or services are delivered honestly and as promised.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('0nuho526fvmghe1e73sbt5','combined-clause-KW-4','Pricing is fair, transparent, and justifiable.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('kgn2jku42jryy23wlb82s','combined-clause-KW-4','Suppliers and partners are selected ethically.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('nge0uunzdmoejzyxf7j7e','combined-clause-KW-4','Business risks are identified and managed responsibly.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('6f3b0uyirnsovx7u0myll','combined-clause-KW-4','Quality standards are consistently applied.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('z8cbnifd2vsz2y0f71rfgg','combined-clause-KW-5','Customer feedback is actively collected and addressed.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('z00i700mhvnd4ofn88zj','combined-clause-KW-5','Marketing and communication are truthful and responsible.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('397brzl7kmpt93v169girj','combined-clause-KW-5','Stakeholders are treated with respect and professionalism.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('23a8sx7d8acfwag9qf0or5','combined-clause-KW-5','Data and privacy are protected appropriately.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('dzysvuhcwkg2aq2blmc7re','combined-clause-KW-5','Complaints are resolved fairly and timely.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('hl8i8bf4gnwxorb84uelgb','combined-clause-KW-6','The organisation considers environmental impact in its operations.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('rn1ojzoihhg3clx7ohk58','combined-clause-KW-6','Waste reduction or responsible disposal practices are in place.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('rxjsj0w3tekzviv471auu','combined-clause-KW-6','Resource use (energy, water, materials) is monitored or managed.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('pg42ma61a6muggj8o7r7kc','combined-clause-KW-6','The organisation contributes positively to its community.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('2ku501rpbhlyj9irir8eqq','combined-clause-KW-6','Long-term sustainability is part of business planning.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('vsqaw18d4wm315955pwhlq','combined-clause-KW-7','The organisation regularly reviews its processes.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('q1949rj0nwu4z5vm5qpbc','combined-clause-KW-7','Lessons learned are documented and applied.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('rev6g6t5bswq902tocedl','combined-clause-KW-7','Innovation is encouraged ethically and responsibly.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('c6h286ln5xg1v4m9hgphpp','combined-clause-KW-7','Compliance requirements are monitored.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('pg2g121k0y9uzb32gprbu','combined-clause-KW-7','The organisation is committed to improvement over time.',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('iwm6ytxvm7krxkb9m7gfqs','combined-clause-ISO-4','Has the organization identified internal and external issues relevant to its purpose and strategic direction?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('jwi9p6w2ism681gkf50prt','combined-clause-ISO-4','Are the needs and expectations of interested parties determined and reviewed?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('t5dedt02uzmxgc8m9jm3','combined-clause-ISO-4','Is the scope of the QMS documented and maintained?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('1o3sje2xo6al33x9bkobc','combined-clause-ISO-5','Has top management demonstrated leadership and commitment to the QMS?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('4wqj377hljv0h9grw0wobk','combined-clause-ISO-5','Is a quality policy established, implemented, and communicated?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('v1w594veett0esgiw0bq','combined-clause-ISO-5','Are roles, responsibilities, and authorities assigned and communicated?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('as6m3c746nef0oud61gu8','combined-clause-ISO-6','Are risks and opportunities that could affect the QMS identified and addressed?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('hm7jwsduuwpigzw5oogp','combined-clause-ISO-6','Are quality objectives established at relevant functions and levels?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('l1bk2jutz7cpnwutrpl5qp','combined-clause-ISO-6','Are changes to the QMS planned and managed?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('8ehwl6kjfzuvjnvbdqbcca','combined-clause-ISO-7','Are resources determined and provided for the QMS?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('bh108qb0yh5m4a4s5ikn4','combined-clause-ISO-7','Are personnel competent based on education, training, or experience?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('sekqj60xrps9n1wb52wnf','combined-clause-ISO-7','Are employees aware of the quality policy and their contribution to the QMS?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('i7qnxaod0qqun82i1e5k','combined-clause-ISO-7','Is documented information controlled and maintained?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('rr3z81ut2up55z4pxrax9o','combined-clause-ISO-8','Are processes for operational planning and control implemented?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('v7qo1tt4nk01siewkr9jtc','combined-clause-ISO-8','Are customer requirements for products and services determined and reviewed?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('w7ipjp447tmrbiya1okcv','combined-clause-ISO-8','Is design and development controlled (if applicable)?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('3r5jen1qwsmqx68y6fji1','combined-clause-ISO-8','Are controls in place for externally provided processes, products, and services?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,4,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('a5g2dabg68jm64kbtbaf8','combined-clause-ISO-8','Is production and service provision carried out under controlled conditions?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,5,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('wwodbpy3ivehahqbpqzplu','combined-clause-ISO-8','Are products and services released only after meeting requirements?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,6,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('lnc9zpltipgc0bml7sjn6','combined-clause-ISO-8','Are nonconforming outputs identified and controlled?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,7,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('06j9c51lycgaekr3nsdpuzh','combined-clause-ISO-9','Are monitoring, measurement, analysis, and evaluation activities conducted?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('01jcizn5pqezmw95e169c7','combined-clause-ISO-9','Are internal audits planned and conducted?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('672rzmhqs2elm9gtsddnwn','combined-clause-ISO-9','Does management review the QMS at planned intervals?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,3,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('6nmqspsgxi778ggmebyt5','combined-clause-ISO-10','Are nonconformities and corrective actions managed?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,1,1,'2026-01-01 12:15:25','2026-01-01 12:15:25'),
('irwo42pxygdkkp5q94rne','combined-clause-ISO-10','Is there evidence of continual improvement of the QMS?',NULL,'SCALE','{"max":3,"min":0,"labels":["Not in place","Informal","Partially","Fully"]}',1.00,2,1,'2026-01-01 12:15:25','2026-01-01 12:15:25');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- END OF MIGRATION
-- Run with: mysql -u root < mysql_migration.sql
-- ============================================================
