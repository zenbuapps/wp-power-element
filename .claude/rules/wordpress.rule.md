---
globs: "**/*.php"
---

# WordPress / PHP 開發規則

## 語言與風格
- PHP 8.1+，`declare(strict_types=1)`
- Namespace: `J7\PowerElement`，PSR-4 autoloading（`inc/classes/`）
- 遵循 WPCS 編碼標準（`phpcs.xml`）

## Singleton Pattern
- Plugin 和 Bootstrap 使用 `\J7\WpUtils\Traits\SingletonTrait`
- 存取: `ClassName::instance()`

## Elementor Widget 開發
- 繼承 `\Elementor\Widget_Base`
- Widget name 前綴: `pe_`
- 使用 PHP 8.1 Enum (`EField`) 管理欄位設定
- Controls 分為 Content Tab 和 Style Tab
- Repeater 用於動態數量的子項目

## Dynamic Tags
- 繼承 `BaseBoundPostTag`（自訂 abstract class）
- 共用 `bound_post_id` control
- Group name: `power-element`
- 僅在 Elementor Pro 啟用時註冊（`class_exists` guard）

## Shortcode 開發
- 實作 `IShortcode` 介面
- 在 `Shortcodes\Register` 集中註冊
- Callback 為 static method

## Script 註冊
- Bootstrap 管理所有前端元件的 enqueue
- Script handle: `power-element-{ComponentName}`
- Strategy: `async`
- Hook: `wp_enqueue_scripts` priority 200
