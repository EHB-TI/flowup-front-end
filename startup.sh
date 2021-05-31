#!/bin/sh
cp /home/frontend/secrets/.env /home/frontend/actions-runner/_work/flowup-front-end/flowup-front-end/ip2-frontend/.env
cd ip2-frontend/
composer install
npm i
npm run dev
cd ..
rm -rf /home/frontend/actions-runner/_work/flowup-front-end/flowup-front-end/ip2-frontend/vendor/vladimir-yuldashev
cp -r /home/frontend/vlad/vladimir-yuldashev /home/frontend/actions-runner/_work/flowup-front-end/flowup-front-end/ip2-frontend/vendor/
chmod -R +x /home/frontend/actions-runner/_work/flowup-front-end/flowup-front-end
docker-compose build
docker-compose down
docker-compose up -d
docker-compose exec -T app php artisan migrate
docker-compose exec -T app php artisan key:generate
docker-compose exec -T app php artisan config:cache
