#!/bin/bash
# Sends a local post to production: created there as a draft, or updated if the slug exists
# (blog:manage import backs up the old text first). Publishing stays a click in the production admin.
set -euo pipefail

id=${1:?usage: scripts/post-to-prod.sh <post id> [--force]}
shift

remote=${SINFAT_REMOTE:-ssh sinfat}
dir=${SINFAT_DIR:-/var/www/sinfat}

cd "$(dirname "$0")/.."

file=$(php artisan blog:manage export --id="$id" | sed -n 's/^Exported post #[0-9]* to //p')
[ -f "$file" ] || { echo "Export of post #$id failed" >&2; exit 1; }

# The file goes over stdin, so nothing is left behind on the server
$remote "cd $dir && php artisan blog:manage import --file=- $*" < "$file"
