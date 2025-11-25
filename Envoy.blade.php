@servers(['demo.powerphpscripts.com' => 'john@23.94.253.248 -p 22'])

@task('install', ['confirm' => true])
    cd /home/john/web/demo.powerphpscripts.com/public_html
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html directory."

    rm -rf pet-adoption
    echo "Removed existing pet-adoption directory."

    git clone https://github.com/johneady/pet-adoption

    cd pet-adoption
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption directory."

    composer install --optimize-autoloader --no-dev

    cp .env.example .env
    php artisan key:generate
    
    php artisan migrate:fresh --force
    php artisan db:seed --class=FirstInstallSeeder --force

    php artisan optimize

    npm install

    npm run build

    rm -rf node_modules/
    
    echo "Your application has been installed. Please update your .env file with the correct settings."

@endtask

@task('update')
    cd /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption
    echo "Inside /home/john/web/demo.powerphpscripts.com/public_html/pet-adoption directory."

    rm -rf vendor/
    echo "Removed vendor/ directory."

    git pull origin main

    composer install --optimize-autoloader --no-dev

    php artisan migrate --force

    php artisan optimize 
    
    npm install

    npm run build

    rm -rf node_modules/
    echo "Removed node_modules/ directory."
@endtask