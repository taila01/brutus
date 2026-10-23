#!/usr/bin/env bash
set -euo pipefail
project_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
php_bin="${BRUTUS_PHP_BIN:-php}"
"$php_bin" "$project_root/scripts/verificar-php.php"
cd "$project_root"
exec "$php_bin" -d session.upload_progress.enabled=0 -S 127.0.0.1:8091 -t public scripts/router.php
