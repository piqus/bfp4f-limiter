#!/usr/bin/env
# Weapon Limiter (c) by piqus
# Switch to *limiter-pdo* instaed of *limiter-mongo* if you are using SQL DB as database service provider

while [ 1 = 1 ]; do
	if [ -e 'limiter.lock' ]; then
		exit
	else
		if [ -d 'logs/' ]; then			
			php limiter-console.php &>> "logs/$(date +%F).log"
		else
			mkdir 'logs'
		fi		
		sleep 60
	fi
done
