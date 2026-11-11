set dotenv-load := true

default:
    @just --list

# dev — start Vite dev server
dev:
    npm run dev

# build — production build
build:
    npm run build

# serve — start Laravel dev server
serve:
    php artisan serve

# migrate — run database migrations
migrate:
    php artisan migrate

# fresh — fresh migration with seeders
fresh:
    php artisan migrate:fresh --seed

# test — run PHPUnit tests
test:
    php artisan test

# tinker — open Laravel tinker REPL
tinker:
    php artisan tinker

# deploy — deploy to production (requires SSH config for 'sinfat' host)
deploy:
    ssh sinfat "bash /var/www/sinfat/scripts/deploy.sh"

# ssh — open SSH session to production server
ssh:
    ssh sinfat

# post-to-prod: send local post <id> to production as a draft (updates the post with the same slug)
post-to-prod id *args:
    bash scripts/post-to-prod.sh {{id}} {{args}}
