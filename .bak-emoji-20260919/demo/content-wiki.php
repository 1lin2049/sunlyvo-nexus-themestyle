<?php
/**
 * SunLyvo Nexus — 演示百科内容
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 返回演示百科列表。
 *
 * @since 1.0.0
 * @return array
 */
function slv_demo_wiki(): array {
    return [
        [
            'demo_key'    => 'wiki-01',
            'title'       => '内容电商',
            'slug'        => 'content-commerce',
            'excerpt'     => '内容电商是以内容为载体、以商品为转化目标、以用户行为数据为驱动的电商形态。',
            'wiki_cat'    => [ '商业模式' ],
            'meta'        => [
                '_slv_wiki_definition' => '内容电商（Content Commerce）是将内容与商品深度绑定，让用户在阅读中完成购买的新型商业模式。核心公式是「内容即货架，用户即渠道，AI 即入口」。',
                '_slv_wiki_aliases'    => 'Content Commerce,内容营销电商,种草电商',
            ],
            'seo'         => [
                'title'       => '内容电商 - SunLyvo Nexus 百科',
                'description' => '内容电商是以内容为载体、以商品为转化目标、以用户行为数据为驱动的电商形态。核心公式是「内容即货架，用户即渠道，AI 即入口」。',
                'keywords'    => '内容电商,Content Commerce,商业模式',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>内容电商（Content Commerce）</strong>是将内容与商品深度绑定，让用户在阅读、观看、学习的过程中完成购买的新型商业模式。核心公式是「内容即货架，用户即渠道，AI 即入口」，从「看到内容」到「完成支付」不超过 3 次点击。</p>

<h2>定义</h2>
<p>内容电商是电子商务与内容营销的融合形态。传统电商依赖搜索流量，用户主动搜索商品；内容电商通过高质量内容吸引用户，在内容中嵌入商品，实现"种草—转化"一体化。</p>

<h2>核心特征</h2>
<ul>
<li><strong>内容即货架</strong>：内容本身就是商品展示位</li>
<li><strong>用户即渠道</strong>：用户通过分享内容成为分销渠道</li>
<li><strong>AI 即入口</strong>：AI 代理通过引用内容发现商品</li>
<li><strong>最短转化链路</strong>：不超过 3 次点击完成支付</li>
</ul>

<h2>商业模式</h2>
<table>
<thead><tr><th>收入来源</th><th>费率</th></tr></thead>
<tbody>
<tr><td>商品佣金抽成</td><td>10-15%</td></tr>
<tr><td>知识付费抽成</td><td>15-20%</td></tr>
<tr><td>商户入驻费</td><td>$99-499/年</td></tr>
<tr><td>会员订阅费</td><td>$9.9-99/月</td></tr>
</tbody>
</table>

<h2>相关概念</h2>
<ul>
<li><a href="/wiki/geo/">GEO</a>——生成式引擎优化</li>
<li><a href="/wiki/aeo/">AEO</a>——答案引擎优化</li>
<li><a href="/wiki/acp/">ACP</a>——AI 代理商务协议</li>
</ul>
HTML,
        ],

        [
            'demo_key'    => 'wiki-02',
            'title'       => 'GEO（生成式引擎优化）',
            'slug'        => 'geo',
            'excerpt'     => 'GEO 是让内容被 ChatGPT、Perplexity、Claude 等生成式 AI 引擎引用的优化技术。',
            'wiki_cat'    => [ '流量引擎' ],
            'meta'        => [
                '_slv_wiki_definition' => 'GEO（Generative Engine Optimization，生成式引擎优化）是让内容被 ChatGPT、Perplexity、Claude 等生成式 AI 引擎引用为信源的技术，核心手段包括 llms.txt、语义分块、BLUF 结构、AI 爬虫管理。',
                '_slv_wiki_aliases'    => 'Generative Engine Optimization,生成式引擎优化,AI优化',
            ],
            'seo'         => [
                'title'       => 'GEO（生成式引擎优化）- SunLyvo Nexus 百科',
                'description' => 'GEO 是让内容被 ChatGPT、Perplexity、Claude 等生成式 AI 引擎引用的优化技术。核心手段包括 llms.txt、语义分块、BLUF 结构。',
                'keywords'    => 'GEO,生成式引擎优化,AI引用',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>GEO（Generative Engine Optimization，生成式引擎优化）</strong>是让内容被 ChatGPT、Perplexity、Claude、Google AI Overviews 等生成式 AI 引擎引用为信源的技术。核心手段包括 llms.txt、语义分块、BLUF 结构、AI 爬虫管理与引用追踪。</p>

<h2>定义</h2>
<p>GEO 是 SEO 在 AI 时代的延伸。SEO 让内容被搜索引擎收录，GEO 让内容被 AI 引擎引用。二者目标不同：前者面向搜索列表，后者面向 AI 答案。</p>

<h2>核心手段</h2>
<ol>
<li><strong>llms.txt</strong>：为 LLM 设计的网站索引</li>
<li><strong>语义分块</strong>：将内容按 H2/H3 拆分为独立块</li>
<li><strong>BLUF 结构</strong>：结论前置，40-60 字给答案</li>
<li><strong>AI 爬虫管理</strong>：robots.txt 放行引用型爬虫</li>
<li><strong>引用追踪</strong>：分析哪些 AI 引擎引用了内容</li>
</ol>

<h2>与 SEO 的对比</h2>
<table>
<thead><tr><th>维度</th><th>SEO</th><th>GEO</th></tr></thead>
<tbody>
<tr><td>目标引擎</td><td>Google / 百度</td><td>ChatGPT / Perplexity</td></tr>
<tr><td>输出形式</td><td>搜索结果列表</td><td>AI 生成的答案</td></tr>
<tr><td>核心信号</td><td>外链、关键词</td><td>结构化、语义分块</td></tr>
</tbody>
</table>

<h2>相关概念</h2>
<ul>
<li><a href="/wiki/aeo/">AEO</a>——答案引擎优化</li>
<li><a href="/wiki/content-commerce/">内容电商</a>——商业模式</li>
<li><a href="/blog/geo-optimization-guide/">GEO 优化完整指南</a>——深度阅读</li>
</ul>
HTML,
        ],

        [
            'demo_key'    => 'wiki-03',
            'title'       => 'AEO（答案引擎优化）',
            'slug'        => 'aeo',
            'excerpt'     => 'AEO 是抢占 Google Featured Snippet、People Also Ask、语音搜索结果的技术。',
            'wiki_cat'    => [ '流量引擎' ],
            'meta'        => [
                '_slv_wiki_definition' => 'AEO（Answer Engine Optimization，答案引擎优化）是让内容被直接选为答案的技术，目标是出现在 Google Featured Snippet、People Also Ask、语音搜索结果中。',
                '_slv_wiki_aliases'    => 'Answer Engine Optimization,答案引擎优化,Snippet优化',
            ],
            'seo'         => [
                'title'       => 'AEO（答案引擎优化）- SunLyvo Nexus 百科',
                'description' => 'AEO 是抢占 Google Featured Snippet、People Also Ask、语音搜索结果的技术。核心手段包括 FAQPage / HowTo / QAPage / Speakable Schema。',
                'keywords'    => 'AEO,答案引擎优化,Featured Snippet',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>AEO（Answer Engine Optimization，答案引擎优化）</strong>是让内容被直接选为答案的技术，目标是出现在 Google Featured Snippet、People Also Ask、语音搜索结果中。核心手段包括 FAQPage / HowTo / QAPage / Speakable Schema 与 Snippet 结构优化。</p>

<h2>定义</h2>
<p>AEO 是 SEO 的进阶形态。SEO 让内容出现在搜索结果中，AEO 让内容成为搜索结果的答案本身。前者争夺排名，后者争夺答案。</p>

<h2>三种 Snippet 类型</h2>
<ul>
<li><strong>段落型</strong>：回答"什么是 X"，答案 40-60 字</li>
<li><strong>列表型</strong>：回答"如何做 X"，3-8 步有序列表</li>
<li><strong>表格型</strong>：回答"X 对比 Y"，使用 table 标签</li>
</ul>

<h2>四大 Schema</h2>
<ol>
<li><strong>FAQPage</strong>：一页多个问答对</li>
<li><strong>HowTo</strong>：教程类内容</li>
<li><strong>QAPage</strong>：一页一个问答</li>
<li><strong>Speakable</strong>：语音搜索优化</li>
</ol>

<h2>相关概念</h2>
<ul>
<li><a href="/wiki/geo/">GEO</a>——生成式引擎优化</li>
<li><a href="/wiki/content-commerce/">内容电商</a>——商业模式</li>
<li><a href="/blog/aeo-complete-guide/">AEO 优化完整指南</a>——深度阅读</li>
</ul>
HTML,
        ],

        [
            'demo_key'    => 'wiki-04',
            'title'       => '设计 Token',
            'slug'        => 'design-token',
            'excerpt'     => '设计 Token 是设计系统中的最小原子值，用于统一颜色、间距、字体、阴影等设计决策。',
            'wiki_cat'    => [ '设计系统' ],
            'meta'        => [
                '_slv_wiki_definition' => '设计 Token（Design Token）是设计系统中的最小原子值，用于统一颜色、间距、字体、阴影等设计决策。采用五层架构：Seed、Map、Alias、Component、State。',
                '_slv_wiki_aliases'    => 'Design Token,设计变量,设计原子',
            ],
            'seo'         => [
                'title'       => '设计 Token - SunLyvo Nexus 百科',
                'description' => '设计 Token 是设计系统中的最小原子值，用于统一颜色、间距、字体、阴影等设计决策。采用五层架构：Seed、Map、Alias、Component、State。',
                'keywords'    => '设计Token,Design Token,设计系统',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>设计 Token（Design Token）</strong>是设计系统中的最小原子值，用于统一颜色、间距、字体、阴影等设计决策。大型设计系统通常采用五层架构：Seed、Map、Alias、Component、State。核心规则是<strong>只有 Seed 层允许硬编码</strong>。</p>

<h2>定义</h2>
<p>设计 Token 是设计与开发之间的契约。设计师定义 Token，开发者在代码中消费 Token，二者共享同一套设计语言。Token 通常以 CSS 变量、JSON、SCSS 变量等形式存在。</p>

<h2>五层架构</h2>
<table>
<thead><tr><th>层</th><th>职责</th><th>允许硬编码</th></tr></thead>
<tbody>
<tr><td>Seed</td><td>原始值定义</td><td>✅</td></tr>
<tr><td>Map</td><td>派生计算</td><td>❌</td></tr>
<tr><td>Alias</td><td>语义别名</td><td>❌</td></tr>
<tr><td>Component</td><td>组件专用</td><td>❌</td></tr>
<tr><td>State</td><td>状态变体</td><td>❌</td></tr>
</tbody>
</table>

<h2>核心规则</h2>
<ol>
<li>只有 Seed 层允许硬编码</li>
<li>Map 层只做派生</li>
<li>Alias 层只做语义映射</li>
<li>Component 层只消费 Alias</li>
<li>State 层只消费 Component</li>
<li>业务 CSS 只消费 Alias / Component / State</li>
<li>theme.json 为唯一真理源</li>
</ol>

<h2>相关概念</h2>
<ul>
<li><a href="/blog/design-token-five-layers/">设计 Token 五层架构完整指南</a>——深度阅读</li>
</ul>
HTML,
        ],

        [
            'demo_key'    => 'wiki-05',
            'title'       => 'ACP 协议',
            'slug'        => 'acp',
            'excerpt'     => 'ACP 是 OpenAI 与 Stripe 联合推出的 AI 代理商务开放协议，实现 AI 代理自动发现商品、比价、下单、支付。',
            'wiki_cat'    => [ 'AI 商务' ],
            'meta'        => [
                '_slv_wiki_definition' => 'ACP（Agentic Commerce Protocol）是 OpenAI 与 Stripe 联合推出的 AI 代理商务开放协议。核心是四个端点：创建结账会话、更新会话、完成结账、取消会话。支付使用共享支付令牌（SPT）。',
                '_slv_wiki_aliases'    => 'Agentic Commerce Protocol,AI代理商务协议',
            ],
            'seo'         => [
                'title'       => 'ACP 协议 - SunLyvo Nexus 百科',
                'description' => 'ACP 是 OpenAI 与 Stripe 联合推出的 AI 代理商务开放协议。核心是四个端点与共享支付令牌（SPT）。',
                'keywords'    => 'ACP,Agentic Commerce Protocol,AI代理',
            ],
            'content'     => <<<'HTML'
<p class="slv-bluf-summary"><strong>ACP（Agentic Commerce Protocol）</strong>是由 OpenAI 与 Stripe 联合推出的 AI 代理商务开放协议。核心是四个端点：创建结账会话、更新会话、完成结账、取消会话。支付使用<strong>共享支付令牌（SPT）</strong>，AI 代理无需接触用户原始卡号。</p>

<h2>定义</h2>
<p>ACP 定义了 AI 代理与电商平台交互的标准协议，让 AI 代理能够自动发现商品、比价、下单、支付，同时保证商户保留对交易的掌控权。</p>

<h2>四个端点</h2>
<table>
<thead><tr><th>端点</th><th>方法</th><th>用途</th></tr></thead>
<tbody>
<tr><td>/checkout_sessions</td><td>POST</td><td>创建结账会话</td></tr>
<tr><td>/checkout_sessions/{id}</td><td>POST</td><td>更新会话</td></tr>
<tr><td>/checkout_sessions/{id}/complete</td><td>POST</td><td>完成结账</td></tr>
<tr><td>/checkout_sessions/{id}/cancel</td><td>POST</td><td>取消会话</td></tr>
</tbody>
</table>

<h2>核心实施要点</h2>
<ol>
<li>AI 代理负责发现商品与购买倾向</li>
<li>商户保留对交易结账的掌控权</li>
<li>卖家始终是记录商户</li>
<li>控制目录、库存、定价、税务和履约</li>
<li>代理管理对话并推动购买</li>
<li>支付提供商负责保护和处理支付</li>
</ol>

<h2>相关概念</h2>
<ul>
<li><a href="/wiki/content-commerce/">内容电商</a>——商业模式</li>
<li><a href="/blog/ai-agent-commerce-acp/">AI 代理商务：ACP 协议入门</a>——深度阅读</li>
</ul>
HTML,
        ],
    ];
}