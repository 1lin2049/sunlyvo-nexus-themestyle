<?php
/**
 * SunLyvo Nexus — 演示商品内容
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 返回演示商品列表。
 *
 * @since 1.0.0
 * @return array
 */
function slv_demo_products(): array {
    return [
        [
            'demo_key'    => 'product-01',
            'title'       => 'SunLyvo Nexus 内容电商引擎',
            'slug'        => 'sunlyvo-nexus-engine',
            'excerpt'     => '开箱即用的内容电商解决方案，含 65+ 张数据表、18+ 内容类型、SEO/GEO/AEO 三层流量引擎、阅读体验子系统。',
            'product_cat' => [ '软件产品' ],
            'tags'        => [ '内容电商', '电商引擎', 'WordPress' ],
            'product'     => [
                '_slv_sku'             => 'SLV-ENGINE-001',
                '_slv_price'           => '999.00',
                '_slv_wholesale_price' => '799.00',
                '_slv_moq'             => '1',
                '_slv_hs_code'         => '8523.49.00',
                '_slv_lead_time'       => '3 个工作日',
            ],
            'seo'         => [
                'title'       => 'SunLyvo Nexus 内容电商引擎 - SunLyvo Nexus',
                'description' => '开箱即用的内容电商解决方案，含 65+ 张数据表、18+ 内容类型、SEO/GEO/AEO 三层流量引擎、阅读体验子系统。',
                'keywords'    => '内容电商引擎,电商解决方案,WordPress 电商',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo Nexus 内容电商引擎</strong>是开箱即用的内容电商解决方案，包含 65+ 张数据表、18+ 内容类型、SEO/GEO/AEO 三层流量引擎、阅读体验子系统。安装后即刻呈现完整站点结构与示例内容。</p>

<h2>核心功能</h2>
<ul>
<li><strong>18 张电商引擎表</strong>：商品、购物车、订单、支付、库存、税务、运费、优惠券、多币种</li>
<li><strong>18+ 内容类型</strong>：博客、百科、FAQ、视频、文档、合集、商品</li>
<li><strong>SEO/GEO/AEO 三层流量引擎</strong>：结构化数据、llms.txt、AI 爬虫管理、Snippet 优化</li>
<li><strong>阅读体验子系统</strong>：TOC 自动生成、复制、引用、分享、阅读位置记忆</li>
<li><strong>双市场支持</strong>：Stripe / PayPal / 微信支付 / 支付宝</li>
</ul>

<h2>技术规格</h2>
<table>
<thead><tr><th>项</th><th>规格</th></tr></thead>
<tbody>
<tr><td>技术栈</td><td>WordPress Multisite + 自研电商引擎 + React 19 + Ant Design 6</td></tr>
<tr><td>PHP 版本</td><td>8.2+</td></tr>
<tr><td>数据库表</td><td>65+ 张</td></tr>
<tr><td>内容类型</td><td>18+ CPT</td></tr>
<tr><td>用户角色</td><td>12+</td></tr>
<tr><td>许可证</td><td>GPL v2 or later</td></tr>
</tbody>
</table>

<h2>包含内容</h2>
<ol>
<li>完整主题代码（Hybrid Theme）</li>
<li>65+ 张数据表创建脚本</li>
<li>18+ CPT 注册代码</li>
<li>SEO/GEO/AEO 模块</li>
<li>阅读体验子系统</li>
<li>Demo 内容种子脚本（25 个页面）</li>
<li>安装/使用/开发/API 文档</li>
</ol>

<h2>相关阅读</h2>
<ul>
<li><a href="/blog/what-is-content-commerce/">什么是内容电商？</a></li>
<li><a href="/wiki/content-commerce/">内容电商</a></li>
<li><a href="/faq/how-to-build-content-commerce/">如何搭建内容电商平台？</a></li>
</ul>
HTML,
        ],

        [
            'demo_key'    => 'product-02',
            'title'       => 'SunLyvo GEO 优化套件',
            'slug'        => 'geo-optimization-kit',
            'excerpt'     => '自动生成 llms.txt、语义分块 Schema、AI 爬虫日志、GEO 五维评分，让内容被 ChatGPT 引用。',
            'product_cat' => [ '插件工具' ],
            'tags'        => [ 'GEO', 'AI引用', 'llms.txt' ],
            'product'     => [
                '_slv_sku'             => 'SLV-GEO-001',
                '_slv_price'           => '299.00',
                '_slv_wholesale_price' => '199.00',
                '_slv_moq'             => '1',
                '_slv_hs_code'         => '8523.49.00',
                '_slv_lead_time'       => '即时交付',
            ],
            'seo'         => [
                'title'       => 'SunLyvo GEO 优化套件 - SunLyvo Nexus',
                'description' => '自动生成 llms.txt、语义分块 Schema、AI 爬虫日志、GEO 五维评分，让内容被 ChatGPT 引用。',
                'keywords'    => 'GEO,llms.txt,AI引用,ChatGPT',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo GEO 优化套件</strong>自动生成 llms.txt、语义分块 Schema、AI 爬虫日志、GEO 五维评分。配置后内容被 ChatGPT 引用的概率提升 <strong>47%</strong>。</p>

<h2>核心功能</h2>
<ol>
<li><strong>llms.txt 自动生成</strong>：站点介绍 + 核心页面 + 各内容类型 + 结构化数据入口</li>
<li><strong>llms-full.txt 生成</strong>：包含完整正文，供 LLM 深度阅读</li>
<li><strong>语义分块 Schema</strong>：按 H2/H3 拆分内容，输出 ItemList</li>
<li><strong>AI 爬虫日志</strong>：记录 17 个 AI 爬虫的访问</li>
<li><strong>BLUF 结构检测</strong>：检测每个 H2 是否满足 40-60 字答案前置</li>
<li><strong>GEO 五维评分</strong>：结构、实体、事实、Schema、新鲜度</li>
</ol>

<h2>技术规格</h2>
<table>
<thead><tr><th>项</th><th>规格</th></tr></thead>
<tbody>
<tr><td>支持 AI 引擎</td><td>ChatGPT / Perplexity / Claude / Gemini / 文心 / 通义 / Kimi</td></tr>
<tr><td>llms.txt 生成时间</td><td>< 500ms</td></tr>
<tr><td>Schema 输出时间</td><td>< 50ms</td></tr>
<tr><td>爬虫日志写入</td><td>< 50ms</td></tr>
</tbody>
</table>
HTML,
        ],

        [
            'demo_key'    => 'product-03',
            'title'       => 'SunLyvo AEO 优化套件',
            'slug'        => 'aeo-optimization-kit',
            'excerpt'     => '自动生成 FAQPage / HowTo / QAPage / Speakable Schema，抢占 Google Featured Snippet。',
            'product_cat' => [ '插件工具' ],
            'tags'        => [ 'AEO', 'Featured Snippet', '语音搜索' ],
            'product'     => [
                '_slv_sku'             => 'SLV-AEO-001',
                '_slv_price'           => '299.00',
                '_slv_wholesale_price' => '199.00',
                '_slv_moq'             => '1',
                '_slv_hs_code'         => '8523.49.00',
                '_slv_lead_time'       => '即时交付',
            ],
            'seo'         => [
                'title'       => 'SunLyvo AEO 优化套件 - SunLyvo Nexus',
                'description' => '自动生成 FAQPage / HowTo / QAPage / Speakable Schema，抢占 Google Featured Snippet。',
                'keywords'    => 'AEO,Featured Snippet,语音搜索',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo AEO 优化套件</strong>自动生成 FAQPage / HowTo / QAPage / Speakable Schema，Snippet 结构检测，AEO 五维评分。配置后内容出现在 Featured Snippet 的概率提升 <strong>35%</strong>。</p>

<h2>核心功能</h2>
<ol>
<li><strong>FAQPage Schema</strong>：一页多个问答对</li>
<li><strong>HowTo Schema</strong>：自动从教程内容提取步骤</li>
<li><strong>QAPage Schema</strong>：一页一个问答</li>
<li><strong>Speakable Schema</strong>：语音搜索优化</li>
<li><strong>Snippet 结构检测</strong>：段落型 / 列表型 / 表格型</li>
<li><strong>PAA 问题生成</strong>：People Also Ask 问题预测</li>
<li><strong>AEO 五维评分</strong>：段落、列表、FAQ、Speakable</li>
</ol>
HTML,
        ],

        [
            'demo_key'    => 'product-04',
            'title'       => 'SunLyvo 阅读体验子系统',
            'slug'        => 'reader-experience-suite',
            'excerpt'     => 'TOC 自动生成、复制/引用/分享、阅读位置记忆，四大模块做到极致。',
            'product_cat' => [ '插件工具' ],
            'tags'        => [ '阅读体验', 'TOC', '用户体验' ],
            'product'     => [
                '_slv_sku'             => 'SLV-READER-001',
                '_slv_price'           => '199.00',
                '_slv_wholesale_price' => '129.00',
                '_slv_moq'             => '1',
                '_slv_hs_code'         => '8523.49.00',
                '_slv_lead_time'       => '即时交付',
            ],
            'seo'         => [
                'title'       => 'SunLyvo 阅读体验子系统 - SunLyvo Nexus',
                'description' => 'TOC 自动生成、复制/引用/分享、阅读位置记忆，四大模块做到极致。',
                'keywords'    => '阅读体验,TOC,复制,分享',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo 阅读体验子系统</strong>包含 TOC 自动生成、复制/引用/分享、阅读位置记忆、快捷键四大模块。TOC 满足十个极致细节，复制/引用/分享满足十项极致，阅读位置记忆满足七项极致。</p>

<h2>TOC 十项极致</h2>
<ol>
<li>多级 TOC（H2/H3/H4）</li>
<li>智能折叠</li>
<li>进度可视化</li>
<li>章节预估时间</li>
<li>移动端悬浮+抽屉</li>
<li>键盘导航（[ / ] / \）</li>
<li>位置持久化</li>
<li>锚点平滑滚动</li>
<li>URL 锚点同步</li>
<li>打印友好</li>
</ol>

<h2>复制/引用/分享十项极致</h2>
<ol>
<li>多格式复制（纯文本 / Markdown / HTML / 富文本）</li>
<li>带元数据引用</li>
<li>可配置分享平台</li>
<li>复制成功动效</li>
<li>二维码分享</li>
<li>短链接</li>
<li>分享统计</li>
<li>引用格式选择（APA / MLA / Chicago / GB7714）</li>
<li>选区智能识别</li>
<li>键盘快捷键（Ctrl/Cmd+Shift+C）</li>
</ol>
HTML,
        ],

        [
            'demo_key'    => 'product-05',
            'title'       => 'SunLyvo 多商户引擎',
            'slug'        => 'multi-vendor-engine',
            'excerpt'     => '多商户入驻、订单拆分、Stripe Connect 分账、收益结算、提现全流程。',
            'product_cat' => [ '软件产品' ],
            'tags'        => [ '多商户', '订单拆分', 'Stripe Connect' ],
            'product'     => [
                '_slv_sku'             => 'SLV-MV-001',
                '_slv_price'           => '1499.00',
                '_slv_wholesale_price' => '1199.00',
                '_slv_moq'             => '1',
                '_slv_hs_code'         => '8523.49.00',
                '_slv_lead_time'       => '5 个工作日',
            ],
            'seo'         => [
                'title'       => 'SunLyvo 多商户引擎 - SunLyvo Nexus',
                'description' => '多商户入驻、订单拆分、Stripe Connect 分账、收益结算、提现全流程。',
                'keywords'    => '多商户,订单拆分,Stripe Connect,分账',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo 多商户引擎</strong>提供多商户入驻、订单拆分、Stripe Connect 分账、收益结算、提现全流程。支持商户隔离、三层仓库选品、商户店铺独立页。</p>

<h2>核心功能</h2>
<ul>
<li><strong>商户入驻审批</strong>：提交资料 → 平台审核 → 开通账号</li>
<li><strong>商户隔离</strong>：商户只能管理自己的产品与订单</li>
<li><strong>订单拆分</strong>：按商户拆分订单，分别计算收益</li>
<li><strong>Stripe Connect 分账</strong>：Express 账户，订单完成后自动分账</li>
<li><strong>收益结算</strong>：T+7 天后可提现</li>
<li><strong>提现流程</strong>：申请 → 审核 → 打款</li>
<li><strong>店铺独立页</strong>：/store/{slug}/</li>
</ul>

<h2>技术规格</h2>
<table>
<thead><tr><th>项</th><th>规格</th></tr></thead>
<tbody>
<tr><td>商户隔离查询</td><td>< 100ms</td></tr>
<tr><td>订单拆分</td><td>< 500ms</td></tr>
<tr><td>收益记录</td><td>< 100ms</td></tr>
<tr><td>分账方式</td><td>Stripe Connect Express</td></tr>
<tr><td>结算周期</td><td>T+7</td></tr>
</tbody>
</table>
HTML,
        ],
    ];
}