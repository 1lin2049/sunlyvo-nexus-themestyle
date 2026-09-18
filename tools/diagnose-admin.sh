#!/usr/bin/env bash
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WP_ROOT="$(cd "${THEME_DIR}/../../.." && pwd)"

echo "════════════════════════════════════════════════════════"
echo "  中台诊断报告"
echo "════════════════════════════════════════════════════════"

echo ""
echo "=== 1. 构建产物 ==="
ls -la "${THEME_DIR}/src/dist/" 2>/dev/null | head -20 || echo "❌ dist/ 不存在"

echo ""
echo "=== 2. index.html 引用路径（关键！应带 /app/） ==="
if [ -f "${THEME_DIR}/src/dist/index.html" ]; then
    grep -oP '(src|href)="[^"]*"' "${THEME_DIR}/src/dist/index.html"
else
    echo "❌ index.html 不存在"
fi

echo ""
echo "=== 3. 部署目录 ==="
ls -la "${WP_ROOT}/app/" 2>/dev/null | head -20 || echo "❌ /app/ 不存在"

echo ""
echo "=== 4. config.js ==="
if [ -f "${WP_ROOT}/app/config.js" ]; then
    cat "${WP_ROOT}/app/config.js"
else
    echo "❌ config.js 不存在"
fi

echo ""
echo "=== 5. WordPress 选项 ==="
cd "${WP_ROOT}"
wp option get slv_admin_url 2>/dev/null || echo "未设置"
wp option get slv_admin_deploy_mode 2>/dev/null || echo "未设置"

echo ""
echo "=== 6. HTTP 验证 ==="
echo -n "/app/ → "
curl -s -o /dev/null -w "%{http_code}\n" http://sunlyvo.com/app/

echo -n "/app/config.js → "
curl -s -o /dev/null -w "%{http_code}\n" http://sunlyvo.com/app/config.js

echo -n "/app/platform/template → "
curl -s -o /dev/null -w "%{http_code}\n" http://sunlyvo.com/app/platform/template

echo ""
echo "════════════════════════════════════════════════════════"