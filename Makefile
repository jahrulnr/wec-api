IMAGE_NAME=wec-api

.PHONY: build
build-app:
	docker build . -t ${IMAGE_NAME}:dev -f config/docker/Dockerfile --progress plain

build-arm:
	docker build . -t ${IMAGE_NAME}:dev -f config/docker/Dockerfile --progress plain --platform arm64 --build-arg "DOCKER_ENV=production"

.PHONY: network
network:
	if [ ! -z "$(docker network ls | grep services)" ]; then \
		docker network create services; \
	fi

.PHONY: run
run: build-app
	@make network
	docker compose -f config/docker/compose.yml up -d
logs: run
	docker compose -f config/docker/compose.yml logs -f

.PHONY: stop
stop: 
	docker compose -f config/docker/compose.yml down --remove-orphans

.PHONY: shell
shell:
	docker exec -it wec-api bash

.PHONY: import-db
import-db:
	docker exec -i database mysql -proot wec-api -e "DROP database wec-api; CREATE database wec-api"
	zcat ./config/dump/*.sql.gz | docker exec -i database mysql -proot wec-api

# PRODUCTION
.PHONY: build-prod
build-prod:
	docker build . -t ${IMAGE_NAME} -f config/docker/Dockerfile --progress plain --build-arg "DOCKER_ENV=production"
	docker save ${IMAGE_NAME}:latest | gzip > ${IMAGE_NAME}.tar.gz

update-composer-files:
	docker cp wec-api:/apps/vendor web/