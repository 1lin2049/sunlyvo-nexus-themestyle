<?php
/**
 * SunLyvo Nexus 数据库表创建脚本
 *
 * 使用 dbDelta() 安全创建/更新表结构。
 * 仅在 SLV_DB_VERSION 变化时执行，避免每次页面加载运行 DDL。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 创建所有自定义表。
 *
 * @since 1.0.0
 */
function slv_create_tables(): void {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // ─── 4.2.1 自研电商引擎表（18张）───────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_products (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_type VARCHAR(30) NOT NULL DEFAULT 'simple',
        sku VARCHAR(100) DEFAULT '',
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        description LONGTEXT,
        short_description TEXT,
        status VARCHAR(20) DEFAULT 'draft',
        vendor_id BIGINT(20) DEFAULT 0,
        station_id BIGINT(20) DEFAULT 0,
        price DECIMAL(12,2) DEFAULT 0,
        compare_price DECIMAL(12,2) DEFAULT 0,
        cost_price DECIMAL(12,2) DEFAULT 0,
        wholesale_price DECIMAL(12,2) DEFAULT 0,
        currency VARCHAR(10) DEFAULT 'USD',
        tax_status VARCHAR(20) DEFAULT 'taxable',
        tax_class VARCHAR(50) DEFAULT '',
        manage_stock TINYINT(1) DEFAULT 0,
        stock_quantity INT DEFAULT 0,
        stock_status VARCHAR(20) DEFAULT 'instock',
        weight DECIMAL(10,3) DEFAULT 0,
        length DECIMAL(10,3) DEFAULT 0,
        width DECIMAL(10,3) DEFAULT 0,
        height DECIMAL(10,3) DEFAULT 0,
        hs_code VARCHAR(50) DEFAULT '',
        moq INT DEFAULT 1,
        lead_time VARCHAR(50) DEFAULT '',
        is_featured TINYINT(1) DEFAULT 0,
        is_virtual TINYINT(1) DEFAULT 0,
        is_downloadable TINYINT(1) DEFAULT 0,
        average_rating DECIMAL(3,2) DEFAULT 0,
        review_count INT DEFAULT 0,
        sale_count INT DEFAULT 0,
        view_count INT DEFAULT 0,
        published_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_sku (sku),
        UNIQUE KEY uniq_slug (slug),
        KEY idx_vendor_id (vendor_id),
        KEY idx_station_id (station_id),
        KEY idx_status (status),
        KEY idx_product_type (product_type),
        KEY idx_price (price),
        KEY idx_published_at (published_at)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_product_variants (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) NOT NULL,
        sku VARCHAR(100) DEFAULT '',
        attributes TEXT,
        price DECIMAL(12,2) DEFAULT 0,
        wholesale_price DECIMAL(12,2) DEFAULT 0,
        stock_quantity INT DEFAULT 0,
        stock_status VARCHAR(20) DEFAULT 'instock',
        image_id BIGINT(20) DEFAULT 0,
        weight DECIMAL(10,3) DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_sku (sku),
        KEY idx_product_id (product_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_product_categories (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) NOT NULL,
        category_id BIGINT(20) NOT NULL,
        is_primary TINYINT(1) DEFAULT 0,
        sort_order INT DEFAULT 0,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_product_category (product_id, category_id),
        KEY idx_category_id (category_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_product_images (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) NOT NULL,
        attachment_id BIGINT(20) NOT NULL,
        is_primary TINYINT(1) DEFAULT 0,
        sort_order INT DEFAULT 0,
        PRIMARY KEY  (id),
        KEY idx_product_id (product_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_carts (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        cart_key VARCHAR(64) NOT NULL,
        user_id BIGINT(20) DEFAULT 0,
        session_id VARCHAR(64) DEFAULT '',
        currency VARCHAR(10) DEFAULT 'USD',
        subtotal DECIMAL(12,2) DEFAULT 0,
        discount_total DECIMAL(12,2) DEFAULT 0,
        tax_total DECIMAL(12,2) DEFAULT 0,
        shipping_total DECIMAL(12,2) DEFAULT 0,
        total DECIMAL(12,2) DEFAULT 0,
        coupon_codes TEXT,
        status VARCHAR(20) DEFAULT 'active',
        expires_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_cart_key (cart_key),
        KEY idx_user_id (user_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_cart_items (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        cart_id BIGINT(20) NOT NULL,
        product_id BIGINT(20) NOT NULL,
        variant_id BIGINT(20) DEFAULT 0,
        vendor_id BIGINT(20) DEFAULT 0,
        quantity INT NOT NULL DEFAULT 1,
        unit_price DECIMAL(12,2) NOT NULL,
        subtotal DECIMAL(12,2) NOT NULL,
        discount DECIMAL(12,2) DEFAULT 0,
        tax DECIMAL(12,2) DEFAULT 0,
        total DECIMAL(12,2) NOT NULL,
        attributes TEXT,
        referrer_id BIGINT(20) DEFAULT 0,
        content_id BIGINT(20) DEFAULT 0,
        content_type VARCHAR(50) DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_cart_id (cart_id),
        KEY idx_product_id (product_id),
        KEY idx_vendor_id (vendor_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_orders (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        order_number VARCHAR(50) NOT NULL,
        user_id BIGINT(20) DEFAULT 0,
        customer_email VARCHAR(100) NOT NULL,
        customer_name VARCHAR(100) DEFAULT '',
        customer_phone VARCHAR(50) DEFAULT '',
        status VARCHAR(20) DEFAULT 'pending',
        currency VARCHAR(10) DEFAULT 'USD',
        subtotal DECIMAL(12,2) DEFAULT 0,
        discount_total DECIMAL(12,2) DEFAULT 0,
        tax_total DECIMAL(12,2) DEFAULT 0,
        shipping_total DECIMAL(12,2) DEFAULT 0,
        total DECIMAL(12,2) NOT NULL,
        paid_total DECIMAL(12,2) DEFAULT 0,
        refunded_total DECIMAL(12,2) DEFAULT 0,
        payment_method VARCHAR(50) DEFAULT '',
        payment_status VARCHAR(20) DEFAULT 'pending',
        transaction_id VARCHAR(255) DEFAULT '',
        shipping_method VARCHAR(50) DEFAULT '',
        shipping_address TEXT,
        billing_address TEXT,
        customer_note TEXT,
        admin_note TEXT,
        referrer_id BIGINT(20) DEFAULT 0,
        content_id BIGINT(20) DEFAULT 0,
        content_type VARCHAR(50) DEFAULT '',
        traffic_source VARCHAR(50) DEFAULT 'direct',
        agent_id VARCHAR(100) DEFAULT '',
        station_id BIGINT(20) DEFAULT 0,
        ip_address VARCHAR(45) DEFAULT '',
        user_agent TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        paid_at DATETIME DEFAULT NULL,
        completed_at DATETIME DEFAULT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_order_number (order_number),
        KEY idx_user_id (user_id),
        KEY idx_status (status),
        KEY idx_payment_status (payment_status),
        KEY idx_created_at (created_at),
        KEY idx_station_id (station_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_order_items (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        order_id BIGINT(20) NOT NULL,
        product_id BIGINT(20) NOT NULL,
        variant_id BIGINT(20) DEFAULT 0,
        vendor_id BIGINT(20) DEFAULT 0,
        product_name VARCHAR(255) NOT NULL,
        product_sku VARCHAR(100) DEFAULT '',
        quantity INT NOT NULL DEFAULT 1,
        unit_price DECIMAL(12,2) NOT NULL,
        subtotal DECIMAL(12,2) NOT NULL,
        discount DECIMAL(12,2) DEFAULT 0,
        tax DECIMAL(12,2) DEFAULT 0,
        total DECIMAL(12,2) NOT NULL,
        commission_rate DECIMAL(5,4) DEFAULT 0,
        commission_amount DECIMAL(12,2) DEFAULT 0,
        vendor_earning DECIMAL(12,2) DEFAULT 0,
        platform_earning DECIMAL(12,2) DEFAULT 0,
        referrer_earning DECIMAL(12,2) DEFAULT 0,
        attributes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_order_id (order_id),
        KEY idx_product_id (product_id),
        KEY idx_vendor_id (vendor_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_order_status_history (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        order_id BIGINT(20) NOT NULL,
        from_status VARCHAR(20) DEFAULT '',
        to_status VARCHAR(20) NOT NULL,
        note TEXT,
        changed_by BIGINT(20) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_order_id (order_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_payments (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        order_id BIGINT(20) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        gateway VARCHAR(50) NOT NULL,
        transaction_id VARCHAR(255) DEFAULT '',
        amount DECIMAL(12,2) NOT NULL,
        currency VARCHAR(10) DEFAULT 'USD',
        status VARCHAR(20) DEFAULT 'pending',
        gateway_response TEXT,
        paid_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_order_id (order_id),
        KEY idx_transaction_id (transaction_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_refunds (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        order_id BIGINT(20) NOT NULL,
        payment_id BIGINT(20) DEFAULT 0,
        amount DECIMAL(12,2) NOT NULL,
        reason TEXT,
        status VARCHAR(20) DEFAULT 'pending',
        refunded_by BIGINT(20) DEFAULT 0,
        gateway_refund_id VARCHAR(255) DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_order_id (order_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_inventory_logs (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) NOT NULL,
        variant_id BIGINT(20) DEFAULT 0,
        change_type VARCHAR(30) NOT NULL,
        quantity_change INT NOT NULL,
        quantity_before INT NOT NULL,
        quantity_after INT NOT NULL,
        reference_id BIGINT(20) DEFAULT 0,
        note VARCHAR(255) DEFAULT '',
        created_by BIGINT(20) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_product_id (product_id),
        KEY idx_change_type (change_type),
        KEY idx_created_at (created_at)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_tax_rates (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        country VARCHAR(5) NOT NULL,
        state VARCHAR(10) DEFAULT '',
        city VARCHAR(100) DEFAULT '',
        postcode VARCHAR(20) DEFAULT '',
        tax_class VARCHAR(50) DEFAULT '',
        rate DECIMAL(5,4) NOT NULL,
        tax_name VARCHAR(50) DEFAULT '',
        priority INT DEFAULT 0,
        is_compound TINYINT(1) DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_location (country, state, city, postcode),
        KEY idx_tax_class (tax_class)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_shipping_zones (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        zone_name VARCHAR(100) NOT NULL,
        regions TEXT NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        PRIMARY KEY  (id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_shipping_methods (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        zone_id BIGINT(20) NOT NULL,
        method_type VARCHAR(50) NOT NULL,
        method_name VARCHAR(100) NOT NULL,
        cost DECIMAL(12,2) DEFAULT 0,
        min_amount DECIMAL(12,2) DEFAULT 0,
        max_amount DECIMAL(12,2) DEFAULT 0,
        weight_rate DECIMAL(10,4) DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        PRIMARY KEY  (id),
        KEY idx_zone_id (zone_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_coupons (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        code VARCHAR(50) NOT NULL,
        discount_type VARCHAR(20) NOT NULL,
        discount_value DECIMAL(12,2) NOT NULL,
        min_order_amount DECIMAL(12,2) DEFAULT 0,
        max_discount DECIMAL(12,2) DEFAULT 0,
        usage_limit INT DEFAULT 0,
        usage_limit_per_user INT DEFAULT 0,
        used_count INT DEFAULT 0,
        vendor_id BIGINT(20) DEFAULT 0,
        station_id BIGINT(20) DEFAULT 0,
        applicable_products TEXT,
        applicable_categories TEXT,
        excluded_products TEXT,
        start_date DATETIME DEFAULT NULL,
        end_date DATETIME DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_code (code),
        KEY idx_vendor_id (vendor_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_coupon_usages (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        coupon_id BIGINT(20) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        order_id BIGINT(20) NOT NULL,
        discount_amount DECIMAL(12,2) NOT NULL,
        used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_coupon_id (coupon_id),
        KEY idx_user_id (user_id),
        KEY idx_order_id (order_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_currencies (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        code VARCHAR(10) NOT NULL,
        name VARCHAR(100) NOT NULL,
        symbol VARCHAR(10) NOT NULL,
        exchange_rate DECIMAL(15,6) DEFAULT 1,
        decimal_places INT DEFAULT 2,
        is_default TINYINT(1) DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_code (code)
    ) {$charset_collate};" );

    // ─── 4.2.2 配置类表（8张）─────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_member_levels (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        level_name VARCHAR(100) NOT NULL,
        level_slug VARCHAR(100) NOT NULL,
        level_order INT NOT NULL DEFAULT 0,
        required_spent DECIMAL(12,2) DEFAULT 0,
        required_points INT DEFAULT 0,
        discount_rate DECIMAL(5,2) DEFAULT 0,
        points_multiplier DECIMAL(3,2) DEFAULT 1.00,
        benefits TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_level_slug (level_slug)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_config_registry (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        config_key VARCHAR(100) NOT NULL,
        config_group VARCHAR(50) NOT NULL,
        config_type VARCHAR(20) NOT NULL,
        default_value TEXT,
        allowed_values TEXT,
        owner_type VARCHAR(20) NOT NULL,
        owner_id BIGINT(20) DEFAULT 0,
        config_value TEXT,
        is_locked TINYINT(1) DEFAULT 0,
        capability_required VARCHAR(100) DEFAULT '',
        description TEXT,
        sort_order INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_config_owner (config_key, owner_type, owner_id),
        KEY idx_config_group (config_group)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_config_permissions (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        role VARCHAR(50) NOT NULL,
        config_group VARCHAR(50) NOT NULL,
        can_view TINYINT(1) DEFAULT 1,
        can_edit TINYINT(1) DEFAULT 0,
        can_lock TINYINT(1) DEFAULT 0,
        scope VARCHAR(20) DEFAULT 'own',
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_role_group (role, config_group)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_config_audit_log (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        config_key VARCHAR(100) NOT NULL,
        owner_type VARCHAR(20) NOT NULL,
        owner_id BIGINT(20) NOT NULL,
        old_value TEXT,
        new_value TEXT,
        changed_by BIGINT(20) NOT NULL,
        changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        ip_address VARCHAR(45) DEFAULT '',
        PRIMARY KEY  (id),
        KEY config_lookup (config_key, owner_type, owner_id),
        KEY changed_by (changed_by)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_field_definitions (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        field_key VARCHAR(100) NOT NULL,
        field_label VARCHAR(200) NOT NULL,
        field_type VARCHAR(30) NOT NULL,
        object_type VARCHAR(50) NOT NULL,
        object_subtype VARCHAR(50) DEFAULT '',
        field_group VARCHAR(50) DEFAULT '',
        default_value TEXT,
        options TEXT,
        validation_rules TEXT,
        is_required TINYINT(1) DEFAULT 0,
        is_searchable TINYINT(1) DEFAULT 0,
        is_syncable TINYINT(1) DEFAULT 1,
        sync_mode VARCHAR(20) DEFAULT 'full',
        capability_required VARCHAR(100) DEFAULT '',
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY field_object (field_key, object_type, object_subtype),
        KEY object_lookup (object_type, object_subtype)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_notification_rules (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        rule_key VARCHAR(100) NOT NULL,
        event_type VARCHAR(50) NOT NULL,
        channel VARCHAR(20) NOT NULL,
        template_key VARCHAR(100) DEFAULT '',
        recipient_type VARCHAR(20) DEFAULT 'user',
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_rule_key (rule_key)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_email_templates (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        template_key VARCHAR(100) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        body LONGTEXT NOT NULL,
        locale VARCHAR(10) DEFAULT 'zh_CN',
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_template_locale (template_key, locale)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_schema_mappings (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        object_type VARCHAR(50) NOT NULL,
        object_subtype VARCHAR(50) DEFAULT '',
        schema_type VARCHAR(50) NOT NULL,
        schema_template TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_object (object_type, object_subtype)
    ) {$charset_collate};" );

    // ─── 4.2.3 记录类表（17张）─────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_points_log (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        points INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        reference_id BIGINT(20) DEFAULT 0,
        description VARCHAR(255) DEFAULT '',
        expires_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_user_id (user_id),
        KEY idx_action (action),
        KEY idx_expires_at (expires_at)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_points_rules (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        rule_key VARCHAR(100) NOT NULL,
        action VARCHAR(50) NOT NULL,
        points INT NOT NULL DEFAULT 0,
        multiplier DECIMAL(3,2) DEFAULT 1.00,
        max_points INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_rule_key (rule_key)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_vendor_earnings (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        vendor_id BIGINT(20) NOT NULL,
        order_id BIGINT(20) NOT NULL,
        gross DECIMAL(12,2) NOT NULL,
        commission DECIMAL(12,2) NOT NULL,
        net DECIMAL(12,2) NOT NULL,
        type VARCHAR(20) DEFAULT 'product',
        status VARCHAR(20) DEFAULT 'pending',
        blog_id BIGINT(20) DEFAULT 0,
        settled_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_vendor_id (vendor_id),
        KEY idx_order_id (order_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_withdrawals (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        method VARCHAR(50) NOT NULL,
        account VARCHAR(255) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        reviewed_by BIGINT(20) DEFAULT 0,
        reviewed_at DATETIME DEFAULT NULL,
        paid_at DATETIME DEFAULT NULL,
        remark TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_user_id (user_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_subscriptions (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        plan_id BIGINT(20) NOT NULL,
        status VARCHAR(20) DEFAULT 'active',
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        gateway VARCHAR(50) DEFAULT 'stripe',
        gateway_sub_id VARCHAR(255) DEFAULT '',
        auto_renew TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_user_id (user_id),
        KEY idx_plan_id (plan_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_subscription_plans (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        plan_name VARCHAR(100) NOT NULL,
        plan_slug VARCHAR(100) NOT NULL,
        price DECIMAL(12,2) NOT NULL,
        currency VARCHAR(10) DEFAULT 'USD',
        interval_type VARCHAR(20) DEFAULT 'month',
        interval_count INT DEFAULT 1,
        trial_days INT DEFAULT 0,
        features TEXT,
        gateway_price_id VARCHAR(255) DEFAULT '',
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_plan_slug (plan_slug)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_tips (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        from_user_id BIGINT(20) NOT NULL,
        to_user_id BIGINT(20) NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        currency VARCHAR(10) DEFAULT 'USD',
        message TEXT,
        order_id BIGINT(20) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_from_user (from_user_id),
        KEY idx_to_user (to_user_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_crowdfunding (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        target_amount DECIMAL(12,2) NOT NULL,
        raised_amount DECIMAL(12,2) DEFAULT 0,
        currency VARCHAR(10) DEFAULT 'USD',
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        status VARCHAR(20) DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_live_sessions (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        mux_live_id VARCHAR(255) DEFAULT '',
        stream_key VARCHAR(255) DEFAULT '',
        playback_id VARCHAR(255) DEFAULT '',
        status VARCHAR(20) DEFAULT 'scheduled',
        started_at DATETIME DEFAULT NULL,
        ended_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_campaigns (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        campaign_type VARCHAR(50) NOT NULL,
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        enrollment_limit INT DEFAULT 0,
        enrolled_count INT DEFAULT 0,
        status VARCHAR(20) DEFAULT 'draft',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_checkins (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        campaign_id BIGINT(20) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        checkin_date DATE NOT NULL,
        content TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_campaign_user_date (campaign_id, user_id, checkin_date),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_consultations (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) DEFAULT 0,
        user_id BIGINT(20) NOT NULL,
        expert_id BIGINT(20) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        content TEXT,
        status VARCHAR(20) DEFAULT 'pending',
        scheduled_at DATETIME DEFAULT NULL,
        completed_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_user_id (user_id),
        KEY idx_expert_id (expert_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_exams (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        exam_name VARCHAR(255) NOT NULL,
        questions LONGTEXT,
        pass_score INT DEFAULT 60,
        duration_minutes INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_exam_records (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        exam_id BIGINT(20) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        score INT DEFAULT 0,
        answers LONGTEXT,
        passed TINYINT(1) DEFAULT 0,
        started_at DATETIME NOT NULL,
        completed_at DATETIME DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY idx_exam_id (exam_id),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_events (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        event_name VARCHAR(255) NOT NULL,
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        venue VARCHAR(500) DEFAULT '',
        capacity INT DEFAULT 0,
        registered_count INT DEFAULT 0,
        price DECIMAL(12,2) DEFAULT 0,
        status VARCHAR(20) DEFAULT 'draft',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_event_registrations (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        event_id BIGINT(20) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        ticket_type VARCHAR(50) DEFAULT 'general',
        status VARCHAR(20) DEFAULT 'confirmed',
        registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_event_user (event_id, user_id),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_api_keys (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        key_name VARCHAR(100) NOT NULL,
        api_key_hash VARCHAR(255) NOT NULL,
        permissions TEXT,
        last_used_at DATETIME DEFAULT NULL,
        expires_at DATETIME DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_api_key_hash (api_key_hash),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    // ─── 4.2.4 合集系统表（4张）────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_collections (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        collection_type VARCHAR(50) NOT NULL,
        description LONGTEXT,
        cover_image_id BIGINT(20) DEFAULT 0,
        author_id BIGINT(20) NOT NULL,
        vendor_id BIGINT(20) DEFAULT 0,
        station_id BIGINT(20) DEFAULT 0,
        price DECIMAL(12,2) DEFAULT 0,
        member_price DECIMAL(12,2) DEFAULT 0,
        points_cost INT DEFAULT 0,
        is_free TINYINT(1) DEFAULT 0,
        member_only TINYINT(1) DEFAULT 0,
        total_items INT DEFAULT 0,
        free_items INT DEFAULT 0,
        status VARCHAR(20) DEFAULT 'draft',
        published_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_slug (slug),
        KEY idx_author_id (author_id),
        KEY idx_collection_type (collection_type),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_collection_items (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        collection_id BIGINT(20) NOT NULL,
        object_id BIGINT(20) NOT NULL,
        object_type VARCHAR(50) NOT NULL,
        item_order INT NOT NULL DEFAULT 0,
        is_free_preview TINYINT(1) DEFAULT 0,
        title_override VARCHAR(255) DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_collection_object (collection_id, object_id, object_type),
        KEY idx_collection_id (collection_id),
        KEY idx_object_lookup (object_id, object_type)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_collection_access (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        collection_id BIGINT(20) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        access_type VARCHAR(20) NOT NULL,
        order_id BIGINT(20) DEFAULT 0,
        granted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        expires_at DATETIME DEFAULT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_collection_user (collection_id, user_id),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_collection_progress (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        collection_id BIGINT(20) NOT NULL,
        items_read INT DEFAULT 0,
        last_item_id BIGINT(20) DEFAULT 0,
        progress DECIMAL(5,2) DEFAULT 0,
        started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_user_collection (user_id, collection_id),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    // ─── 4.2.5 关系类表（7张）──────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_content_product (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        content_id BIGINT(20) NOT NULL,
        content_type VARCHAR(50) NOT NULL,
        product_id BIGINT(20) NOT NULL,
        position VARCHAR(50) DEFAULT 'inline',
        sort_order INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_content (content_id, content_type),
        KEY idx_product_id (product_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_digital_assets (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        object_id BIGINT(20) NOT NULL,
        object_type VARCHAR(50) NOT NULL,
        asset_type VARCHAR(50) NOT NULL,
        file_path VARCHAR(500) DEFAULT '',
        file_size BIGINT(20) DEFAULT 0,
        download_limit INT DEFAULT 0,
        expiry_days INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_object (object_id, object_type)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_learning_progress (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        object_id BIGINT(20) NOT NULL,
        object_type VARCHAR(50) NOT NULL,
        progress DECIMAL(5,2) DEFAULT 0,
        last_position INT DEFAULT 0,
        started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_user_object (user_id, object_id, object_type),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_user_follows (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        follower_id BIGINT(20) NOT NULL,
        followed_id BIGINT(20) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_follow (follower_id, followed_id),
        KEY idx_followed_id (followed_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_group_members (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        group_id BIGINT(20) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        role VARCHAR(20) DEFAULT 'member',
        joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_group_user (group_id, user_id),
        KEY idx_user_id (user_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_social_interactions (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        object_id BIGINT(20) NOT NULL,
        object_type VARCHAR(50) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        interaction_type VARCHAR(20) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_user_object_type (user_id, object_id, object_type, interaction_type),
        KEY idx_object (object_id, object_type)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_knowledge_relations (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        source_id BIGINT(20) NOT NULL,
        source_type VARCHAR(50) NOT NULL,
        target_id BIGINT(20) NOT NULL,
        target_type VARCHAR(50) NOT NULL,
        relation_type VARCHAR(50) NOT NULL,
        weight DECIMAL(5,2) DEFAULT 1.00,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_source (source_id, source_type),
        KEY idx_target (target_id, target_type)
    ) {$charset_collate};" );

    // ─── 4.2.6 多站点与同步类表（3张）───────────────────────

    dbDelta( "CREATE TABLE {$wpdb->base_prefix}slv_city_config (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        blog_id BIGINT(20) NOT NULL,
        parent_region_id BIGINT(20) DEFAULT 0,
        city_name VARCHAR(100) NOT NULL,
        city_slug VARCHAR(100) NOT NULL,
        country_code VARCHAR(5) NOT NULL,
        language VARCHAR(10) NOT NULL,
        currency VARCHAR(10) NOT NULL,
        timezone VARCHAR(50) DEFAULT '',
        geo_lat DECIMAL(10,7) DEFAULT 0,
        geo_lng DECIMAL(10,7) DEFAULT 0,
        poi_source VARCHAR(50) DEFAULT '',
        station_master_id BIGINT(20) DEFAULT 0,
        commission_rate DECIMAL(5,2) DEFAULT 20.00,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_blog_city (blog_id),
        KEY idx_parent_region (parent_region_id),
        KEY idx_country_code (country_code),
        KEY idx_language (language)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_field_sync_rules (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        field_key VARCHAR(100) NOT NULL,
        sync_type VARCHAR(20) DEFAULT 'full',
        sync_mode VARCHAR(20) DEFAULT 'state',
        seo_behavior VARCHAR(50) DEFAULT '',
        owner_type VARCHAR(20) DEFAULT 'platform',
        owner_id BIGINT(20) DEFAULT 0,
        priority INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_field_owner (field_key, owner_type, owner_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_sync_queue (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        source_blog_id BIGINT(20) NOT NULL,
        target_blog_id BIGINT(20) NOT NULL,
        post_id BIGINT(20) NOT NULL,
        sync_mode VARCHAR(20) NOT NULL,
        payload TEXT,
        status VARCHAR(20) DEFAULT 'pending',
        priority INT DEFAULT 0,
        retry_count INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        processed_at DATETIME DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY idx_status_priority (status, priority),
        KEY idx_target_blog (target_blog_id)
    ) {$charset_collate};" );

    // ─── 4.2.7 BYOK配置类表（2张）─────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_service_configs (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        service_key VARCHAR(100) NOT NULL,
        owner_type VARCHAR(20) NOT NULL DEFAULT 'platform',
        owner_id BIGINT(20) DEFAULT 0,
        config_data TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_service_owner (service_key, owner_type, owner_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_service_logs (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        service_key VARCHAR(100) NOT NULL,
        action VARCHAR(50) NOT NULL,
        request_data TEXT,
        response_data TEXT,
        status VARCHAR(20) DEFAULT 'success',
        duration_ms INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_service_key (service_key),
        KEY idx_created_at (created_at)
    ) {$charset_collate};" );

    // ─── 4.2.8 统计类表（4张）──────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_post_stats (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        views BIGINT(20) DEFAULT 0,
        reads BIGINT(20) DEFAULT 0,
        time_total BIGINT(20) DEFAULT 0,
        p25 BIGINT(20) DEFAULT 0,
        p50 BIGINT(20) DEFAULT 0,
        p75 BIGINT(20) DEFAULT 0,
        p100 BIGINT(20) DEFAULT 0,
        avg_scroll DECIMAL(5,2) DEFAULT 0,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_post_id (post_id),
        KEY idx_views (views),
        KEY idx_reads (reads)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_post_daily (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        stat_date DATE NOT NULL,
        views BIGINT(20) DEFAULT 0,
        reads BIGINT(20) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_post_date (post_id, stat_date),
        KEY idx_stat_date (stat_date)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_track_log (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        event VARCHAR(20) NOT NULL,
        value INT DEFAULT 0,
        visitor_id VARCHAR(64) DEFAULT '',
        user_id BIGINT(20) DEFAULT 0,
        session_id VARCHAR(64) DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id),
        KEY idx_event (event),
        KEY idx_visitor_id (visitor_id),
        KEY idx_created_at (created_at)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_reading_position (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        post_id BIGINT(20) NOT NULL,
        scroll_position INT DEFAULT 0,
        scroll_percent DECIMAL(5,2) DEFAULT 0,
        last_read_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_user_post (user_id, post_id),
        KEY idx_user_id (user_id),
        KEY idx_post_id (post_id)
    ) {$charset_collate};" );

    // ─── 4.2.9 仓库类表（5张）──────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_product_warehouses (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        warehouse_type VARCHAR(30) NOT NULL,
        owner_type VARCHAR(20) NOT NULL,
        owner_id BIGINT(20) NOT NULL,
        warehouse_name VARCHAR(100) NOT NULL,
        warehouse_slug VARCHAR(100) NOT NULL,
        description TEXT,
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_owner_warehouse (owner_type, owner_id, warehouse_slug),
        KEY idx_warehouse_type (warehouse_type)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_warehouse_items (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        warehouse_id BIGINT(20) NOT NULL,
        product_id BIGINT(20) NOT NULL,
        source_type VARCHAR(30) NOT NULL,
        source_owner_id BIGINT(20) DEFAULT 0,
        status VARCHAR(20) DEFAULT 'pending',
        is_required TINYINT(1) DEFAULT 0,
        is_optional TINYINT(1) DEFAULT 1,
        commission_rate DECIMAL(5,2) DEFAULT 0,
        apply_reason TEXT,
        reviewed_by BIGINT(20) DEFAULT 0,
        reviewed_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_warehouse_product (warehouse_id, product_id),
        KEY idx_warehouse_id (warehouse_id),
        KEY idx_product_id (product_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_warehouse_applications (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        warehouse_id BIGINT(20) NOT NULL,
        applicant_id BIGINT(20) NOT NULL,
        product_id BIGINT(20) DEFAULT 0,
        application_type VARCHAR(30) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        reason TEXT,
        reviewed_by BIGINT(20) DEFAULT 0,
        reviewed_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_warehouse_id (warehouse_id),
        KEY idx_applicant_id (applicant_id),
        KEY idx_status (status)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_listing_rules (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        rule_name VARCHAR(100) NOT NULL,
        warehouse_id BIGINT(20) DEFAULT 0,
        rule_type VARCHAR(30) NOT NULL,
        rule_config TEXT,
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_warehouse_id (warehouse_id)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_store_profiles (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        store_type VARCHAR(20) NOT NULL,
        store_slug VARCHAR(100) NOT NULL,
        store_name VARCHAR(200) NOT NULL,
        store_logo VARCHAR(500) DEFAULT '',
        store_banner VARCHAR(500) DEFAULT '',
        store_description TEXT,
        store_category VARCHAR(100) DEFAULT '',
        contact_phone VARCHAR(50) DEFAULT '',
        contact_email VARCHAR(100) DEFAULT '',
        contact_wechat VARCHAR(100) DEFAULT '',
        address TEXT,
        geo_lat DECIMAL(10,7) DEFAULT 0,
        geo_lng DECIMAL(10,7) DEFAULT 0,
        business_hours VARCHAR(200) DEFAULT '',
        social_links TEXT,
        is_verified TINYINT(1) DEFAULT 0,
        is_featured TINYINT(1) DEFAULT 0,
        rating DECIMAL(3,2) DEFAULT 0,
        follower_count INT DEFAULT 0,
        product_count INT DEFAULT 0,
        sales_count INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_store_slug (store_slug),
        KEY idx_user_id (user_id),
        KEY idx_store_type (store_type)
    ) {$charset_collate};" );

    // ─── 订单号序列表 ──────────────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_order_sequence (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) {$charset_collate};" );

    // ─── SEO/GEO/AEO 表 ────────────────────────────────────

    dbDelta( "CREATE TABLE {$prefix}slv_seo_meta (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        meta_title VARCHAR(255) DEFAULT '',
        meta_description VARCHAR(500) DEFAULT '',
        meta_keywords VARCHAR(500) DEFAULT '',
        og_title VARCHAR(255) DEFAULT '',
        og_description VARCHAR(500) DEFAULT '',
        og_image VARCHAR(500) DEFAULT '',
        twitter_card VARCHAR(20) DEFAULT 'summary_large_image',
        twitter_title VARCHAR(255) DEFAULT '',
        twitter_description VARCHAR(500) DEFAULT '',
        canonical_url VARCHAR(500) DEFAULT '',
        robots VARCHAR(100) DEFAULT 'index,follow',
        priority DECIMAL(2,1) DEFAULT 0.5,
        changefreq VARCHAR(20) DEFAULT 'weekly',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_post_id (post_id),
        KEY idx_robots (robots)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_seo_404_log (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        url VARCHAR(500) NOT NULL,
        referer VARCHAR(500) DEFAULT '',
        user_agent TEXT,
        ip_address VARCHAR(45) DEFAULT '',
        hit_count INT DEFAULT 1,
        first_hit DATETIME DEFAULT CURRENT_TIMESTAMP,
        last_hit DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_url (url),
        KEY idx_hit_count (hit_count)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_seo_redirects (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        source_url VARCHAR(500) NOT NULL,
        target_url VARCHAR(500) NOT NULL,
        redirect_type INT DEFAULT 301,
        hit_count INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_source (source_url),
        KEY idx_is_active (is_active)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_aeo_faqs (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        question TEXT NOT NULL,
        answer LONGTEXT NOT NULL,
        answer_short VARCHAR(500) DEFAULT '',
        sort_order INT DEFAULT 0,
        is_featured TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id),
        KEY idx_sort_order (sort_order)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_geo_crawlers (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        crawler_name VARCHAR(50) NOT NULL,
        user_agent TEXT,
        url VARCHAR(500) NOT NULL,
        ip_address VARCHAR(45) DEFAULT '',
        hit_count INT DEFAULT 1,
        first_hit DATETIME DEFAULT CURRENT_TIMESTAMP,
        last_hit DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_crawler_url (crawler_name, url),
        KEY idx_crawler_name (crawler_name),
        KEY idx_last_hit (last_hit)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_geo_citations (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        engine VARCHAR(50) NOT NULL,
        query TEXT,
        citation_url VARCHAR(500) DEFAULT '',
        citation_context TEXT,
        detected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY idx_post_id (post_id),
        KEY idx_engine (engine),
        KEY idx_detected_at (detected_at)
    ) {$charset_collate};" );

    dbDelta( "CREATE TABLE {$prefix}slv_geo_scores (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) NOT NULL,
        structure_score INT DEFAULT 0,
        entity_score INT DEFAULT 0,
        fact_score INT DEFAULT 0,
        schema_score INT DEFAULT 0,
        freshness_score INT DEFAULT 0,
        overall_score INT DEFAULT 0,
        suggestions TEXT,
        scored_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY uniq_post_id (post_id),
        KEY idx_overall_score (overall_score)
    ) {$charset_collate};" );

    update_option( 'slv_db_version', SLV_DB_VERSION );
}

/**
 * 仅在数据库版本变化时执行 DDL。
 *
 * @since 1.0.0
 */
function slv_maybe_create_tables(): void {
    if ( get_option( 'slv_db_version' ) !== SLV_DB_VERSION ) {
        slv_create_tables();
    }
}

add_action( 'after_setup_theme', 'slv_maybe_create_tables' );