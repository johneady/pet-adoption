@servers(['web' => 'root@23.94.253.248 -p 22'])

@task('deploy')
    cd /home/john/web/demo.powerphpscripts.com/public_html
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html directory."

    rm -rf vendor/
    echo "Removed vendor/ directory."

    git pull origin main

    composer install --optimize-autoloader --no-dev

    php artisan migrate --force

    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    npm install

    npm run build

    rm -rf node_modules/
    echo "Removed node_modules/ directory."
@endtask