#!/usr/bin/env bash
#
# SunLyvo Nexus — 构建图标 sprite
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SRC_DIR="${THEME_DIR}/assets/icons/lucide"
OUT="${THEME_DIR}/assets/icons/slv-icons.svg"

if [ ! -d "${SRC_DIR}" ]; then
    echo "请先运行 tools/download-icons.sh"
    exit 1
fi

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" > "${OUT}"
echo "<svg xmlns=\"http://www.w3.org/2000/svg\" style=\"display:none\" aria-hidden=\"true\">" >> "${OUT}"
echo "  <defs>" >> "${OUT}"

for file in "${SRC_DIR}"/*.svg; do
    [ -f "${file}" ] || continue
    name="$(basename "${file}" .svg)"

    # 提取 viewBox、内部内容
    content="$(tr -d '\n' < "${file}")"
    # 提取 <svg> 内容（去掉外框）
    inner="$(echo "${content}" | sed -E 's@.*<svg[^>]*>(.*)</svg>.*@\1@')"

    echo "    <symbol id=\"slv-icon-${name}\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\">${inner}</symbol>" >> "${OUT}"
done

echo "  </defs>" >> "${OUT}"
echo "</svg>" >> "${OUT}"

echo "已生成：${OUT}"