# SEOPress Pro vs Free Diff

## Method
- Compared `wp-seopress-pro` against `wp-seopress` by computing SHA-256 hashes for every file and aligning them by relative path.
- Recorded matches where both the path and hash are identical, and noted files that share hashes across different locations.
- Summaries below reflect snapshot counts from the current repository state.

## Duplicate files (safe to skip copying)
- **Same path & same hash:** 771 files are identical between Pro and Free; 769 are under `vendor/`, with the remainder being placeholder indexes in `inc/` and `languages/`.
- **Hash matches in different paths:** 6 unique hashes appear in both products but at different locations (e.g., placeholder `index.php` files, minifier binaries, Google `SECURITY.md` and `LICENSE` documents).
- **Top duplicate vendor packages (identical content):**
  - `vendor/phpseclib/phpseclib` — 342 files
  - `vendor/monolog/monolog` — 120 files
  - `vendor/google/auth` — 50 files
  - `vendor/guzzlehttp/guzzle` — 47 files
  - `vendor/guzzlehttp/psr7` — 35 files
  - `vendor/google/apiclient` — 27 files
  - `vendor/guzzlehttp/promises` — 20 files
  - `vendor/matthiasmullie/minify` — 18 files
  - `vendor/psr/log` — 14 files
  - `vendor/paragonie/constant_time_encoding` — 14 files

### Do-not-copy list (identical or superseded)
- `vendor/` packages listed above plus other Composer-managed dependencies with matching paths.
- Placeholder `index.php` files shared between products (e.g., `inc/index.php`, `languages/index.php`, asset and function subfolders).
- Shared utility binaries and docs: `vendor/bin/minifyjs`, `vendor/bin/minifycss`, Google `SECURITY.md`/`LICENSE` files that already exist in Free.

## Pro-only files (candidates for migration)
- **Volume:** 1,486 files exist only in Pro (versus 497 only in Free).
- **Top-level Pro-only locations:**
  - `vendor/` — 995 files (Pro-specific dependencies)
  - `inc/` — 172 files (130 admin-facing, 42 functional extensions)
  - `src/` — 160 files (services, schemas, actions, tags)
  - `templates/` — 73 files (stop words and JSON schema templates)
  - `assets/` — 59 files (Pro styles/scripts)
  - `public/` — 23 files (front-facing assets/endpoints)
  - Plus Pro bootstrap files `seopress-pro.php` and `seopress-pro-functions.php`
- **Detail by subsystem:**
  - `inc/admin/` contains 130 Pro-only admin controllers and settings screens.
  - `inc/functions/` adds 42 Pro-only feature helpers.
  - `src/Services/`, `src/JsonSchemas/`, `src/Actions/`, `src/Tags/` host most of the 160 Pro-only PHP classes.
  - `templates/stop-words/` (38 files) and `templates/json-schemas/` (35 files) provide Pro-only template content.
