#!/bin/sh
cp /home/frontend/secrets/.env /home/frontend/actions-runner/_work/flowup-front-end/flowup-front-end/ip2-frontend/.env
mkdir ip2-frontend/letsencrypt/
cp /home/frontend/secrets/fullchain.pem /home/frontend/actions-runner/_work/flowup-front-end/flowup-front-end/ip2-frontend/letsencrypt/fullchain.pem
cp /home/frontend/secrets/privkey.pem /home/frontend/actions-runner/_work/flowup-front-end/flowup-front-end/ip2-frontend/letsencrypt/privkey.pem
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
docker-compose exec -T app php artisan storage:link
docker-compose exec -T app php artisan key:generate
docker-compose exec -T app php artisan config:cache
