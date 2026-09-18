#!/usr/bin/env bash
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WP_ROOT="$(cd "${THEME_DIR}/../../.." && pwd)"

echo "════════════════════════════════════════════════════════"
echo "  SunLyvo Nexus 中台部署"
echo "════════════════════════════════════════════════════════"

# 1. 构建
echo ""
echo "▶ [1/5] 构建中台..."
cd "${THEME_DIR}/src"
rm -rf dist .umi .umi-production .umi-test
pnpm build

if [ ! -d "dist" ]; then
    echo "❌ 构建失败：dist/ 不存在"
    exit 1
fi
echo "✅ 构建成功"

# 2. 部署到 /app/
echo ""
echo "▶ [2/5] 部署到 /app/..."
ADMIN_DIR="${WP_ROOT}/app"
mkdir -p "${ADMIN_DIR}"
rm -rf "${ADMIN_DIR:?}"/*
cp -r dist/* "${ADMIN_DIR}/"
echo "✅ 部署完成"

# 3. 生成 config.js
echo ""
echo "▶ [3/5] 生成 config.js..."
cd "${WP_ROOT}"
wp option update slv_admin_url 'http://sunlyvo.com/app/' 2>/dev/null || true
wp eval 'slv_write_admin_config_js();' || echo "⚠️ 请到后台手动生成"

if [ -f "${ADMIN_DIR}/config.js" ]; then
    echo "✅ config.js："
    cat "${ADMIN_DIR}/config.js"
fi

# 4. 清缓存
echo ""
echo "▶ [4/5] 清缓存..."
wp cache flush 2>/dev/null || true
wp rewrite flush --hard 2>/dev/null || true
echo "✅ 完成"

# 5. 验证
echo ""
echo "▶ [5/5] 验证..."
if [ -f "${ADMIN_DIR}/index.html" ]; then
    echo "✅ index.html 存在"
    echo ""
    echo "关键检查：所有 JS/CSS 引用应带 /app/ 前缀"
    grep -oP '(src|href)="[^"]*"' "${ADMIN_DIR}/index.html" | head -10
fi

echo ""
echo "════════════════════════════════════════════════════════"
echo "  ✅ 部署完成"
echo "  访问：http://sunlyvo.com/app/"
echo "════════════════════════════════════════════════════════"