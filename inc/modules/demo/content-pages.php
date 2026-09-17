<?php
/**
 * SunLyvo Nexus — 演示页面内容
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 返回演示页面列表。
 *
 * @since 1.0.0
 * @return array
 */
function slv_demo_pages(): array {
    return [
        [
            'demo_key' => 'page-about',
            'title'    => '关于我们',
            'slug'     => 'about',
            'seo'      => [
                'title'       => '关于我们 - SunLyvo Nexus',
                'description' => 'SunLyvo Nexus 是面向全球市场的内容电商 + 知识付费 + 多商户 + B2B + AI 代理商务一体化平台。',
                'keywords'    => '关于我们,SunLyvo Nexus',
            ],
            'content'  => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo Nexus</strong>是面向全球市场的内容电商 + 知识付费 + 多商户 + B2B + AI 代理商务一体化平台。核心公式是「内容即货架，用户即渠道，AI 即入口」。</p>

<h2>我们的使命</h2>
<p>让每一个内容创作者都能零成本带货，让每一个商户都能获得精准流量，让每一个用户都能从可信内容中完成购买。</p>

<h2>我们的定位</h2>
<p>SunLyvo Nexus 不是 Amazon，不是 Shopify，不是 Udemy，不是知识星球。它是一个融合内容、电商、知识付费、多商户、B2B、AI 代理的一体化平台。</p>

<h2>技术特色</h2>
<ul>
<li><strong>Hybrid Theme</strong>：区块模板 + 经典 PHP 模板混合</li>
<li><strong>设计 Token 五层架构</strong>：Seed → Map → Alias → Component → State</li>
<li><strong>SEO/GEO/AEO 三层流量引擎</strong>：覆盖搜索、AI、答案全入口</li>
<li><strong>阅读体验子系统</strong>：TOC / 复制 / 引用 / 分享做到极致</li>
<li><strong>双市场支持</strong>：国际 + 国内双栈</li>
</ul>

<h2>联系我们</h2>
<p>开发者：李咏燊</p>
<p>微信：getthink-info</p>
HTML,
        ],

        [
            'demo_key' => 'page-contact',
            'title'    => '联系我们',
            'slug'     => 'contact',
            'seo'      => [
                'title'       => '联系我们 - SunLyvo Nexus',
                'description' => '通过微信 getthink-info 联系 SunLyvo Nexus 团队。',
                'keywords'    => '联系我们,联系方式',
            ],
            'content'  => <<<'HTML'
<p class="slv-bluf-summary">通过以下方式联系 <strong>SunLyvo Nexus</strong> 团队：</p>

<h2>开发者</h2>
<ul>
<li><strong>姓名</strong>：李咏燊</li>
<li><strong>微信</strong>：getthink-info</li>
<li><strong>项目</strong>：SunLyvo Nexus</li>
</ul>

<h2>商务合作</h2>
<p>商户入驻、B2B 采购、城市分站加盟、AI 代理接入，请通过微信联系。</p>

<h2>技术支持</h2>
<p>安装、配置、开发问题，请通过微信联系并说明具体场景。</p>

<h2>办公地址</h2>
<p>中国 · 郑州</p>
HTML,
        ],

        [
            'demo_key' => 'page-privacy',
            'title'    => '隐私政策',
            'slug'     => 'privacy',
            'seo'      => [
                'title'       => '隐私政策 - SunLyvo Nexus',
                'description' => 'SunLyvo Nexus 隐私政策，说明我们如何收集、使用、保护您的个人信息。',
                'keywords'    => '隐私政策,个人信息保护',
            ],
            'content'  => <<<'HTML'
<p class="slv-bluf-summary">本隐私政策说明 <strong>SunLyvo Nexus</strong> 如何收集、使用、保护您的个人信息。使用本平台即表示您同意本政策。</p>

<h2>我们收集的信息</h2>
<ul>
<li>账户信息：邮箱、用户名、密码（加密存储）</li>
<li>交易信息：订单、支付、收货地址</li>
<li>使用信息：浏览、搜索、点击行为</li>
<li>设备信息：IP、浏览器、操作系统</li>
</ul>

<h2>我们如何使用信息</h2>
<ul>
<li>提供、维护、改进平台服务</li>
<li>处理订单、支付、退款</li>
<li>推荐相关内容与商品</li>
<li>防止欺诈、保障安全</li>
</ul>

<h2>我们如何保护信息</h2>
<ul>
<li>所有 API Key 使用 AES-256-CBC 加密</li>
<li>所有密码使用 bcrypt 加密</li>
<li>所有敏感操作经过 nonce 与权限校验</li>
<li>所有数据传输使用 HTTPS</li>
</ul>

<h2>您的权利</h2>
<ul>
<li>访问、修改、删除您的个人信息</li>
<li>导出您的数据</li>
<li>撤回同意</li>
<li>投诉举报</li>
</ul>

<h2>联系我们</h2>
<p>如有隐私相关问题，请通过微信 getthink-info 联系。</p>
HTML,
        ],

        [
            'demo_key' => 'page-terms',
            'title'    => '服务条款',
            'slug'     => 'terms',
            'seo'      => [
                'title'       => '服务条款 - SunLyvo Nexus',
                'description' => 'SunLyvo Nexus 服务条款，说明使用本平台的权利与义务。',
                'keywords'    => '服务条款,用户协议',
            ],
            'content'  => <<<'HTML'
<p class="slv-bluf-summary">本服务条款规定您使用 <strong>SunLyvo Nexus</strong> 平台的权利与义务。使用本平台即表示您同意本条款。</p>

<h2>账户</h2>
<ul>
<li>您需对账户安全负责</li>
<li>禁止转让、出租账户</li>
<li>禁止使用虚假信息注册</li>
</ul>

<h2>内容</h2>
<ul>
<li>您对发布的内容负责</li>
<li>禁止发布违法、侵权内容</li>
<li>平台有权审核、删除违规内容</li>
</ul>

<h2>交易</h2>
<ul>
<li>订单成立以支付成功为准</li>
<li>退款按平台规则处理</li>
<li>禁止刷单、虚假交易</li>
</ul>

<h2>知识产权</h2>
<ul>
<li>平台代码、设计、商标归平台所有</li>
<li>用户内容版权归用户所有</li>
<li>禁止未经授权使用平台内容</li>
</ul>

<h2>免责</h2>
<ul>
<li>不可抗力导致的服务中断，平台不承担责任</li>
<li>第三方服务故障，平台不承担责任</li>
<li>用户自身原因导致的损失，平台不承担责任</li>
</ul>
HTML,
        ],

        [
            'demo_key' => 'page-404',
            'title'    => '页面未找到',
            'slug'     => '404',
            'seo'      => [
                'title'       => '页面未找到 - SunLyvo Nexus',
                'description' => '您访问的页面不存在或已被移除。',
                'keywords'    => '404,页面未找到',
            ],
            'content'  => <<<'HTML'
<p class="slv-bluf-summary">您访问的页面不存在或已被移除。</p>

<h2>可能的原因</h2>
<ul>
<li>URL 输入错误</li>
<li>页面已被删除</li>
<li>页面已更名</li>
</ul>

<h2>您可以</h2>
<ul>
<li>返回<a href="/">首页</a></li>
<li>查看<a href="/blog/">博客</a></li>
<li>查看<a href="/products/">商品</a></li>
<li>使用搜索功能</li>
</ul>
HTML,
        ],
    ];
}