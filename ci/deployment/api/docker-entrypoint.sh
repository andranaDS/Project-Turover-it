#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ] || [ "$1" = 'crontab' ] || [ "$1" = 'fixtures' ]; then
	PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
	if [ "$APP_ENV" != 'prod' ]; then
		PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
	fi
	ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"

	mkdir -p var/cache var/log
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
	# setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX config/jwt

	if [ "$APP_ENV" != 'prod' ] && [ -f /certs/localCA.crt ]; then
		ln -sf /certs/localCA.crt /usr/local/share/ca-certificates/localCA.crt
		update-ca-certificates
	fi

	if [ "$APP_ENV" != 'prod' ]; then
		composer install
	fi

	if [ "$APP_ENV" == 'prod' ]; then
    rm -rf var/cache/* && php bin/console cache:warmup && php bin/console app:cache:annotations
	fi

	echo "Waiting for db to be ready..."
	ATTEMPTS_LEFT_TO_REACH_DATABASE=60
	until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
		sleep 1
		ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE-1))
		echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
	done

	if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
		echo "The db is not up or not reachable"
		exit 1
	else
	   echo "The db is now ready and reachable"
	fi

  if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
			bin/console doctrine:migrations:migrate --no-interaction
	fi

	# create a file to tell that the entrypoint finished
	touch /srv/api/healthy
fi

if [ "$1" = 'fixtures' ]; then
	bin/console doctrine:database:drop --force --connection=default --if-exists
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:migration:migrate -n
	bin/console fos:elastica:reset
	bin/console doctrine:fixtures:load -n --env=dev -v
	bin/console fos:elastica:populate -n
elif [ "$1" = 'crontab' ]; then
	# Never stop the container
	echo "Cron will start in 1 min"
	while true; do
		sleep 60
	  bin/console cron:run
	done
else
	exec "$@"
fi
