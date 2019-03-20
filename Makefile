container=php

start:
	docker-compose up

build:
	docker-compose build

build-clean:
	docker-compose build --no-cache

down:
	docker-compose down

space:
	docker rmi $$(docker images --filter "dangling=true" -q --no-trunc); docker rm $$(docker ps -qa --no-trunc --filter "status=exited"); docker volume rm $$(docker volume ls -qf dangling=true);

