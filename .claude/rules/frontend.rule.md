---
globs: "js/src/**/*.{ts,tsx,scss,css}"
---

# Frontend 開發規則

## 技術棧
- React 18 + TypeScript（ButtonA, ButtonB 元件）
- jQuery（pe_interactive_image 互動邏輯）
- Vite 5 + vite-plugin-optimizer（外部化 jQuery, @wordpress/element）
- TailwindCSS 3 + SCSS
- @wordpress/element 作為 React wrapper

## Multi-Entry Build
- 每個元件獨立 entry point: `js/src/components/{Name}/index.tsx`
- 輸出: `js/dist/components/{Name}/index.js` + `index.css`
- CSS 分離: enabled

## Import Alias
- `@/` → `js/src/`
- 使用 `vite-tsconfig-paths` 自動解析

## WordPress 外部化
- `jquery` → `window.jQuery`
- `@wordpress/element` → `window.wp.element`
- 透過 `vite-plugin-optimizer` 實現

## TailwindCSS 設定
- `preflight: false`（避免與 Elementor 衝突）
- 自訂 screens: sm(576), md(810), lg(1080), xl(1280), xxl(1440)
- 自訂 animation: `tw-pulse`（避免命名衝突）
- Blocklist: hidden, columns-1, fixed, block, inline

## CSS Naming
- 元件 class: `.pe_interactive_image`, `.button-a`, `.button-b`
- TailwindCSS utilities 加 `tw-` prefix 避免衝突
