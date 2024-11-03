DOCKER_COMPOSE=$(if $(shell which docker-compose),docker-compose,docker compose)

all: build run

build:
	$(DOCKER_COMPOSE) build --no-cache --build-arg hostUID=`id -u` --build-arg hostGID=`id -g` web

start: run
run:
	$(DOCKER_COMPOSE) up -d --remove-orphans web

stop:
	$(DOCKER_COMPOSE) kill

destroy:
	$(DOCKER_COMPOSE) down --remove-orphans

logs:
	$(DOCKER_COMPOSE) logs -f web

shell:
	$(DOCKER_COMPOSE) exec --user www-data web bash

root:
	$(DOCKER_COMPOSE) exec web bash
