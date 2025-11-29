@servers(['demo.powerphpscripts.com' => 'john@23.94.253.248 -p 22'])

@task('install', ['confirm' => true])
    cd /home/john/web/demo.powerphpscripts.com/public_html
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html directory."

    rm -rf pet-adoption
    echo "Removed existing pet-adoption directory."

    git clone https://github.com/johneady/pet-adoption

    cd pet-adoption
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption directory."

    composer install --optimize-autoloader

    cp .env.example .env
    php artisan key:generate

    php artisan storage:link

    php artisan config:clear
    php artisan optimize

    npm install

    npm run build

    rm -rf node_modules/

    echo "Your application has been installed. Please update your .env file with the correct settings, then run the
    'vendor/bin/envoy run seed' command to finalize the setup."
@endtask

@task('seed', ['confirm' => true])
    cd /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption directory."

    php artisan migrate:fresh --force
    php artisan db:seed --force

    echo "Database has been seeded."
@endtask


@task('update')
    cd /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption directory."

    rm -rf vendor/
    echo "Removed vendor/ directory."

    git pull origin main

    composer install --optimize-autoloader --no-dev

    php artisan migrate --force
    php artisan config:clear
    php artisan optimize

    npm install

    npm run build

    rm -rf node_modules/
    echo "Removed node_modules/ directory."
@endtask
