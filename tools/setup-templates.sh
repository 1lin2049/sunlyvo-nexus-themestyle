#!/usr/bin/env bash
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TPL_DIR="${THEME_DIR}/assets/templates"

# 清理旧目录
rm -rf "${THEME_DIR}/assets/css/styles"

mkdir -p "${TPL_DIR}"

TEMPLATES=(
    "brand:品牌默认:base:false:true"
    "dark:深色模式:base:true:true"
    "industrial:工业机械:industry:false:true"
    "tech:消费电子:industry:false:true"
    "consumer:日用消费品:industry:false:true"
    "medical:医疗健康:industry:false:true"
    "energy:新能源:industry:false:true"
    "home:家居建材:industry:false:true"
    "high-contrast:高对比:aux:false:true"
    "minimal:极简:aux:false:true"
)

for item in "${TEMPLATES[@]}"; do
    IFS=':' read -r slug name group user_switch admin_switch <<< "$item"
    DIR="${TPL_DIR}/${slug}"
    mkdir -p "${DIR}/templates"

    cat > "${DIR}/template.json" <<JSON
{
    "slug": "${slug}",
    "name": "${name}",
    "description": "",
    "version": "1.0.0",
    "author": "李咏燊",
    "group": "${group}",
    "user_switchable": ${user_switch},
    "admin_switchable": ${admin_switch},
    "supports": {
        "overrides": ["tokens", "components", "layout", "blocks"]
    }
}
JSON

    # 创建三个空 CSS 文件（占位）
    : > "${DIR}/tokens.css"
    : > "${DIR}/components.css"
    : > "${DIR}/layout.css"
done

echo "✅ 模板目录已创建：${TPL_DIR}"
ls -la "${TPL_DIR}"