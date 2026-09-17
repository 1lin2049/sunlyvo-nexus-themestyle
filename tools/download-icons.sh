#!/usr/bin/env bash
#
# SunLyvo Nexus — Lucide Icons 下载脚本
#
# 用法：bash tools/download-icons.sh
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${THEME_DIR}/assets/icons/lucide"
VERSION="0.460.0"

mkdir -p "${OUT_DIR}"

# 需要的图标清单
ICONS=(
    # 导航
    "arrow-right" "arrow-left" "chevron-down" "chevron-up"
    "chevron-right" "chevron-left" "menu" "x" "search"
    "external-link"

    # 用户与账户
    "user" "user-circle" "log-in" "log-out" "settings" "bell"

    # 主题切换
    "sun" "moon" "sun-moon"

    # 电商
    "shopping-cart" "credit-card" "truck" "shield-check"
    "tag" "gift" "package" "store"

    # 操作
    "check" "plus" "minus" "trash-2" "pencil" "copy"
    "share-2" "download" "upload" "filter" "list"

    # 状态
    "star" "heart" "info" "triangle-alert" "circle-check"
    "circle-x" "circle-alert"

    # 阅读与内容
    "book-open" "bookmark" "quote" "link" "clock" "eye"
    "file-text" "image" "video" "music"

    # 数据与图表
    "chart-line" "chart-bar" "trending-up" "trending-down"
    "activity" "gauge"

    # 位置与地图
    "map-pin" "globe" "navigation"

    # AI
    "sparkles" "bot" "brain" "lightbulb"

    # 品牌与社交
    "twitter" "linkedin" "github" "facebook" "youtube"
    "instagram" "rss"
)

BASE_URL="https://raw.githubusercontent.com/lucide-icons/lucide/${VERSION}/icons"

echo "下载 Lucide Icons v${VERSION} 到 ${OUT_DIR}..."
COUNT=0
for icon in "${ICONS[@]}"; do
    url="${BASE_URL}/${icon}.svg"
    out="${OUT_DIR}/${icon}.svg"
    if [ -f "${out}" ]; then
        continue
    fi
    if curl -fsSL "${url}" -o "${out}"; then
        COUNT=$((COUNT + 1))
    else
        echo "  ❌ 失败：${icon}"
        rm -f "${out}"
    fi
done

echo "完成，下载 ${COUNT} 个新图标。"
echo "共 ${#ICONS[@]} 个图标在清单中。"