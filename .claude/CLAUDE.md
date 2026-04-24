# Power Element

> **Last synced:** 2026-04-09 | **Version:** 0.0.10 | **PHP Namespace:** `J7\PowerElement`

## 1. What This Plugin Does

自製 Shortcode 及 Elementor Widget 集合外掛。提供互動式圖片 Widget（Interactive Image）——在背景圖上放置可互動的圖標，hover/click 顯示綁定文章的資訊卡片，側邊顯示列表。

**Core capabilities:**
- **Interactive Image Widget**: Elementor widget，背景圖 + 浮動圖片 + 互動圖標 + 資訊卡 + 列表面板
- **Elementor Dynamic Tags**: 綁定文章的 title / excerpt / URL / author（需 Elementor Pro）
- **React Components**: ButtonA / ButtonB 示範元件
- **Shortcode**: `pe_interactive_image`（placeholder，主要用 Elementor widget）

---

## 2. Tech Stack

| Layer | Technology |
|-------|-----------|
| PHP | 8.1+, WordPress 5.7+ |
| Frontend | React 18 + TypeScript, jQuery (interactive image) |
| Build | Vite 5 + vite-plugin-optimizer |
| CSS | TailwindCSS 3 + SCSS |
| Elementor | Widget API + Dynamic Tags API |
| WP Env | wp-env (WP 6.8, PHP 8.2, port 8899) |

---

## 3. Architecture

```
J7\PowerElement\
├── Plugin                              # Singleton + PluginTrait
├── Bootstrap                           # 元件註冊 & script enqueue
├── Shortcodes\
│   ├── Register                        # Shortcode 集中註冊
│   ├── InteractiveImage\InteractiveImage  # pe_interactive_image callback
│   └── Shared\Interfaces\IShortcode   # Shortcode 介面
└── ElementorWidgets\
    ├── Register                        # Widget & Dynamic Tag 註冊
    ├── InteractiveImage\
    │   ├── InteractiveImage            # Widget 主類別
    │   ├── Styles                      # Style tab controls
    │   └── Shared\Enums\EField        # PHP 8.1 Enum for field config
    └── Shared\
        ├── DynamicTags\
        │   ├── BaseBoundPostTag        # Abstract base (bound_post_id)
        │   ├── PostTitle               # power-post-title
        │   ├── PostExcerpt             # power-post-excerpt
        │   ├── PostUrl                 # power-post-url
        │   └── AuthorName             # power-author-name
        └── Utils\ControlUtils          # Elementor control helpers
```

---

## 4. Interactive Image Widget

### Elementor Controls (Content Tab)

**Layout Section:**
- `background_image` (MEDIA) — 背景圖
- `background_image_width` (SLIDER) — 0-100%

**Float Images (Repeater):**
- 每個項目: title, image, hover_image, position_top/left, image_width

**Icons (Repeater):**
- 每個項目: title, post_id (綁定文章), icon, position_top/left, icon_width, icon_color
- `card_display_items` (SELECT2) — featured_image, title, excerpt, author
- `card_meta_key` — 自訂 meta key（逗號分隔）
- `card_content` (WYSIWYG) — 自訂 HTML

**List Items Section:**
- `is_unique` (SWITCHER) — 隱藏重複 post_id
- `list_item_display_items`, `list_item_meta_key`, `list_item_content`

### Frontend Behavior (jQuery)
- Hover 列表項目 → 顯示對應卡片 & 圖標高亮
- 點擊圖標 → Toggle 卡片顯示
- Hover 浮動圖片 → 加 `hover` class
- 列表高度動態匹配圖片高度

---

## 5. Dynamic Tags (Elementor Pro)

| Tag Name | Category | Output |
|----------|----------|--------|
| `power-post-title` | TEXT | `get_the_title(bound_post_id)` |
| `power-post-excerpt` | TEXT | Post excerpt (可選 post_content) |
| `power-post-url` | URL | `get_permalink(bound_post_id)` |
| `power-author-name` | TEXT | Author display_name |

Group: `power-element`，僅在 Elementor Pro 存在時註冊。

---

## 6. Frontend Components (React)

透過 Bootstrap 註冊，每個元件獨立 entry point：

| Component | Selector | Stack |
|-----------|----------|-------|
| `ButtonA` | `.button-a` | React + @wordpress/element + CSS Modules |
| `ButtonB` | `.button-b` | React + @wordpress/element + SCSS |
| `pe_interactive_image` | `.pe_interactive_image` | jQuery + vanilla JS |

Script handle 格式: `power-element-{ComponentName}`
Strategy: `async` loading

---

## 7. Commands

```bash
# Development
pnpm dev                    # Vite dev server
pnpm build                  # Build → js/dist/

# Code Quality
pnpm lint                   # ESLint + PHPCBF
pnpm lint:fix               # Auto-fix
pnpm format                 # Prettier

# Release
pnpm release                # Patch release
pnpm zip                    # Create zip
pnpm sync:version           # Sync version

# Setup
pnpm bootstrap              # pnpm install + composer install
```

---

## 8. Dependencies

**PHP:** `j7-dev/wp-utils` ^0.3
**JS (key):** @wordpress/element, react 18, vite 5, tailwindcss 3

---

## 9. Configuration Notes

- TailwindCSS `preflight` disabled（避免與 Elementor 衝突）
- 自訂 animation `tw-pulse`（避免與 Elementor pulse 衝突）
- Blocklist classes: `hidden`, `columns-1`, `fixed`, `block`, `inline`（避免與 WP 衝突）
- No WooCommerce integration (commented out)
- No custom wp_options settings
