<?php
/**
 * SunLyvo Nexus — 演示合集内容
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 返回演示合集列表。
 *
 * @since 1.0.0
 * @return array
 */
function slv_demo_collections(): array {
    return [
        [
            'demo_key'        => 'collection-01',
            'title'           => '内容电商实战系列',
            'slug'            => 'content-commerce-series',
            'collection_type' => 'article_series',
            'is_free'         => '1',
            'collection_cat'  => [ '行业洞察' ],
            'excerpt'         => '从商业模式到技术架构，系统讲解内容电商的完整体系。共 5 章，前 2 章免费试读。',
            'seo'             => [
                'title'       => '内容电商实战系列 - SunLyvo Nexus 合集',
                'description' => '从商业模式到技术架构，系统讲解内容电商的完整体系。共 5 章，前 2 章免费试读。',
                'keywords'    => '内容电商,实战系列,系统课程',
            ],
            'content'         => <<<'HTML'
<p class="slv-bluf-summary"><strong>内容电商实战系列</strong>从商业模式到技术架构，系统讲解内容电商的完整体系。共 5 章，前 2 章免费试读。</p>

<h2>合集内容</h2>
<ol>
<li>第 1 章：内容电商的定义与商业模式（免费试读）</li>
<li>第 2 章：内容电商的三种链路（免费试读）</li>
<li>第 3 章：内容电商的技术架构</li>
<li>第 4 章：内容-商品关联的实现</li>
<li>第 5 章：流量引擎：SEO + GEO + AEO</li>
</ol>
HTML,
            'chapters'        => [
                [
                    'title'           => '第 1 章：内容电商的定义与商业模式',
                    'excerpt'         => '系统讲解内容电商的定义、三条链路、收入模型。',
                    'is_free_preview' => '1',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary"><strong>内容电商</strong>是将内容与商品深度绑定，让用户在阅读中完成购买的新型商业模式。核心公式是「内容即货架，用户即渠道，AI 即入口」。</p>

<h2>三条链路</h2>
<ul>
<li>传统电商：用户 → 商品页 → 加购 → 结账 → 支付</li>
<li>内容电商：用户 → 内容 → 商品卡片 → 直接下单 → 支付</li>
<li>AI 代理：AI 代理 → 内容被引用 → 商品被调用 → ACP 自动下单 → 支付</li>
</ul>

<h2>收入模型</h2>
<table>
<thead><tr><th>收入来源</th><th>费率</th></tr></thead>
<tbody>
<tr><td>商品佣金抽成</td><td>10-15%</td></tr>
<tr><td>知识付费抽成</td><td>15-20%</td></tr>
<tr><td>商户入驻费</td><td>$99-499/年</td></tr>
<tr><td>会员订阅费</td><td>$9.9-99/月</td></tr>
</tbody>
</table>
HTML,
                ],
                [
                    'title'           => '第 2 章：内容电商的三种链路',
                    'excerpt'         => '深入讲解传统电商、内容电商、AI 代理三条链路。',
                    'is_free_preview' => '1',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">内容电商的三种链路分别是<strong>传统电商</strong>（搜索→商品）、<strong>内容电商</strong>（内容→商品卡片→下单）、<strong>AI 代理</strong>（AI→内容→ACP 下单）。</p>

<h2>最短转化链路</h2>
<p>从"看到内容"到"完成支付"不超过 3 次点击。这要求商品卡片必须能在内容中原生嵌入，支持原地加购、快速结账。</p>
HTML,
                ],
                [
                    'title'           => '第 3 章：内容电商的技术架构',
                    'excerpt'         => '讲解内容电商的五层架构、外部服务引入、数据模型。',
                    'is_free_preview' => '0',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">内容电商的技术架构分<strong>五层</strong>：表现层、应用层、领域层、数据层、基础设施层。核心是内容-商品关联层与流量引擎层。</p>
HTML,
                ],
                [
                    'title'           => '第 4 章：内容-商品关联的实现',
                    'excerpt'         => '讲解商品卡片嵌入、原地加购、快速结账、转化追踪。',
                    'is_free_preview' => '0',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">内容-商品关联通过 <code>slv_content_product</code> 表实现，包含 8 种商品卡片形态，支持原地加购、快速结账、转化追踪。</p>
HTML,
                ],
                [
                    'title'           => '第 5 章：流量引擎：SEO + GEO + AEO',
                    'excerpt'         => '讲解 SEO、GEO、AEO 三层流量引擎的协同。',
                    'is_free_preview' => '0',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">流量引擎分三层：<strong>SEO</strong> 让内容被搜索引擎收录，<strong>GEO</strong> 让内容被 AI 引擎引用，<strong>AEO</strong> 让内容成为答案本身。</p>
HTML,
                ],
            ],
        ],

        [
            'demo_key'        => 'collection-02',
            'title'           => 'GEO/AEO 流量引擎实战',
            'slug'            => 'geo-aeo-masterclass',
            'collection_type' => 'video_course',
            'is_free'         => '0',
            'collection_cat'  => [ '流量引擎' ],
            'excerpt'         => '系统讲解 GEO 与 AEO 的完整实现方法，含 llms.txt、语义分块、Schema 配置、Snippet 抢占。共 4 章。',
            'seo'             => [
                'title'       => 'GEO/AEO 流量引擎实战 - SunLyvo Nexus 合集',
                'description' => '系统讲解 GEO 与 AEO 的完整实现方法，含 llms.txt、语义分块、Schema 配置、Snippet 抢占。共 4 章。',
                'keywords'    => 'GEO,AEO,流量引擎,实战课程',
            ],
            'content'         => <<<'HTML'
<p class="slv-bluf-summary"><strong>GEO/AEO 流量引擎实战</strong>系统讲解 GEO 与 AEO 的完整实现方法，含 llms.txt、语义分块、Schema 配置、Snippet 抢占。共 4 章。</p>

<h2>合集内容</h2>
<ol>
<li>第 1 章：GEO 核心原理与 llms.txt 配置</li>
<li>第 2 章：语义分块与 BLUF 结构</li>
<li>第 3 章：AEO 四大 Schema 配置</li>
<li>第 4 章：Snippet 抢占与 AEO 评分</li>
</ol>
HTML,
            'chapters'        => [
                [
                    'title'           => '第 1 章：GEO 核心原理与 llms.txt 配置',
                    'excerpt'         => 'GEO 的定义、与 SEO 的区别、llms.txt 配置。',
                    'is_free_preview' => '1',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">GEO 是让内容被 ChatGPT、Perplexity 等生成式 AI 引擎引用的技术。核心是 llms.txt、语义分块、BLUF 结构、AI 爬虫管理。</p>
HTML,
                ],
                [
                    'title'           => '第 2 章：语义分块与 BLUF 结构',
                    'excerpt'         => '如何按 H2/H3 拆分内容，如何写 BLUF 结构。',
                    'is_free_preview' => '0',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">语义分块将内容按 H2/H3 拆分为独立块，每块 200-500 字。BLUF 要求每个标题下前 40-60 字给答案。</p>
HTML,
                ],
                [
                    'title'           => '第 3 章：AEO 四大 Schema 配置',
                    'excerpt'         => 'FAQPage、HowTo、QAPage、Speakable Schema 配置。',
                    'is_free_preview' => '0',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">AEO 四大 Schema：FAQPage（一页多问）、HowTo（教程）、QAPage（一页一问）、Speakable（语音搜索）。</p>
HTML,
                ],
                [
                    'title'           => '第 4 章：Snippet 抢占与 AEO 评分',
                    'excerpt'         => '段落型、列表型、表格型 Snippet 抢占，AEO 评分。',
                    'is_free_preview' => '0',
                    'content'         => <<<'HTML'
<p class="slv-bluf-summary">三种 Snippet 类型：段落型（40-60 字）、列表型（3-8 步）、表格型（清晰表头）。AEO 五维评分：段落、列表、FAQ、Speakable。</p>
HTML,
                ],
            ],
        ],
    ];
}