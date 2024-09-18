api_bins := "./api/vendor/bin/"

phpstan := api_bins + "phpstan"
php_cs_fixer := api_bins + "php-cs-fixer"
phpunit := api_bins + "phpunit"

symfony_server := "symfony --dir=api"
docker_compose := "docker compose"

setup:
    @composer install
    cd twitter-scrapper && \
        uv venv && \
        source .venv/bin/activate && \
        uv pip sync requirements.txt
    cd infra && bun install
    just decrypt-secrets

decrypt-secrets:
    sops --decrypt --input-type binary --output-type binary secrets.json | tee .env

encrypt-secrets:
    sops -e --input-type binary --output-type binary .env | tee secrets.json

start:
    {{symfony_server}} local:server:start -d --no-tls --port 8080
    {{docker_compose}} up -d --remove-orphans

stop:
    {{symfony_server}} local:server:stop
    {{docker_compose}} down

lint: phpstan

phpstan:
    @{{phpstan}} analyze -c api/phpstan.dist.neon

format: php-cs-fixer biome

biome:
    biome format --fix

php-cs-fixer:
    @{{php_cs_fixer}} fix --config api/.php-cs-fixer.dist.php

test: phpunit

phpunit:
  @{{phpunit}} --coverage-html api/coverage -c api/phpunit.dist.xml
