#!/usr/bin/env bash
#
# SunLyvo Nexus — 中台部署验证脚本
#
set -euo pipefail

ADMIN_DIR="/opt/1panel/www/sites/sunlyvo.com/index/admin"
BASE="http://sunlyvo.com/admin"

echo "════════════════════════════════════════════════════════"
echo "  中台部署验证"
echo "════════════════════════════════════════════════════════"

# ─── [1] 物理文件 ───
echo ""
echo "▶ [1] 物理文件..."
ls "${ADMIN_DIR}" | grep -E '^(index\.html|config\.js|umi\.)' || echo "  ❌ 关键文件缺失"

UMI_COUNT=$(ls "${ADMIN_DIR}" | grep -c '^umi\.' || echo 0)
if [ "${UMI_COUNT}" = "1" ]; then
    echo "  ✅ umi.*.js 数量：1"
else
    echo "  ❌ umi.*.js 数量：${UMI_COUNT}（应为 1）"
fi

# ─── [2] index.html 引用的 hash 与物理文件是否匹配 ───
echo ""
echo "▶ [2] hash 匹配..."
HTML_HASH=$(grep -oP 'umi\.\K[a-f0-9]+(?=\.js)' "${ADMIN_DIR}/index.html" | head -1 || echo "")
PHYS_HASH=$(ls "${ADMIN_DIR}" | grep -oP 'umi\.\K[a-f0-9]+(?=\.js)' | head -1 || echo "")
echo "  index.html 引用：umi.${HTML_HASH}.js"
echo "  物理文件：    umi.${PHYS_HASH}.js"
if [ "${HTML_HASH}" = "${PHYS_HASH}" ]; then
    echo "  ✅ 匹配"
else
    echo "  ❌ 不匹配！部署未完成或构建未同步"
fi

# ─── [3] HTTP 状态 ───
echo ""
echo "▶ [3] HTTP 状态..."
for path in "/" "/dashboard" "/platform/template" "/config.js" "/umi.${HTML_HASH}.js"; do
    code=$(curl -s -o /dev/null -w "%{http_code}" "${BASE}${path}" 2>/dev/null || echo "000")
    status=$([ "${code}" = "200" ] && echo "✅" || echo "❌")
    echo "  ${status} ${code}  ${BASE}${path}"
done

# ─── [4] 缓存头 ───
echo ""
echo "▶ [4] 缓存头..."
echo "  index.html:"
curl -sI "${BASE}/index.html" | grep -i "cache-control" | sed 's/^/    /' || echo "    (无)"
echo "  config.js:"
curl -sI "${BASE}/config.js" | grep -i "cache-control" | sed 's/^/    /' || echo "    (无)"

# ─── [5] config.js 内容 ───
echo ""
echo "▶ [5] config.js 内容..."
curl -s "${BASE}/config.js" | head -10 | sed 's/^/  /'

# ─── [6] REST 端点 ───
echo ""
echo "▶ [6] REST 端点..."
echo "  GET /user/theme:"
curl -s "http://sunlyvo.com/wp-json/slv/v1/user/theme" | sed 's/^/    /' || echo "    (失败)"
echo "  GET /templates:"
curl -s "http://sunlyvo.com/wp-json/slv/v1/templates" | head -c 200 | sed 's/^/    /' || echo "    (失败)"

echo ""
echo "════════════════════════════════════════════════════════"
echo "  验证完成"
echo "════════════════════════════════════════════════════════"