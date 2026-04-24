# E2E Tests (Placeholder)

此目錄目前為空骨架，僅供 CI pipeline 的 `working-directory: tests/e2e`
與 playwright install step 不炸。

未來補 Playwright e2e 測試時，請參考姊妹 repo（例如 power-webinar、power-checkout）
的 `tests/e2e/` 結構：

- `package.json` — 宣告 `@playwright/test` 依賴
- `playwright.config.ts` — Playwright 設定（baseURL、locale、timezone）
- `global-setup.ts` / `global-teardown.ts` — auth state 準備與清理
- `helpers/` — API client、admin setup、auth bypass 工具
- `01-admin/` — 後台 API/UI 測試
- `02-frontend/` — 前台流程測試
- `03-integration/` — 跨層整合測試
