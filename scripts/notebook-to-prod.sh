#!/bin/bash
# Sends a local notebook snapshot to production as it is stored here (notebook:manage import made it).
# Production refuses one that links a snapshot it lacks, and replacing a stamp without --force.
set -euo pipefail

stamp=${1:?usage: scripts/notebook-to-prod.sh <stamp> [--force]}
shift

# Only --force goes on: anything else would reach the server's shell unquoted
case "${1:-}" in
    --force) force=" --force" ;;
    "") force="" ;;
    *) echo "usage: scripts/notebook-to-prod.sh <stamp> [--force]" >&2; exit 2 ;;
esac
[ $# -le 1 ] || { echo "usage: scripts/notebook-to-prod.sh <stamp> [--force]" >&2; exit 2; }

remote=${SINFAT_REMOTE:-ssh sinfat}
dir=${SINFAT_DIR:-/var/www/sinfat}

cd "$(dirname "$0")/.."

# Checked here first, so an unknown stamp stops before anything reaches the server
php artisan notebook:manage export --stamp="$stamp" > /dev/null

# The page goes over stdin, so nothing is left behind on the server
php artisan notebook:manage export --stamp="$stamp" \
    | $remote "cd $dir && php artisan notebook:manage import --prepared --stamp=$stamp --file=-$force"
