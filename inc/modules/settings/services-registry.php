<?php
/**
 * SunLyvo Nexus — 服务注册表
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function slv_get_services_registry(): array {
    return [
        'stripe' => [
            'label'  => 'Stripe', 'group' => 'payment', 'region' => 'international',
            'icon'   => 'dashicons-money-alt', 'doc_url' => 'https://dashboard.stripe.com/apikeys',
            'fields' => [
                'secret_key'      => [ 'label' => 'Secret Key', 'type' => 'password', 'required' => true ],
                'publishable_key' => [ 'label' => 'Publishable Key', 'type' => 'text', 'required' => true ],
                'webhook_secret'  => [ 'label' => 'Webhook Secret', 'type' => 'password', 'required' => false ],
            ],
        ],
        'wechatpay' => [
            'label'  => '微信支付', 'group' => 'payment', 'region' => 'cn', 'icon' => 'dashicons-money-alt',
            'fields' => [
                'app_id'    => [ 'label' => 'App ID', 'type' => 'text', 'required' => true ],
                'mch_id'    => [ 'label' => '商户号', 'type' => 'text', 'required' => true ],
                'api_key'   => [ 'label' => 'API v3 Key', 'type' => 'password', 'required' => true ],
                'cert_path' => [ 'label' => '证书路径', 'type' => 'text', 'required' => false ],
            ],
        ],
        'alipay' => [
            'label'  => '支付宝', 'group' => 'payment', 'region' => 'cn', 'icon' => 'dashicons-money-alt',
            'fields' => [
                'app_id'      => [ 'label' => 'App ID', 'type' => 'text', 'required' => true ],
                'private_key' => [ 'label' => '应用私钥', 'type' => 'textarea', 'required' => true ],
                'public_key'  => [ 'label' => '支付宝公钥', 'type' => 'textarea', 'required' => true ],
            ],
        ],
        'sendgrid' => [
            'label'  => 'SendGrid', 'group' => 'email', 'region' => 'international', 'icon' => 'dashicons-email',
            'fields' => [
                'api_key'    => [ 'label' => 'API Key', 'type' => 'password', 'required' => true ],
                'from_email' => [ 'label' => '发件邮箱', 'type' => 'email', 'required' => true ],
                'from_name'  => [ 'label' => '发件人名称', 'type' => 'text', 'required' => false ],
            ],
        ],
        'deepl' => [
            'label'  => 'DeepL', 'group' => 'translate', 'region' => 'international', 'icon' => 'dashicons-translation',
            'fields' => [
                'api_key' => [ 'label' => 'API Key', 'type' => 'password', 'required' => true ],
            ],
        ],
        'mux' => [
            'label'  => 'Mux', 'group' => 'video', 'region' => 'international', 'icon' => 'dashicons-video-alt3',
            'fields' => [
                'token_id'     => [ 'label' => 'Token ID', 'type' => 'text', 'required' => true ],
                'token_secret' => [ 'label' => 'Token Secret', 'type' => 'password', 'required' => true ],
            ],
        ],
        'openai' => [
            'label'  => 'OpenAI', 'group' => 'ai', 'region' => 'international', 'icon' => 'dashicons-superhero',
            'fields' => [
                'api_key' => [ 'label' => 'API Key', 'type' => 'password', 'required' => true ],
                'model'   => [ 'label' => '默认模型', 'type' => 'text', 'required' => false ],
            ],
        ],
        'deepseek' => [
            'label'  => 'DeepSeek', 'group' => 'ai', 'region' => 'cn', 'icon' => 'dashicons-superhero',
            'fields' => [
                'api_key' => [ 'label' => 'API Key', 'type' => 'password', 'required' => true ],
            ],
        ],
        'aliyun_sms' => [
            'label'  => '阿里云短信', 'group' => 'sms', 'region' => 'cn', 'icon' => 'dashicons-smartphone',
            'fields' => [
                'access_key_id'     => [ 'label' => 'Access Key ID', 'type' => 'text', 'required' => true ],
                'access_key_secret' => [ 'label' => 'Access Key Secret', 'type' => 'password', 'required' => true ],
                'sign_name'         => [ 'label' => '短信签名', 'type' => 'text', 'required' => true ],
                'template_code'     => [ 'label' => '模板 Code', 'type' => 'text', 'required' => true ],
            ],
        ],
        'google_maps' => [
            'label'  => 'Google Maps', 'group' => 'map', 'region' => 'international', 'icon' => 'dashicons-location-alt',
            'fields' => [
                'api_key' => [ 'label' => 'API Key', 'type' => 'password', 'required' => true ],
            ],
        ],
        'cloudflare' => [
            'label'  => 'Cloudflare', 'group' => 'cdn', 'region' => 'international', 'icon' => 'dashicons-cloud',
            'fields' => [
                'api_key' => [ 'label' => 'API Token', 'type' => 'password', 'required' => true ],
                'zone_id' => [ 'label' => 'Zone ID', 'type' => 'text', 'required' => true ],
            ],
        ],
    ];
}

/**
 * 获取服务配置（供 API 客户端调用）。
 *
 * @since 1.0.0
 */
function slv_get_service_config( string $service_key ): ?array {
    static $cache = [];
    if ( isset( $cache[ $service_key ] ) ) {
        return $cache[ $service_key ];
    }
    $repo = new SLV_Service_Config_Repo();
    $config = $repo->get( $service_key, 'platform', 0 );
    $cache[ $service_key ] = $config;
    return $config;
}