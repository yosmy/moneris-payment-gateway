# Test

docker network create backend

cd test

export UID
export GID
docker-compose \
-f docker/all.yml \
-p yosmy_moneris_gateway \
up -d \
--remove-orphans --force-recreate

docker exec -it yosmy_moneris_gateway_php sh
cd test
rm -rf var/cache/*

php bin/app.php /payment/gateway/moneris/add-card customer1 4242424242424242 12 14 099 1035
php bin/app.php /payment/gateway/moneris/execute-charge customer1 1WJoIQu4MaereKRPqC2Irr2M2 100 "Deposito" "Deposito"