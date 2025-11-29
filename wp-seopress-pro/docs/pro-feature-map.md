# SEOPress Pro feature map

This document highlights major PRO-only surfaces and how they tie into shared SEOPress code.

## Custom post types
- **Schemas (`seopress_schemas`)** — non-public CPT that exposes schema builder entries with dedicated capabilities (edit/read/publish/delete variants). Registered on `admin_init` and uses a meta-cap mapping for granular access control.【F:inc/admin/schemas/schemas.php†L90-L150】
- **404/Redirections (`seopress_404`)** — non-public CPT for individual redirection rules, including custom capabilities and taxonomy support for categories. Meta-cap mapping enforces edit/delete/read rules for private entries.【F:inc/admin/redirections/redirections.php†L110-L184】
- **Broken Links Bot (`seopress_bot`)** — non-public CPT used by the crawling bot UI; disallows creation via capability override and removes inline/bulk edit actions, replacing the edit link with the source post URL for remediation.【F:inc/admin/bot/bot.php†L40-L96】

## Scheduled tasks
`seopress_pro_cron()` seeds recurring events for key PRO services:
- 404 log cleanup (`seopress_404_cron_cleaning`, daily)
- GA and Matomo dashboard widgets (`seopress_google_analytics_cron`, `seopress_matomo_analytics_cron`, hourly)
- PageSpeed cache refresh (`seopress_page_speed_insights_cron`, daily)
- 404 email alerts (`seopress_404_email_alerts_cron`, weekly)
- GSC insights pull (`seopress_insights_gsc_cron`, daily)
- SEO alerts (`seopress_alerts_cron`, twice daily)
These schedules run only when SEOPress Free is present (autoloaded before Kernel boot).【F:seopress-pro.php†L35-L138】

## REST API endpoints
PRO registers additional REST routes under `seopress/v1` via `ExecuteHooks` classes:
- `GET /page-speed` returns cached PageSpeed results for desktop/mobile; restricted to `manage_options` users and validates the device parameter.【F:src/Actions/Api/PageSpeed/GetPageSpeedReport.php†L11-L59】
- `GET /redirections` reads `seopress_404` posts with optional filters (id, enabled, type) and requires the `read_redirection` capability, linking the endpoint to the redirection CPT permissions.【F:src/Actions/Api/Redirections.php†L11-L120】

## Free/PRO coupling
- The PRO loader includes the free plugin autoloader and shared functions before booting the PRO kernel, and deactivates itself if SEOPress Free is not active. This ensures container services and shared hooks from the free codebase are available for the above features.【F:seopress-pro.php†L35-L138】
