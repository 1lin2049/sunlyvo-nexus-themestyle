#!/bin/bash
# SunLyvo Nexus 源码扫描（只读，不改任何文件）
THEME="/opt/1panel/www/sites/sunlyvo.com/index/wp-content/themes/sunlyvo-nexus"
SITE="/opt/1panel/www/sites/sunlyvo.com/index"
cd "$THEME" || { echo "主题目录不存在"; exit 1; }

SEP() { echo; echo "══════════ $1 ══════════"; echo; }

SEP "1. Git 分支与最近提交"
git rev-parse --abbrev-ref HEAD
git log --oneline -10
echo "--- 未提交改动 ---"
git status --short

SEP "2. 主题顶层目录"
ls -la

SEP "3. inc/ 目录树（两层）"
find inc -maxdepth 2 \( -type d -o -name '*.php' \) | sort

SEP "4. functions.php 的 \$slv_core_files"
grep -n -A 45 'slv_core_files' functions.php | head -60

SEP "5. templates/ 全部文件"
ls -la templates/

SEP "6. parts/ 与 patterns/"
ls -la parts/ patterns/

SEP "7. assets/css/ 全部文件"
ls -la assets/css/

SEP "8. assets/js/react/ 目录树（排除 node_modules/dist）"
find assets/js/react -maxdepth 3 \
  \( -name '*.ts' -o -name '*.tsx' -o -name '*.json' -o -name '*.js' \) \
  ! -path '*/node_modules/*' ! -path '*/dist/*' | sort

SEP "9. assets/js/react/package.json"
cat assets/js/react/package.json 2>/dev/null || echo "不存在"

SEP "10. assets/js/react/vite.config.ts"
cat assets/js/react/vite.config.ts 2>/dev/null || echo "不存在"

SEP "11. assets/templates/ 各行业模板完整度"
for d in assets/templates/*/; do
  echo "--- $d ---"
  ls "$d"
  for f in tokens.css components.css layout.css; do
    if [ -f "$d$f" ]; then
      echo "  $f: $(wc -l < "$d$f") 行"
    else
      echo "  $f: 缺失"
    fi
  done
  [ -d "$d/templates" ] && echo "  templates/: $(ls "$d/templates" | wc -l) 个文件"
  echo
done

SEP "12. src/（中台）目录树"
find src -maxdepth 3 -type d ! -path '*/node_modules/*' ! -path '*/.umi/*' ! -path '*/dist/*' | sort

SEP "13. src/package.json"
cat src/package.json 2>/dev/null || echo "不存在"

SEP "14. src/ 构建配置（判断 Umi 还是 Vite）"
for f in src/.umirc.ts src/config/config.ts src/config/routes.ts src/vite.config.ts src/tsconfig.json; do
  echo "--- $f ---"
  [ -f "$f" ] && head -60 "$f" || echo "不存在"
  echo
done

SEP "15. src/.env* 环境文件"
for f in src/.env src/.env.development src/.env.production \
         src/.env.production.subdomain src/.env.production.subdir; do
  echo "--- $f ---"
  [ -f "$f" ] && cat "$f" || echo "不存在"
  echo
done

SEP "16. src/src/pages/ 页面清单"
find src/src/pages -type f \( -name '*.tsx' -o -name '*.ts' \) ! -path '*/node_modules/*' | sort

SEP "17. 关键中台页面真实内容（每个只取前 60 行）"
for f in \
  src/src/pages/platform/dashboard/index.tsx \
  src/src/pages/platform/users/index.tsx \
  src/src/pages/platform/vendors/index.tsx \
  src/src/pages/platform/orders/index.tsx \
  src/src/pages/platform/settings/index.tsx \
  src/src/pages/platform/template/index.tsx \
  src/src/pages/user/login/index.tsx \
  src/src/app.tsx \
  src/src/services/api.ts; do
  echo "─── $f ───"
  [ -f "$f" ] && head -60 "$f" || echo "不存在"
  echo
done

SEP "18. inc/helpers-content.php 是否存在（上次交付）"
[ -f inc/helpers-content.php ] && { echo "存在"; wc -l inc/helpers-content.php; } || echo "不存在"

SEP "19. inc/template-parts/ 是否存在"
ls -la inc/template-parts/ 2>/dev/null || echo "不存在"

SEP "20. inc/modules/seo/redirects.php 是否存在"
[ -f inc/modules/seo/redirects.php ] && wc -l inc/modules/seo/redirects.php || echo "不存在"

SEP "21. inc/react-mounts.php 注册的 REST 端点"
grep -n 'register_rest_route' inc/react-mounts.php 2>/dev/null

SEP "22. inc/modules/ 所有 module.php"
find inc/modules -maxdepth 2 -name 'module.php' -o -name 'rest-api.php' | sort

SEP "23. inc/enqueue.php 条件加载逻辑"
head -180 inc/enqueue.php 2>/dev/null || echo "不存在"

SEP "24. 已部署路径"
ls -la "$SITE/admin/" 2>/dev/null | head -20 || echo "/admin/ 未部署"
echo
ls -la "$SITE/app/" 2>/dev/null | head -20 || echo "/app/ 未部署"

SEP "25. .gitignore"
cat .gitignore

SEP "26. debug.log 最近 40 行"
tail -40 "$SITE/wp-content/debug.log" 2>/dev/null || echo "无 debug.log"

SEP "27. 主题版本信息"
grep -m1 'Version:' style.css 2>/dev/null
grep -m1 'SLV_VERSION' inc/constants.php 2>/dev/null

echo
echo "══════════ 扫描完成 ══════════"