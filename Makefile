m:
	docker exec testTaskZhilfond_php-fpm php artisan migrate
o:
	docker exec testTaskZhilfond_php-fpm php artisan optimize:clear
