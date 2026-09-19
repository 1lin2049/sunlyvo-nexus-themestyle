<?php
/**
 * SunLyvo Nexus — 演示 FAQ 内容
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 返回演示 FAQ 列表。
 *
 * @since 1.0.0
 * @return array
 */
function slv_demo_faq(): array {
    return [
        [
            'demo_key'   => 'faq-01',
            'title'      => '如何搭建内容电商平台？',
            'slug'       => 'how-to-build-content-commerce',
            'excerpt'    => '搭建内容电商平台需要 6 个阶段：Demo 验证、基础设施、电商引擎、内容-商品关联、规模化、出海。',
            'faq_cat'    => [ '实施指南' ],
            'seo'        => [
                'title'       => '如何搭建内容电商平台？- SunLyvo Nexus FAQ',
                'description' => '搭建内容电商平台需要 6 个阶段：Demo 验证、基础设施、电商引擎、内容-商品关联、规模化、出海。',
                'keywords'    => '内容电商,搭建,实施',
            ],
            'faq_qa'     => [
                [
                    'q' => '如何搭建内容电商平台？',
                    'a' => '搭建内容电商平台分 6 个阶段：① Demo 验证核心价值；② 建设基础设施（用户体系、会员积分）；③ 建设电商引擎（商品、购物车、订单、支付）；④ 建设内容-商品关联（商品卡片、转化追踪）；⑤ 规模化（多商户、B2B、社区）；⑥ 出海（多语言、多站点）。',
                ],
                [
                    'q' => '搭建内容电商平台需要多长时间？',
                    'a' => 'Demo 阶段约 7 周，完整 6 阶段约 12-18 个月。建议先完成 Demo 验证核心价值主张，再决定是否继续投入。',
                ],
                [
                    'q' => '内容电商平台的技术栈如何选型？',
                    'a' => '推荐使用成熟框架而非从零构建。典型技术栈：WordPress Multisite（多站点）+ 自研电商引擎（核心交易）+ React 19 + Ant Design 6（前端）+ Stripe / 微信支付（支付）+ Redis（缓存）。',
                ],
                [
                    'q' => '内容电商平台如何获取流量？',
                    'a' => '三层流量引擎：① SEO（传统搜索引擎）；② GEO（生成式 AI 引擎，如 ChatGPT）；③ AEO（答案引擎，如 Featured Snippet）。三者协同，覆盖全部流量入口。',
                ],
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>搭建内容电商平台</strong>分 6 个阶段：Demo 验证、基础设施、电商引擎、内容-商品关联、规模化、出海。Demo 阶段约 7 周，完整周期 12-18 个月。</p>

<h2>六个阶段</h2>
<ol>
<li><strong>Demo 验证</strong>：25 个页面，验证 SEO/GEO/AEO 三层流量引擎 + 阅读体验</li>
<li><strong>基础设施</strong>：用户体系、会员积分、角色权限</li>
<li><strong>电商引擎</strong>：商品、购物车、订单、支付、库存、税务</li>
<li><strong>内容-商品关联</strong>：商品卡片、转化追踪、内容带货数据</li>
<li><strong>规模化</strong>：多商户、B2B、社区互动</li>
<li><strong>出海</strong>：多语言、多站点、城市分站</li>
</ol>

<h2>相关阅读</h2>
<ul>
<li><a href="/blog/what-is-content-commerce/">什么是内容电商？</a></li>
<li><a href="/wiki/content-commerce/">内容电商</a></li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'faq-02',
            'title'      => '内容电商与传统电商的区别是什么？',
            'slug'       => 'content-vs-traditional-commerce',
            'excerpt'    => '内容电商依赖内容吸引用户，传统电商依赖搜索流量。前者转化路径短，后者转化路径长。',
            'faq_cat'    => [ '概念澄清' ],
            'seo'        => [
                'title'       => '内容电商与传统电商的区别是什么？- SunLyvo Nexus FAQ',
                'description' => '内容电商依赖内容吸引用户，传统电商依赖搜索流量。前者转化路径短（≤3 次点击），后者转化路径长。',
                'keywords'    => '内容电商,传统电商,区别',
            ],
            'faq_qa'     => [
                [
                    'q' => '内容电商与传统电商的区别是什么？',
                    'a' => '三大区别：① 流量来源：内容电商靠内容吸引，传统电商靠搜索；② 转化路径：内容电商 ≤3 次点击，传统电商 5-8 次；③ 用户决策：内容电商靠"种草"，传统电商靠"比价"。',
                ],
                [
                    'q' => '哪种模式转化率更高？',
                    'a' => '内容电商转化率通常为 3-8%，传统电商为 1-2%。内容电商的转化率是传统电商的 2-4 倍。',
                ],
                [
                    'q' => '传统电商能转型为内容电商吗？',
                    'a' => '能，但需要重构内容能力。核心是在商品页之外建立内容生态（博客、百科、FAQ、视频），并在内容中嵌入商品卡片。',
                ],
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>内容电商与传统电商</strong>有三大区别：流量来源（内容 vs 搜索）、转化路径（≤3 次 vs 5-8 次）、用户决策（种草 vs 比价）。内容电商的转化率是传统电商的 2-4 倍。</p>

<h2>对比</h2>
<table>
<thead><tr><th>维度</th><th>内容电商</th><th>传统电商</th></tr></thead>
<tbody>
<tr><td>流量来源</td><td>内容吸引</td><td>搜索流量</td></tr>
<tr><td>转化路径</td><td>≤3 次点击</td><td>5-8 次点击</td></tr>
<tr><td>用户决策</td><td>种草</td><td>比价</td></tr>
<tr><td>转化率</td><td>3-8%</td><td>1-2%</td></tr>
</tbody>
</table>
HTML,
        ],

        [
            'demo_key'   => 'faq-03',
            'title'      => 'GEO 优化多久能看到效果？',
            'slug'       => 'geo-timeline',
            'excerpt'    => 'GEO 优化通常 2-4 周被 AI 引擎收录，1-3 个月开始出现在 AI 答案中。',
            'faq_cat'    => [ '流量引擎' ],
            'seo'        => [
                'title'       => 'GEO 优化多久能看到效果？- SunLyvo Nexus FAQ',
                'description' => 'GEO 优化通常 2-4 周被 AI 引擎收录，1-3 个月开始出现在 AI 答案中。持续更新内容能缩短周期。',
                'keywords'    => 'GEO,优化,时间',
            ],
            'faq_qa'     => [
                [
                    'q' => 'GEO 优化多久能看到效果？',
                    'a' => '通常 2-4 周被 AI 引擎收录，1-3 个月开始出现在 AI 答案中。新站可能需要 3-6 个月。持续更新内容能缩短这个周期。',
                ],
                [
                    'q' => 'GEO 优化需要哪些前提？',
                    'a' => '三个前提：① 配置 llms.txt；② robots.txt 放行引用型 AI 爬虫；③ 内容遵循 BLUF 结构与语义分块规范。',
                ],
                [
                    'q' => '如何知道内容是否被 AI 引用？',
                    'a' => '通过三个方法：① 分析 AI 爬虫访问日志；② 在 AI 引擎中搜索你的内容标题；③ 使用 Schema 中的 @id 追踪引用来源。',
                ],
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>GEO 优化</strong>通常 2-4 周被 AI 引擎收录，1-3 个月开始出现在 AI 答案中。新站可能需要 3-6 个月。持续更新内容能缩短周期。</p>
HTML,
        ],

        [
            'demo_key'   => 'faq-04',
            'title'      => 'SunLyvo Nexus 是什么？',
            'slug'       => 'what-is-sunlyvo-nexus',
            'excerpt'    => 'SunLyvo Nexus 是面向全球市场的内容电商 + 知识付费 + 多商户 + B2B + AI 代理商务一体化平台。',
            'faq_cat'    => [ '产品介绍' ],
            'seo'        => [
                'title'       => 'SunLyvo Nexus 是什么？- SunLyvo Nexus FAQ',
                'description' => 'SunLyvo Nexus 是面向全球市场的内容电商 + 知识付费 + 多商户 + B2B + AI 代理商务一体化平台。',
                'keywords'    => 'SunLyvo Nexus,内容电商平台',
            ],
            'faq_qa'     => [
                [
                    'q' => 'SunLyvo Nexus 是什么？',
                    'a' => 'SunLyvo Nexus 是面向全球市场的内容电商 + 知识付费 + 多商户 + B2B + AI 代理商务一体化平台。核心公式是「内容即货架，用户即渠道，AI 即入口」。',
                ],
                [
                    'q' => 'SunLyvo Nexus 面向哪些用户？',
                    'a' => '面向六类用户：商户（卖货）、创作者（内容变现）、买家（消费）、企业（采购）、站长（本地运营）、AI 代理（自动下单）。',
                ],
                [
                    'q' => 'SunLyvo Nexus 的核心优势是什么？',
                    'a' => '三大优势：① 内容即货架，从看到内容到支付不超过 3 次点击；② AI 可发现，支持 GEO/AEO 被 AI 引用；③ 双市场支持，同时适配国际与国内市场。',
                ],
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo Nexus</strong>是面向全球市场的内容电商 + 知识付费 + 多商户 + B2B + AI 代理商务一体化平台。核心公式是「内容即货架，用户即渠道，AI 即入口」。</p>
HTML,
        ],

        [
            'demo_key'   => 'faq-05',
            'title'      => 'SunLyvo Nexus 支持哪些支付方式？',
            'slug'       => 'supported-payment-methods',
            'excerpt'    => 'SunLyvo Nexus 支持国际支付（Stripe / PayPal）和国内支付（微信支付 / 支付宝 / 银联）双栈。',
            'faq_cat'    => [ '支付' ],
            'seo'        => [
                'title'       => 'SunLyvo Nexus 支持哪些支付方式？- SunLyvo Nexus FAQ',
                'description' => 'SunLyvo Nexus 支持国际支付（Stripe / PayPal）和国内支付（微信支付 / 支付宝 / 银联）双栈，根据用户地区自动路由。',
                'keywords'    => '支付方式,Stripe,微信支付',
            ],
            'faq_qa'     => [
                [
                    'q' => 'SunLyvo Nexus 支持哪些支付方式？',
                    'a' => '双栈支持：国际市场用 Stripe / PayPal；国内市场用微信支付 / 支付宝 / 银联。系统根据用户地区自动路由。',
                ],
                [
                    'q' => '如何判断用户应该使用哪种支付方式？',
                    'a' => '通过三重识别：① 用户设置；② Cookie；③ IP 地理位置。优先使用用户设置，其次 Cookie，最后 IP。',
                ],
                [
                    'q' => '支付失败如何处理？',
                    'a' => '配置降级方案：Stripe 不可用时降级到 PayPal；PayPal 不可用时降级到线下支付。同时记录失败日志，便于排查。',
                ],
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>SunLyvo Nexus 支持双栈支付</strong>：国际市场用 Stripe / PayPal；国内市场用微信支付 / 支付宝 / 银联。系统根据用户地区自动路由。</p>
HTML,
        ],
    ];
}