#!/usr/bin/env
#
# Weapon limiter continous loop for BFP4F admin panel project.
# 
# @author:  piqus
# @license: Private
# 
############################# That's all folks;

while [ 1 = 1 ]; do
	if [ -e 'limiter.lock' ]; then
		exit
	else
		if [ -d 'logs/' ]; then			
			php limiter.php &>> "logs/$(date +%F).log"
		else
			mkdir 'logs'
		fi		
		sleep 60
	fi
done