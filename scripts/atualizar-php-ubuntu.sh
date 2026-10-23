#!/usr/bin/env bash
# Atualiza a instalação PHP 8.4 existente no Ubuntu e reinicia serviços ativos.
set -euo pipefail
if [[ $EUID -ne 0 ]]; then
    printf '%s\n' 'Execute este script com sudo no terminal do computador.' >&2
    exit 1
fi
project_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
if [[ ! -d /etc/php/8.4 || ! -x /usr/bin/php8.4 ]]; then
    printf '%s\n' 'Este script é específico para a instalação PHP 8.4 existente no Ubuntu.' >&2
    exit 1
fi
command -v apt-get >/dev/null
command -v phpenmod >/dev/null
apt-get update
mapfile -t php_packages < <(dpkg-query -W -f='${binary:Package} ${db:Status-Abbrev}\n' 'php8.4*' 'libapache2-mod-php8.4' 2>/dev/null | awk '$2 == "ii" {print $1}')
if [[ ${#php_packages[@]} -eq 0 ]]; then
    printf '%s\n' 'Nenhum pacote PHP 8.4 instalado foi encontrado.' >&2
    exit 1
fi
apt-get install --only-upgrade -y "${php_packages[@]}"
/usr/bin/php8.4 "$project_root/scripts/verificar-php.php"
install -m 0644 "$project_root/config/php/security.ini" /etc/php/8.4/mods-available/brutus-security.ini
phpenmod -v 8.4 -s ALL brutus-security
if systemctl is-active --quiet php8.4-fpm; then
    /usr/sbin/php-fpm8.4 -t
    systemctl restart php8.4-fpm
    systemctl is-active --quiet php8.4-fpm
fi
if systemctl is-active --quiet apache2; then
    apache2ctl configtest
    systemctl restart apache2
    systemctl is-active --quiet apache2
fi
/usr/bin/php8.4 -r 'if (ini_get("session.upload_progress.enabled")) {fwrite(STDERR, "Configuração de sessões não aplicada.\n"); exit(1);} echo "session.upload_progress.enabled=0 confirmado no CLI.\n";'
printf '%s\n' 'Atualização concluída. Se houver servidores php -S antigos, encerre-os e inicie novamente com bash scripts/servir.sh.'
