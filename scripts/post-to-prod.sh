#!/bin/bash
# Sends a local post to production, published at once (--publish) or as a draft (--draft): one is required,
# so a send never goes live by default. A slug already there is updated (blog:manage import backs up the old
# text first); a published post stays published either way, so re-sending one is Bernard's call.
set -euo pipefail

usage="usage: scripts/post-to-prod.sh <post id> --publish|--draft [--force]"
id=${1:?$usage}
shift

# Only these go on: anything else would reach the server's shell unquoted
mode=""
force=""
for arg in "$@"; do
    case "$arg" in
        --publish|--draft)
            [ -z "$mode" ] || { echo "Choose one of --publish or --draft. $usage" >&2; exit 2; }
            mode=$arg ;;
        --force) force=" --force" ;;
        *) echo "$usage" >&2; exit 2 ;;
    esac
done
[ -n "$mode" ] || { echo "Say --publish (live at once, with its date) or --draft. $usage" >&2; exit 2; }
publish=""
[ "$mode" = --publish ] && publish=" --publish"

remote=${SINFAT_REMOTE:-ssh sinfat}
dir=${SINFAT_DIR:-/var/www/sinfat}

cd "$(dirname "$0")/.."

file=$(php artisan blog:manage export --id="$id" | sed -n 's/^Exported post #[0-9]* to //p')
[ -f "$file" ] || { echo "Export of post #$id failed" >&2; exit 1; }

# The file goes over stdin, so nothing is left behind on the server
$remote "cd $dir && php artisan blog:manage import --file=-$publish$force" < "$file"
