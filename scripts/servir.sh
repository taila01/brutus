#!/usr/bin/env bash
set -euo pipefail
project_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
php_bin="${BRUTUS_PHP_BIN:-php}"
"$php_bin" "$project_root/scripts/verificar-php.php"
cd "$project_root"
# O banco local pode estar parado após reiniciar o computador.
connection_mode="$("$php_bin" -r '$path=$argv[1]; $config=is_file($path) ? require $path : []; echo $config["connection"] ?? "local";' "$project_root/.env.local.php")"
if [[ "$connection_mode" == local ]] && command -v docker >/dev/null 2>&1; then
    if container_state="$(docker inspect --format '{{.State.Status}}' brutus-local-db 2>/dev/null)"; then
        if [[ "$container_state" == exited || "$container_state" == created ]]; then
            printf '%s\n' 'Iniciando o banco local existente do Brutus...'
            docker start brutus-local-db >/dev/null
        fi
        for ((attempt=0; attempt<20; attempt++)); do
            if "$php_bin" scripts/testar-conexao.php >/dev/null 2>&1; then break; fi
            sleep 0.5
        done
    fi
fi
# Interrompe a inicialização com um diagnóstico se o banco não responder.
"$php_bin" scripts/testar-conexao.php
exec "$php_bin" -d session.upload_progress.enabled=0 -S 127.0.0.1:8091 -t public scripts/router.php
