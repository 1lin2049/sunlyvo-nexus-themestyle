<?php
/**
 * SunLyvo Nexus — 演示博客内容
 *
 * 10 篇博客文章，每篇 1500-3000 字，含 BLUF 结构、商品卡片、内链、Schema。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 返回演示博客列表。
 *
 * @since 1.0.0
 * @return array
 */
function slv_demo_posts(): array {
    return [
        [
            'demo_key'   => 'post-01',
            'title'      => '什么是内容电商？2026 年最完整的入门指南',
            'slug'       => 'what-is-content-commerce',
            'excerpt'    => '内容电商是将内容与商品深度绑定，让用户在阅读中完成购买的新型商业模式。本文将系统讲解内容电商的定义、商业模式、技术架构与实施路径。',
            'categories' => [ '行业洞察' ],
            'tags'       => [ '内容电商', '商业模式', '入门指南' ],
            'seo'        => [
                'title'       => '什么是内容电商？2026 年最完整的入门指南 - SunLyvo Nexus',
                'description' => '内容电商是将内容与商品深度绑定，让用户在阅读中完成购买的新型商业模式。本文系统讲解定义、商业模式、技术架构与实施路径。',
                'keywords'    => '内容电商,内容营销,商业模式,电商架构',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>内容电商</strong>是将内容与商品深度绑定，让用户在阅读、观看、学习的过程中完成购买的新型商业模式。其核心公式是「<strong>内容即货架，用户即渠道，AI 即入口</strong>」，从「看到内容」到「完成支付」不超过 3 次点击。</p>

<h2>内容电商的定义</h2>
<p>内容电商（Content Commerce）指以内容为载体、以商品为转化目标、以用户行为数据为驱动的电商形态。与传统电商相比，内容电商不依赖搜索流量，而是通过高质量内容吸引用户，在内容中自然嵌入商品，实现"种草—转化"一体化。</p>
<p>根据行业研究机构 2025 年数据，内容电商在全球市场的年增长率达到 <strong>32%</strong>，远高于传统电商的 <strong>8%</strong>。在中国市场，内容电商 GMV 已占电商总 GMV 的 <strong>18%</strong>。</p>

<h2>内容电商的三种链路</h2>
<p>理解内容电商，需要掌握三条核心链路：</p>
<ul>
<li><strong>传统电商</strong>：用户 → 商品页 → 加购 → 结账 → 支付</li>
<li><strong>内容电商</strong>：用户 → 内容 → 内容中的商品卡片 → 直接下单 → 支付</li>
<li><strong>AI 代理</strong>：AI 代理 → 内容被引用 → 商品被调用 → ACP 协议自动下单 → 支付完成</li>
</ul>
<p>最短转化链路是从"看到内容"到"完成支付"不超过 3 次点击。这要求商品卡片必须能在内容中原生嵌入，支持原地加购、快速结账。</p>

<h2>内容电商的技术架构</h2>
<p>一个完整的内容电商平台需要以下技术能力：</p>
<ol>
<li><strong>内容管理系统</strong>：支持多种内容类型（博客、百科、FAQ、视频、文档）</li>
<li><strong>电商引擎</strong>：商品、购物车、订单、支付、库存、税务、运费</li>
<li><strong>内容-商品关联</strong>：内容与商品的深度绑定，支持嵌入卡片、转化追踪</li>
<li><strong>流量引擎</strong>：SEO（搜索引擎优化）、GEO（生成式引擎优化）、AEO（答案引擎优化）</li>
<li><strong>阅读体验</strong>：TOC 自动生成、复制、引用、分享、阅读位置记忆</li>
</ol>

<h2>内容电商的商业模式</h2>
<p>内容电商的收入来源通常包括：</p>
<table>
<thead><tr><th>收入来源</th><th>费率/价格</th><th>阶段</th></tr></thead>
<tbody>
<tr><td>商品佣金抽成</td><td>10-15%</td><td>启动期主收入</td></tr>
<tr><td>知识付费抽成</td><td>15-20%</td><td>启动期主收入</td></tr>
<tr><td>商户入驻费</td><td>$99-499/年</td><td>规模化期主收入</td></tr>
<tr><td>会员订阅费</td><td>$9.9-99/月</td><td>规模化期主收入</td></tr>
</tbody>
</table>
<p>根据 2025 年行业基准，内容电商的商户留存率达到 <strong>73%</strong>，用户复购率达到 <strong>42%</strong>，均高于传统电商的 <strong>51%</strong> 和 <strong>28%</strong>。</p>

<h2>内容电商的实施路径</h2>
<p>从 0 开始构建内容电商平台，建议遵循以下路径：</p>
<ol>
<li><strong>Demo 先行验证</strong>：用 25 个页面验证核心价值主张，包括 SEO/GEO/AEO 三层流量引擎 + 阅读体验</li>
<li><strong>基础设施层</strong>：用户体系、会员积分、角色权限</li>
<li><strong>电商引擎层</strong>：商品、购物车、订单、支付、库存</li>
<li><strong>内容-商品关联层</strong>：商品卡片、转化追踪、内容带货数据</li>
<li><strong>规模化层</strong>：多商户、B2B、社区互动</li>
<li><strong>出海层</strong>：多语言、多站点、城市分站</li>
</ol>

<h2>推荐商品</h2>
<p>如果你正在构建内容电商平台，以下工具可以帮助你快速起步：</p>
<div class="slv-product-card" data-product-id="1">
<p><strong>SunLyvo Nexus 内容电商引擎</strong></p>
<p>开箱即用的内容电商解决方案，包含 65+ 张数据表、18+ 内容类型、SEO/GEO/AEO 三层流量引擎。</p>
<p><a href="/products/sunlyvo-nexus-engine/" class="slv-button">查看详情</a></p>
</div>

<h2>常见问题</h2>
<h3>内容电商与传统电商的区别是什么？</h3>
<p>传统电商依赖搜索流量，用户主动搜索商品；内容电商通过高质量内容吸引用户，在内容中嵌入商品，实现"内容—商品—支付"一体化。前者的转化路径长，后者的转化路径短，通常不超过 3 次点击。</p>

<h3>内容电商适合哪些行业？</h3>
<p>内容电商适合三类行业：知识密集型（如教育、咨询、法律）、决策复杂型（如家电、数码、B2B 采购）、兴趣驱动型（如美妆、健身、宠物）。这三类行业的用户需要大量信息辅助决策，内容电商能显著降低决策成本。</p>

<h3>内容电商需要哪些技术支持？</h3>
<p>至少需要 5 类技术：内容管理系统、电商引擎、内容-商品关联、流量引擎（SEO/GEO/AEO）、阅读体验子系统。推荐使用成熟框架（如 WordPress Multisite + 自研电商引擎）而非从零构建。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/content-commerce-engine/">内容电商引擎</a>——技术架构详解</li>
<li><a href="/faq/how-to-build-content-commerce/">如何搭建内容电商平台？</a>——实施步骤</li>
<li><a href="/blog/geo-optimization-guide/">GEO 优化完整指南</a>——让内容被 AI 引用</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-02',
            'title'      => 'GEO 优化完整指南：让内容被 ChatGPT 引用',
            'slug'       => 'geo-optimization-guide',
            'excerpt'    => 'GEO（生成式引擎优化）是让内容被 ChatGPT、Perplexity、Claude 等 AI 引擎引用的新技术。本文讲解 llms.txt、语义分块、BLUF 结构、AI 爬虫管理等核心技术。',
            'categories' => [ '流量引擎' ],
            'tags'       => [ 'GEO', 'AI引用', 'ChatGPT', 'Perplexity' ],
            'seo'        => [
                'title'       => 'GEO 优化完整指南：让内容被 ChatGPT 引用 - SunLyvo Nexus',
                'description' => 'GEO 是让内容被 AI 引擎引用的新技术。本文讲解 llms.txt、语义分块、BLUF 结构、AI 爬虫管理等核心技术。',
                'keywords'    => 'GEO,生成式引擎优化,AI引用,llms.txt',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>GEO（Generative Engine Optimization，生成式引擎优化）</strong>是让内容被 ChatGPT、Perplexity、Claude、Google AI Overviews 等生成式 AI 引擎引用为信源的技术。核心手段包括 llms.txt、语义分块、BLUF 结构、AI 爬虫管理与引用追踪。</p>

<h2>GEO 与 SEO 的区别</h2>
<p>SEO 让内容被搜索引擎收录，GEO 让内容被 AI 引擎引用。二者目标不同：</p>
<table>
<thead><tr><th>维度</th><th>SEO</th><th>GEO</th></tr></thead>
<tbody>
<tr><td>目标引擎</td><td>Google / Bing / 百度</td><td>ChatGPT / Perplexity / Claude</td></tr>
<tr><td>输出形式</td><td>搜索结果列表</td><td>AI 生成的答案</td></tr>
<tr><td>核心信号</td><td>外链、关键词、技术 SEO</td><td>结构化、语义分块、llms.txt</td></tr>
<tr><td>验证方式</td><td>Search Console</td><td>引用追踪</td></tr>
</tbody>
</table>

<h2>GEO 的五大核心手段</h2>
<h3>1. llms.txt 自动生成</h3>
<p>llms.txt 是专为 LLM 设计的网站索引，位于网站根目录。它列出核心页面、内容类型、结构化数据入口，帮助 AI 引擎快速理解站点结构。</p>
<p>一个标准的 llms.txt 包含：站点介绍、核心页面、各内容类型的最新条目、结构化数据入口（Schema、Sitemap、robots.txt）。根据 2025 年数据，配置 llms.txt 的站点被 AI 引用的概率提升 <strong>47%</strong>。</p>

<h3>2. 语义分块（Semantic Chunking）</h3>
<p>将长内容按 H2/H3 拆分为语义独立的块，每块可独立理解、独立引用。AI 引擎倾向于引用完整、独立、可验证的语义块。建议每个 H2 下的内容控制在 200-500 字，并包含至少 1 个量化事实。</p>

<h3>3. BLUF 结构（Bottom Line Up Front）</h3>
<p>BLUF 源自军事写作，要求结论前置。每个 H2/H3 标题下，前 40-60 字必须是直接答案，而非铺垫。这样 AI 引擎抓取时能直接提取答案作为引用。</p>

<h3>4. AI 爬虫管理</h3>
<p>必须放行的引用型 AI 爬虫包括：</p>
<ul>
<li><strong>OAI-SearchBot</strong> / ChatGPT-User（OpenAI）</li>
<li><strong>PerplexityBot</strong> / Perplexity-User</li>
<li><strong>ClaudeBot</strong> / Claude-Web（Anthropic）</li>
<li><strong>Google-Extended</strong> / Applebot-Extended</li>
<li><strong>QwenBot</strong> / TongyiBot（国内）</li>
</ul>
<p>在 robots.txt 中显式放行这些爬虫，是内容被 AI 引用的前提。</p>

<h3>5. 引用追踪</h3>
<p>通过爬虫日志分析哪些 AI 引擎访问了你的内容，通过 Schema 中的 @id 追踪引用来源。完整的引用追踪能帮助你优化内容策略。</p>

<h2>GEO 内容写作规范</h2>
<ol>
<li><strong>BLUF 结构</strong>：每个 H2/H3 前 40-60 字给答案</li>
<li><strong>语义独立块</strong>：每个 H2 独立可理解</li>
<li><strong>实体明确</strong>：使用完整名称而非代词</li>
<li><strong>量化事实</strong>：每篇至少 5 处可引用数据</li>
<li><strong>结构化</strong>：多用列表、表格、定义</li>
</ol>

<h2>推荐工具</h2>
<div class="slv-product-card" data-product-id="2">
<p><strong>SunLyvo GEO 优化套件</strong></p>
<p>自动生成 llms.txt、语义分块 Schema、AI 爬虫日志、GEO 五维评分。</p>
<p><a href="/products/geo-optimization-kit/" class="slv-button">查看详情</a></p>
</div>

<h2>常见问题</h2>
<h3>llms.txt 与 robots.txt 有什么区别？</h3>
<p>robots.txt 告诉爬虫"哪些不能抓"，llms.txt 告诉 LLM"哪些是重点"。前者是限制，后者是索引。二者必须同时配置才能最大化 AI 引用率。</p>

<h3>GEO 优化多久能看到效果？</h3>
<p>取决于内容质量和 AI 引擎的更新频率。通常情况下，新内容在 <strong>2-4 周</strong>内被 AI 引擎收录，<strong>1-3 个月</strong>后开始出现在 AI 答案中。持续更新内容能缩短这个周期。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/geo/">GEO（生成式引擎优化）</a>——词条定义</li>
<li><a href="/blog/aeo-complete-guide/">AEO 优化完整指南</a>——抢占 Featured Snippet</li>
<li><a href="/blog/what-is-content-commerce/">什么是内容电商？</a>——商业模式</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-03',
            'title'      => 'AEO 优化完整指南：抢占 Featured Snippet',
            'slug'       => 'aeo-complete-guide',
            'excerpt'    => 'AEO（答案引擎优化）是抢占 Google Featured Snippet、People Also Ask、语音搜索结果的技术。本文讲解 FAQPage、HowTo、QAPage、Speakable 等 Schema 应用。',
            'categories' => [ '流量引擎' ],
            'tags'       => [ 'AEO', 'Featured Snippet', '语音搜索' ],
            'seo'        => [
                'title'       => 'AEO 优化完整指南：抢占 Featured Snippet - SunLyvo Nexus',
                'description' => 'AEO 是抢占 Google Featured Snippet、People Also Ask、语音搜索结果的技术。本文讲解 FAQPage、HowTo、QAPage、Speakable 等 Schema 应用。',
                'keywords'    => 'AEO,答案引擎优化,Featured Snippet,语音搜索',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>AEO（Answer Engine Optimization，答案引擎优化）</strong>是让内容被直接选为答案的技术，目标是出现在 Google Featured Snippet、People Also Ask、语音搜索结果中。核心手段包括 FAQPage / HowTo / QAPage / Speakable Schema 与 Snippet 结构优化。</p>

<h2>AEO 的三种 Snippet 类型</h2>
<h3>段落型 Snippet</h3>
<p>触发查询通常是"什么是 X"、"X 是什么"。优化要点：答案控制在 <strong>40-60 字</strong>，直接回答问题，不含铺垫。使用 <code>&lt;p&gt;</code> 标签包裹答案。</p>

<h3>列表型 Snippet</h3>
<p>触发查询通常是"如何做 X"、"X 的步骤"。优化要点：使用有序列表 <code>&lt;ol&gt;</code>，每步控制在 10-15 字，总步骤 3-8 个。列表前必须有明确的问题标题（H2 或 H3）。</p>

<h3>表格型 Snippet</h3>
<p>触发查询通常是"X 对比 Y"、"X 价格"。优化要点：使用 <code>&lt;table&gt;</code> 标签，表头明确，数据准确。表格前必须有明确的问题标题。</p>

<h2>AEO 的四大 Schema</h2>
<h3>1. FAQPage Schema</h3>
<p>FAQPage 是最常用的 AEO Schema。每个问答对包含 Question 和 Answer 两个字段。建议每页配置 3-8 个 FAQ。FAQ 答案控制在 40-60 字，与段落型 Snippet 一致。</p>

<h3>2. HowTo Schema</h3>
<p>HowTo 用于教程类内容。包含 name、description、totalTime、estimatedCost、tool、supply、step 等字段。每个 step 必须有明确的 name 和 text。</p>

<h3>3. QAPage Schema</h3>
<p>QAPage 用于问答类页面（如 FAQ 详情页）。包含 Question、answerCount、acceptedAnswer 等字段。区别于 FAQPage：FAQPage 是一页多问，QAPage 是一页一问。</p>

<h3>4. Speakable Schema</h3>
<p>Speakable 用于语音搜索优化。通过 cssSelector 指定可朗读的内容区域。建议指定 <code>.slv-bluf-summary</code>、<code>.slv-faq-answer</code>、<code>h1</code> 等选择器。</p>

<h2>AEO 内容写作规范</h2>
<ol>
<li><strong>问题标题用 H2 或 H3</strong>：AI 引擎通过标题识别问题</li>
<li><strong>答案前置</strong>：第一段就是答案，不铺垫</li>
<li><strong>答案长度控制</strong>：段落型 40-60 字，列表型 3-8 步</li>
<li><strong>使用量化事实</strong>：AI 引擎偏好可验证的数据</li>
<li><strong>避免模糊表述</strong>：用"是"而非"可能是"</li>
</ol>

<h2>推荐工具</h2>
<div class="slv-product-card" data-product-id="3">
<p><strong>SunLyvo AEO 优化套件</strong></p>
<p>自动生成 FAQPage / HowTo / QAPage / Speakable Schema，Snippet 结构检测，AEO 五维评分。</p>
<p><a href="/products/aeo-optimization-kit/" class="slv-button">查看详情</a></p>
</div>

<h2>常见问题</h2>
<h3>FAQPage 与 QAPage 的区别是什么？</h3>
<p>FAQPage 是一页包含多个问答对，适合 FAQ 列表页；QAPage 是一页一个问答，适合单个 FAQ 详情页。二者不能混用，否则会导致 Schema 验证失败。</p>

<h3>AEO 优化后多久能看到 Featured Snippet？</h3>
<p>通常需要 <strong>2-8 周</strong>。取决于内容质量、竞争程度、站点权重。新站可能需要 3-6 个月。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/aeo/">AEO（答案引擎优化）</a>——词条定义</li>
<li><a href="/blog/geo-optimization-guide/">GEO 优化完整指南</a>——让内容被 AI 引用</li>
<li><a href="/blog/what-is-content-commerce/">什么是内容电商？</a>——商业模式</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-04',
            'title'      => '多商户平台的订单拆分与分账机制',
            'slug'       => 'multi-vendor-order-split',
            'excerpt'    => '多商户平台的核心挑战是订单拆分与分账。本文讲解按商户拆分订单、Stripe Connect 分账、收益结算、提现流程的完整实现。',
            'categories' => [ '技术架构' ],
            'tags'       => [ '多商户', '订单拆分', 'Stripe Connect' ],
            'seo'        => [
                'title'       => '多商户平台的订单拆分与分账机制 - SunLyvo Nexus',
                'description' => '多商户平台的核心挑战是订单拆分与分账。本文讲解按商户拆分订单、Stripe Connect 分账、收益结算、提现流程的完整实现。',
                'keywords'    => '多商户,订单拆分,分账,Stripe Connect',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>多商户平台的订单拆分</strong>指用户下单后，系统按商户维度拆分订单，分别计算各商户的收益、佣金、应结算金额。<strong>分账</strong>指通过 Stripe Connect 等支付服务商将资金直接分配给各商户账户。二者的核心是佣金计算规则与结算周期。</p>

<h2>订单拆分的三种模式</h2>
<h3>模式 1：下单时拆分</h3>
<p>用户下单时立即按商户拆分，生成多个子订单。优点：商户能立即看到订单；缺点：支付时需要合并多笔交易。</p>

<h3>模式 2：支付后拆分</h3>
<p>用户完成支付后，系统再按商户拆分。优点：支付流程简单；缺点：商户看到订单有延迟。</p>

<h3>模式 3：结算时拆分</h3>
<p>订单完成后再计算各商户收益。优点：实现简单；缺点：不适合实时结算场景。</p>
<p>SunLyvo Nexus 采用<strong>模式 2</strong>，在订单完成钩子中触发拆分，兼顾支付体验与商户体验。</p>

<h2>佣金计算规则</h2>
<p>佣金计算通常包含以下维度：</p>
<table>
<thead><tr><th>维度</th><th>说明</th><th>典型值</th></tr></thead>
<tbody>
<tr><td>平台佣金率</td><td>平台从每笔订单抽取的比例</td><td>10-15%</td></tr>
<tr><td>分销佣金率</td><td>推荐人获得的佣金</td><td>5-10%</td></tr>
<tr><td>商户分成</td><td>商户实际所得</td><td>75-85%</td></tr>
<tr><td>站长分成</td><td>城市站长获得的佣金</td><td>2-5%</td></tr>
</tbody>
</table>

<h2>Stripe Connect 分账</h2>
<p>Stripe Connect 提供三种账户类型：</p>
<ul>
<li><strong>Standard</strong>：商户自己管理 Stripe 账户</li>
<li><strong>Express</strong>：平台代管，商户快速接入</li>
<li><strong>Custom</strong>：完全自定义</li>
</ul>
<p>推荐使用 <strong>Express</strong> 账户，兼顾接入速度与平台控制力。商户首次入驻时创建 Connect 账户，订单完成后通过 <code>Transfer</code> API 分账。</p>

<h2>收益结算与提现</h2>
<p>商户收益结算流程：</p>
<ol>
<li>订单完成 → 生成 vendor_earnings 记录（status: pending）</li>
<li>过 T+7 天后 → 状态更新为 available</li>
<li>商户申请提现 → 生成 withdrawal 记录</li>
<li>平台审核 → 通过 Stripe Transfer 打款</li>
<li>打款成功 → 状态更新为 paid</li>
</ol>

<h2>常见问题</h2>
<h3>多商户平台的佣金率如何设置？</h3>
<p>佣金率通常按商品类目、商户等级、促销活动动态调整。建议使用配置驱动的佣金规则表，避免硬编码。</p>

<h3>Stripe Connect 支持哪些国家？</h3>
<p>Stripe Connect 支持 40+ 个国家和地区。中国大陆商户需要使用 Stripe 香港或其他支持地区账户。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/multi-vendor/">多商户</a>——词条定义</li>
<li><a href="/blog/what-is-content-commerce/">什么是内容电商？</a>——商业模式</li>
<li><a href="/faq/how-to-build-content-commerce/">如何搭建内容电商平台？</a>——实施步骤</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-05',
            'title'      => 'B2B 企业采购的账户体系设计',
            'slug'       => 'b2b-enterprise-account-design',
            'excerpt'    => 'B2B 企业采购的核心是账户体系设计。本文讲解企业主账户、子账户、角色权限、采购限额、账期支付的完整实现方案。',
            'categories' => [ '技术架构' ],
            'tags'       => [ 'B2B', '企业采购', '账户体系' ],
            'seo'        => [
                'title'       => 'B2B 企业采购的账户体系设计 - SunLyvo Nexus',
                'description' => 'B2B 企业采购的核心是账户体系设计。本文讲解企业主账户、子账户、角色权限、采购限额、账期支付的完整实现方案。',
                'keywords'    => 'B2B,企业采购,账户体系,采购限额',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>B2B 企业采购的账户体系</strong>包含企业主账户、多个子账户、角色权限、采购限额、账期支付五个核心模块。企业管理员管理子账户与限额，采购员下单，查看者只读。核心目标是平衡采购效率与财务控制。</p>

<h2>企业账户的三种角色</h2>
<table>
<thead><tr><th>角色</th><th>权限</th><th>典型用户</th></tr></thead>
<tbody>
<tr><td>company_admin</td><td>管理子账户、查看所有订单、设置采购限额</td><td>采购经理、财务主管</td></tr>
<tr><td>company_buyer</td><td>下单、查看自己的订单、使用公司账期</td><td>部门采购员</td></tr>
<tr><td>company_viewer</td><td>只读，查看订单和报价，不能下单</td><td>审计、财务</td></tr>
</tbody>
</table>

<h2>采购限额机制</h2>
<p>采购限额用于控制企业采购风险，包含以下维度：</p>
<ul>
<li><strong>月度限额</strong>：每个子账户每月的采购总额上限</li>
<li><strong>单笔限额</strong>：单笔订单的金额上限</li>
<li><strong>品类限额</strong>：特定商品类目的采购上限</li>
<li><strong>审批阈值</strong>：超过阈值需要审批</li>
</ul>
<p>采购限额在结算时校验，超过限额则阻止下单或触发审批流程。</p>

<h2>账期支付</h2>
<p>B2B 企业采购通常使用账期支付，即"先收货后付款"。账期包含以下要素：</p>
<ol>
<li><strong>账期长度</strong>：通常 30、60、90 天</li>
<li><strong>信用额度</strong>：企业可获得的总信用额度</li>
<li><strong>已用额度</strong>：当前已使用的信用额度</li>
<li><strong>还款日</strong>：每月固定日期结算</li>
</ol>
<p>账期支付需要企业通过资质审核（营业执照、VAT 税号、银行账户）。</p>

<h2>子账户管理</h2>
<p>企业管理员可以添加、编辑、停用子账户。子账户关联企业的 <code>_slv_company_id</code> meta，通过此 meta 建立企业与用户的关联。</p>
<p>子账户添加流程：企业管理员提交邮箱 + 角色 → 系统创建用户 → 发送邀请邮件 → 用户激活账号。</p>

<h2>常见问题</h2>
<h3>B2B 与 B2C 账户体系有什么区别？</h3>
<p>B2B 账户是"组织"概念，包含多个子账户；B2C 账户是"个人"概念。B2B 需要采购限额、账期支付、审批流程；B2C 通常不需要。</p>

<h3>企业采购如何防止滥用？</h3>
<p>通过采购限额、审批阈值、账期信用额度三重机制。超过任一项都需要审批或终止下单。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/b2b-commerce/">B2B 电商</a>——词条定义</li>
<li><a href="/blog/multi-vendor-order-split/">多商户平台的订单拆分与分账机制</a>——技术架构</li>
<li><a href="/faq/how-to-build-content-commerce/">如何搭建内容电商平台？</a>——实施步骤</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-06',
            'title'      => '设计 Token 五层架构完整指南',
            'slug'       => 'design-token-five-layers',
            'excerpt'    => '设计 Token 五层架构是大型设计系统的核心方法。本文讲解 Seed、Map、Alias、Component、State 五层的职责、派生规则、消费约束。',
            'categories' => [ '设计系统' ],
            'tags'       => [ '设计 Token', '设计系统', '五层架构' ],
            'seo'        => [
                'title'       => '设计 Token 五层架构完整指南 - SunLyvo Nexus',
                'description' => '设计 Token 五层架构是大型设计系统的核心方法。本文讲解 Seed、Map、Alias、Component、State 五层的职责、派生规则、消费约束。',
                'keywords'    => '设计Token,设计系统,五层架构,Design Token',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>设计 Token 五层架构</strong>将设计 Token 分为 Seed（种子）、Map（映射）、Alias（别名）、Component（组件）、State（状态）五层。<strong>只有 Seed 层允许硬编码</strong>，其余层通过派生计算与语义别名引用，最终由业务 CSS 消费 Component 和 State 层。</p>

<h2>五层架构的职责</h2>
<table>
<thead><tr><th>层</th><th>职责</th><th>允许硬编码</th></tr></thead>
<tbody>
<tr><td>Seed</td><td>原始值定义</td><td>✅ 唯一允许</td></tr>
<tr><td>Map</td><td>派生计算（color-mix / calc）</td><td>❌</td></tr>
<tr><td>Alias</td><td>语义别名</td><td>❌</td></tr>
<tr><td>Component</td><td>组件专用</td><td>❌</td></tr>
<tr><td>State</td><td>状态变体（hover / active / disabled / focus）</td><td>❌</td></tr>
</tbody>
</table>

<h2>五层的代码示例</h2>
<h3>第 1 层：Seed</h3>
<pre><code>:root {
    --slv-seed-blue-6: #0066ff;
    --slv-seed-space-4: 16px;
}</code></pre>

<h3>第 2 层：Map</h3>
<pre><code>:root {
    --slv-map-blue-hover: color-mix(in srgb, var(--slv-seed-blue-6) 90%, black);
    --slv-map-blue-active: color-mix(in srgb, var(--slv-seed-blue-6) 80%, black);
}</code></pre>

<h3>第 3 层：Alias</h3>
<pre><code>:root {
    --slv-color-primary: var(--slv-seed-blue-6);
    --slv-color-primary-hover: var(--slv-map-blue-hover);
    --slv-color-text-primary: var(--slv-seed-neutral-8);
}</code></pre>

<h3>第 4 层：Component</h3>
<pre><code>:root {
    --slv-button-bg: var(--slv-color-primary);
    --slv-button-padding: var(--slv-seed-space-4);
    --slv-button-radius: 8px;
}</code></pre>

<h3>第 5 层：State</h3>
<pre><code>:root {
    --slv-button-bg-hover: var(--slv-color-primary-hover);
    --slv-button-bg-active: var(--slv-map-blue-active);
    --slv-button-bg-disabled: color-mix(in srgb, var(--slv-color-primary) 40%, white);
    --slv-button-ring-focus: 0 0 0 3px color-mix(in srgb, var(--slv-color-primary) 30%, transparent);
}</code></pre>

<h2>业务 CSS 的消费规则</h2>
<p>业务 CSS 只消费 Component 和 State 层：</p>
<pre><code>.slv-button {
    background: var(--slv-button-bg);
    padding: var(--slv-button-padding);
    border-radius: var(--slv-button-radius);
}
.slv-button:hover { background: var(--slv-button-bg-hover); }
.slv-button:active { background: var(--slv-button-bg-active); }
.slv-button:disabled { background: var(--slv-button-bg-disabled); }
.slv-button:focus-visible { box-shadow: var(--slv-button-ring-focus); }</code></pre>

<h2>五层架构的七条规则</h2>
<ol>
<li>只有 Seed 层允许硬编码</li>
<li>Map 层只做派生，不引入新值</li>
<li>Alias 层只做语义映射</li>
<li>Component 层只消费 Alias</li>
<li>State 层只消费 Component</li>
<li>业务 CSS 只消费 Alias / Component / State</li>
<li>theme.json 为唯一真理源</li>
</ol>

<h2>常见问题</h2>
<h3>为什么状态变体要独立成层？</h3>
<p>状态变体（hover / active / disabled / focus）是组件在不同交互状态下的表现，与组件基础样式分离能让语义更清晰，便于统一调整。例如所有组件的 focus ring 都从 State 层统一取值。</p>

<h3>五层架构适合小项目吗？</h3>
<p>五层架构主要适合中大型设计系统。小项目可以简化为三层（Seed / Alias / Component），但一旦规模扩大，建议迁移到五层。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/design-token/">设计 Token</a>——词条定义</li>
<li><a href="/faq/how-to-build-content-commerce/">如何搭建内容电商平台？</a>——实施步骤</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-07',
            'title'      => 'WordPress Multisite 多站点架构实战',
            'slug'       => 'wordpress-multisite-architecture',
            'excerpt'    => 'WordPress Multisite 是城市分站、多语言站点的成熟方案。本文讲解网络配置、三级网络、用户跨站点、性能优化的完整实践。',
            'categories' => [ '技术架构' ],
            'tags'       => [ 'WordPress Multisite', '多站点', '城市分站' ],
            'seo'        => [
                'title'       => 'WordPress Multisite 多站点架构实战 - SunLyvo Nexus',
                'description' => 'WordPress Multisite 是城市分站、多语言站点的成熟方案。本文讲解网络配置、三级网络、用户跨站点、性能优化的完整实践。',
                'keywords'    => 'WordPress Multisite,多站点,城市分站',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>WordPress Multisite</strong>是 WordPress 原生多站点方案，适合城市分站、多语言站点、多品牌运营。<strong>三级网络</strong>（平台 → 区域 → 城市）是其典型架构，用户表共享，角色按站点隔离。核心挑战是 <code>switch_to_blog</code> 的性能优化。</p>

<h2>Multisite 的核心概念</h2>
<ul>
<li><strong>Network</strong>：整个多站点网络，由超级管理员管理</li>
<li><strong>Blog / Site</strong>：每个子站点，拥有独立的文章、页面、设置</li>
<li><strong>Users</strong>：用户表全局共享（wp_users / wp_usermeta）</li>
<li><strong>Roles</strong>：角色按站点独立，同一用户在不同站点可有不同角色</li>
</ul>

<h2>三级网络架构</h2>
<pre><code>平台主站（Super Admin）
  │
  ├── 区域站（区域 Admin）
  │     ├── 城市站 A（站长 Admin）
  │     ├── 城市站 B（站长 Admin）
  │     └── 城市站 C（站长 Admin）
  │
  ├── 区域站（区域 Admin）
  │     ├── 城市站 D（站长 Admin）
  │     └── 城市站 E（站长 Admin）
  │
  └── 区域站（区域 Admin）
        └── 城市站 F（站长 Admin）</code></pre>

<h2>wp-config.php 配置</h2>
<pre><code>define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'example.com' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );</code></pre>

<h2>用户跨站点</h2>
<p>获取用户在指定站点的角色：</p>
<pre><code>function slv_get_user_role_on_blog( int $user_id, int $blog_id ): string {
    switch_to_blog( $blog_id );
    $user = new WP_User( $user_id );
    $role = ! empty( $user->roles ) ? $user->roles[0] : '';
    restore_current_blog();
    return $role;
}</code></pre>

<h2>性能注意事项</h2>
<table>
<thead><tr><th>问题</th><th>应对</th></tr></thead>
<tbody>
<tr><td>50 个站点产生 500-600 张表</td><td>规划表数量增长</td></tr>
<tr><td>100 个站点超过 1000 张表</td><td>使用对象缓存</td></tr>
<tr><td>wp_usermeta 超过 10 万行时查询延迟</td><td>禁止多余站点分配 + Redis 缓存</td></tr>
<tr><td>switch_to_blog 在循环中性能崩溃</td><td>聚合表 + 异步同步</td></tr>
</tbody>
</table>

<h2>常见问题</h2>
<h3>Multisite 与多站点插件有什么区别？</h3>
<p>Multisite 是 WordPress 原生功能，子站点共享核心代码，只隔离数据。多站点插件（如 WPML Multisite）通常是在 Multisite 之上的封装。推荐使用原生 Multisite。</p>

<h3>Multisite 最多支持多少子站点？</h3>
<p>技术上没有硬限制，但性能上建议控制在 <strong>100-200 个站点</strong>以内。超过后需要专业的缓存与数据库优化。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/multisite/">WordPress Multisite</a>——词条定义</li>
<li><a href="/blog/multi-vendor-order-split/">多商户平台的订单拆分与分账机制</a>——技术架构</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-08',
            'title'      => '自研电商引擎的数据表设计',
            'slug'       => 'ecommerce-database-design',
            'excerpt'    => '自研电商引擎的数据表设计决定平台的可扩展性。本文讲解商品、变体、购物车、订单、支付、库存、税务、运费、优惠券共 18 张核心表的设计。',
            'categories' => [ '技术架构' ],
            'tags'       => [ '电商引擎', '数据库设计', '架构' ],
            'seo'        => [
                'title'       => '自研电商引擎的数据表设计 - SunLyvo Nexus',
                'description' => '自研电商引擎的数据表设计决定平台的可扩展性。本文讲解商品、变体、购物车、订单、支付等 18 张核心表的设计。',
                'keywords'    => '电商引擎,数据库设计,架构,数据表',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>自研电商引擎</strong>通常需要 18 张核心数据表，覆盖商品、变体、购物车、订单、支付、库存、税务、运费、优惠券、多币种十大模块。核心设计原则是<strong>分层存储</strong>：状态层、配置层、记录层、关系层、资产层、进度层、交易层、统计层分别独立。</p>

<h2>数据分层原则</h2>
<table>
<thead><tr><th>层级</th><th>存储</th><th>内容</th></tr></thead>
<tbody>
<tr><td>状态层</td><td>wp_usermeta</td><td>会员等级、积分余额、订阅状态</td></tr>
<tr><td>配置层</td><td>自定义表</td><td>等级配置、佣金规则、税率</td></tr>
<tr><td>记录层</td><td>自定义表</td><td>积分流水、订单收益、提现</td></tr>
<tr><td>关系层</td><td>自定义表</td><td>内容-商品、知识关联</td></tr>
<tr><td>交易层</td><td>自定义表</td><td>商品、购物车、订单、支付</td></tr>
</tbody>
</table>

<h2>18 张核心表</h2>
<ol>
<li><strong>slv_products</strong>：商品主表（含价格、库存、HS 编码、MOQ）</li>
<li><strong>slv_product_variants</strong>：商品变体表</li>
<li><strong>slv_product_categories</strong>：商品-分类关系表</li>
<li><strong>slv_product_images</strong>：商品-图片关系表</li>
<li><strong>slv_carts</strong>：购物车主表</li>
<li><strong>slv_cart_items</strong>：购物车项</li>
<li><strong>slv_orders</strong>：订单主表</li>
<li><strong>slv_order_items</strong>：订单项（含佣金、分账字段）</li>
<li><strong>slv_order_status_history</strong>：订单状态历史</li>
<li><strong>slv_payments</strong>：支付记录</li>
<li><strong>slv_refunds</strong>：退款记录</li>
<li><strong>slv_inventory_logs</strong>：库存日志</li>
<li><strong>slv_tax_rates</strong>：税率表</li>
<li><strong>slv_shipping_zones</strong>：运费区域</li>
<li><strong>slv_shipping_methods</strong>：运费方式</li>
<li><strong>slv_coupons</strong>：优惠券</li>
<li><strong>slv_coupon_usages</strong>：优惠券使用记录</li>
<li><strong>slv_currencies</strong>：货币配置</li>
</ol>

<h2>关键设计要点</h2>
<h3>1. 订单项必须含分账字段</h3>
<p>订单项需要包含 <code>commission_rate</code>、<code>commission_amount</code>、<code>vendor_earning</code>、<code>platform_earning</code>、<code>referrer_earning</code> 五个字段，支持多商户分账。</p>

<h3>2. 购物车项必须含内容溯源</h3>
<p>购物车项需要包含 <code>content_id</code>、<code>content_type</code>、<code>referrer_id</code> 三个字段，支持内容带货追踪。</p>

<h3>3. 库存扣减必须用事务 + 行锁</h3>
<p>库存扣减需要 <code>START TRANSACTION</code> + <code>SELECT ... FOR UPDATE</code> + <code>COMMIT</code>，防止超卖。</p>

<h3>4. 订单号必须防并发</h3>
<p>订单号使用独立的序列表 <code>slv_order_sequence</code> 通过 <code>AUTO_INCREMENT</code> 生成，避免时间戳碰撞。</p>

<h2>常见问题</h2>
<h3>为什么不直接用 WooCommerce？</h3>
<p>WooCommerce 是成熟的电商插件，但它的表结构（wp_posts + wp_postmeta）不适合高并发场景。<strong>自研电商引擎</strong>使用独立表，查询性能更优，且完全可控。</p>

<h3>18 张表够用吗？</h3>
<p>18 张表覆盖核心电商功能。若需扩展（如 B2B 批发、多仓库、订阅），建议新增表而非修改已有表，保持向后兼容。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/ecommerce-engine/">电商引擎</a>——词条定义</li>
<li><a href="/blog/multi-vendor-order-split/">多商户平台的订单拆分与分账机制</a>——技术架构</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-09',
            'title'      => 'AI 代理商务：ACP 协议入门',
            'slug'       => 'ai-agent-commerce-acp',
            'excerpt'    => 'ACP（Agentic Commerce Protocol）是 AI 代理商务的开放协议。本文讲解 ACP 的四端点、共享支付令牌、AI 代理分销追踪的完整实现。',
            'categories' => [ 'AI 商务' ],
            'tags'       => [ 'ACP', 'AI代理', 'Stripe' ],
            'seo'        => [
                'title'       => 'AI 代理商务：ACP 协议入门 - SunLyvo Nexus',
                'description' => 'ACP 是 AI 代理商务的开放协议。本文讲解 ACP 的四端点、共享支付令牌、AI 代理分销追踪的完整实现。',
                'keywords'    => 'ACP,AI代理商务,Agentic Commerce',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>ACP（Agentic Commerce Protocol）</strong>是由 OpenAI 与 Stripe 联合推出的 AI 代理商务开放协议。核心是四个端点：创建结账会话、更新会话、完成结账、取消会话。支付使用<strong>共享支付令牌（SPT）</strong>，AI 代理无需接触用户原始卡号。</p>

<h2>ACP 的四个端点</h2>
<table>
<thead><tr><th>端点</th><th>方法</th><th>用途</th></tr></thead>
<tbody>
<tr><td>/checkout_sessions</td><td>POST</td><td>创建结账会话</td></tr>
<tr><td>/checkout_sessions/{id}</td><td>POST</td><td>更新会话</td></tr>
<tr><td>/checkout_sessions/{id}/complete</td><td>POST</td><td>完成结账</td></tr>
<tr><td>/checkout_sessions/{id}/cancel</td><td>POST</td><td>取消会话</td></tr>
</tbody>
</table>

<h2>ACP 的六个核心实施要点</h2>
<ol>
<li><strong>AI 代理负责发现商品与购买倾向</strong>：代理通过内容、评论、比价发现商品</li>
<li><strong>商户保留对交易结账的掌控权</strong>：商户决定是否接受代理的订单</li>
<li><strong>卖家始终是记录商户</strong>：订单的商户方是实际卖家，非代理</li>
<li><strong>控制目录、库存、定价、税务和履约</strong>：商户侧完全控制</li>
<li><strong>代理管理对话并推动购买</strong>：代理负责与用户对话</li>
<li><strong>支付提供商负责保护和处理支付</strong>：Stripe 处理支付安全</li>
</ol>

<h2>共享支付令牌（SPT）</h2>
<p>SPT 是 ACP 的核心安全机制。其工作原理：</p>
<ol>
<li>用户在 AI 代理中授权支付方式</li>
<li>Stripe 生成一次性支付令牌（SPT）</li>
<li>AI 代理使用 SPT 创建结账会话</li>
<li>商户通过 SPT 完成支付，不接触原始卡号</li>
<li>SPT 只能用于特定商户、特定金额、特定有效期</li>
</ol>

<h2>AI 代理分销追踪</h2>
<p>订单需记录以下字段以追踪 AI 代理来源：</p>
<ul>
<li><code>agent_id</code>：代理标识（如 "chatgpt-4o"）</li>
<li><code>content_id</code>：引用的内容 ID</li>
<li><code>traffic_source</code>：固定为 "ai_agent"</li>
</ul>
<p>基于这些字段，可计算 AI 代理渠道的 GMV、转化率、佣金。</p>

<h2>常见问题</h2>
<h3>ACP 与 MCP 有什么区别？</h3>
<p>ACP（Agentic Commerce Protocol）专注于电商交易；MCP（Model Context Protocol）专注于 AI 与工具的连接。二者互补：MCP 负责让 AI 调用工具，ACP 负责让 AI 完成交易。</p>

<h3>ACP 目前支持哪些 AI 代理？</h3>
<p>截至 2026 年，OpenAI 的 ChatGPT 已支持 ACP 协议。其他主流代理（Claude、Perplexity、Gemini）也在接入中。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/acp/">ACP 协议</a>——词条定义</li>
<li><a href="/blog/what-is-content-commerce/">什么是内容电商？</a>——商业模式</li>
</ul>
HTML,
        ],

        [
            'demo_key'   => 'post-10',
            'title'      => '阅读体验子系统：TOC 做到极致的十个细节',
            'slug'       => 'reader-experience-toc',
            'excerpt'    => '阅读体验是内容平台的差异化壁垒。本文讲解 TOC 做到极致的十个细节：多级、折叠、进度、预估时间、移动端抽屉、键盘导航等。',
            'categories' => [ '设计系统' ],
            'tags'       => [ '阅读体验', 'TOC', '用户体验' ],
            'seo'        => [
                'title'       => '阅读体验子系统：TOC 做到极致的十个细节 - SunLyvo Nexus',
                'description' => '阅读体验是内容平台的差异化壁垒。本文讲解 TOC 做到极致的十个细节：多级、折叠、进度、预估时间、移动端抽屉、键盘导航等。',
                'keywords'    => '阅读体验,TOC,用户体验,目录',
            ],
            'content'    => <<<'HTML'
<p class="slv-bluf-summary"><strong>阅读体验子系统</strong>是内容平台的差异化壁垒。TOC（目录）做到极致需要满足十个细节：多级、智能折叠、进度可视化、章节预估时间、移动端悬浮+抽屉、键盘导航、位置持久化、锚点平滑滚动、URL 锚点同步、打印友好。核心是让用户<strong>随时知道位置、随时能跳转</strong>。</p>

<h2>TOC 的十个极致细节</h2>
<table>
<thead><tr><th>#</th><th>目标</th><th>实现</th></tr></thead>
<tbody>
<tr><td>1</td><td>多级 TOC</td><td>支持 H2/H3/H4 三级嵌套</td></tr>
<tr><td>2</td><td>智能折叠</td><td>长文档自动折叠 H4</td></tr>
<tr><td>3</td><td>进度可视化</td><td>每个 TOC 条目显示章节阅读进度</td></tr>
<tr><td>4</td><td>章节预估时间</td><td>每个 H2 显示预计阅读时间</td></tr>
<tr><td>5</td><td>移动端悬浮+抽屉</td><td>移动端为悬浮按钮，点击弹出抽屉</td></tr>
<tr><td>6</td><td>键盘导航</td><td>[ / ] 跳转上下章节，\ 聚焦 TOC</td></tr>
<tr><td>7</td><td>位置持久化</td><td>记住 TOC 展开状态</td></tr>
<tr><td>8</td><td>锚点平滑滚动</td><td>尊重 prefers-reduced-motion</td></tr>
<tr><td>9</td><td>URL 锚点同步</td><td>滚动时更新 URL hash</td></tr>
<tr><td>10</td><td>打印友好</td><td>打印时 TOC 自动展开</td></tr>
</tbody>
</table>

<h2>阅读体验的另外三个核心</h2>
<h3>复制/引用/分享</h3>
<p>复制支持四种格式（纯文本/Markdown/HTML/富文本），引用支持四种格式（APA/MLA/Chicago/GB7714），分享支持多平台（Twitter/LinkedIn/Reddit/微博/QQ空间）。</p>

<h3>阅读位置记忆</h3>
<p>跨设备同步、智能续读提示、多篇文章列表、章节进度、隐私模式、忽略短停留、段落级别精度，共七项细节。</p>

<h3>快捷键</h3>
<p>t 切换主题、[ / ] 跳转上下章节、\ 聚焦 TOC、j / k 滚动、Home 回到顶部、Esc 关闭面板、Ctrl/Cmd+Shift+C 引用。</p>

<h2>常见问题</h2>
<h3>为什么 TOC 要独立子系统？</h3>
<p>阅读页面的 CSS 需求与其他页面差异较大。独立子系统可以避免主站样式污染阅读页，也便于针对性优化。核心是<strong>reader 环境禁止加载 main.css / motion.css / mobile.css</strong>。</p>

<h3>TOC 的进度可视化怎么做？</h3>
<p>通过每个标题的 <code>offsetTop</code> 与当前 <code>scrollY</code> 计算，为每个 TOC 条目渲染一条进度条。滚动时用 <code>requestAnimationFrame</code> 节流更新。</p>

<h2>相关阅读</h2>
<ul>
<li><a href="/wiki/reader-experience/">阅读体验</a>——词条定义</li>
<li><a href="/blog/design-token-five-layers/">设计 Token 五层架构完整指南</a>——设计系统</li>
</ul>
HTML,
        ],
    ];
}