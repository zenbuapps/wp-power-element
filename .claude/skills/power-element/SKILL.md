---
name: power-element
description: Power Element — 自製 Shortcode & Elementor Widget 外掛開發指引。互動式圖片 Widget、Dynamic Tags、React 元件。使用 /power-element 觸發。
---

# Power Element SKILL

## Quick Facts
- **Plugin:** Power Element v0.0.10
- **Namespace:** `J7\PowerElement`
- **PHP:** 8.1+ | **WP:** 5.7+
- **Frontend:** React 18 + jQuery + Vite 5
- **Elementor:** Widget API + Dynamic Tags

## Core Feature: Interactive Image Widget

背景圖上的互動式圖標地圖，點擊/hover 圖標顯示綁定文章的資訊卡。

### Widget 結構
```
InteractiveImage Widget
├── Content Tab
│   ├── Layout: background_image, width
│   ├── Float Images (Repeater): 浮動裝飾圖
│   ├── Icons (Repeater): 互動圖標 + 綁定 post_id
│   └── List Items: 側邊列表設定
└── Style Tab
    ├── Layout: bg width, segment gap
    ├── Card: title/excerpt typography & color
    ├── Icon: opacity
    └── List: padding, margin, typography
```

### 互動行為
- Hover 列表項 → 對應圖標高亮 + 卡片顯示
- Click 圖標 → Toggle 卡片
- 資料來源: WordPress 文章（title, excerpt, featured_image, author, custom meta）

## Dynamic Tags (Elementor Pro Only)
綁定到特定 post_id 的動態內容標籤：
- `power-post-title`, `power-post-excerpt`, `power-post-url`, `power-author-name`
- 所有繼承自 `BaseBoundPostTag` abstract class

## Architecture
```
Plugin (Singleton) → Bootstrap
  ├── ElementorWidgets\Register
  │   ├── InteractiveImage widget
  │   └── 4 Dynamic Tags
  ├── Shortcodes\Register
  │   └── pe_interactive_image (placeholder)
  └── Frontend Components
      ├── ButtonA (React)
      ├── ButtonB (React)
      └── pe_interactive_image (jQuery)
```

## Development
```bash
pnpm dev       # Vite dev server
pnpm build     # Build → js/dist/
pnpm lint      # ESLint + PHPCBF
```

## Key Design Decisions
- Elementor widget 是主要功能，shortcode 只是 placeholder
- jQuery 用於互動圖片（DOM 操作為主），React 用於獨立元件
- TailwindCSS preflight disabled 避免 Elementor 衝突
- PHP 8.1 Enum (`EField`) 管理 widget 欄位設定
