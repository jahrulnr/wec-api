IMAGE_NAME=wec-api

.PHONY: build
build-app:
	docker build . -t ${IMAGE_NAME}:dev -f config/docker/Dockerfile --progress plain

.PHONY: network
network:
	if [ ! -z "$(docker network ls | grep services)" ]; then \
		docker network create services; \
	fi

.PHONY: api-switch
api-switch:
	@echo "API Switcher Control"
	@echo "-------------------"
	@echo "1) Enable API Switcher (mock mode)"
	@echo "2) Enable API Switcher (real API mode)"
	@echo "3) Disable API Switcher"
	@echo "4) Show current API Switcher status"
	@echo "5) List API criteria"
	@echo "6) Clear API logs"
	@echo "7) Show API endpoint status"
	@read -p "Choose an option: " option; \
	case $$option in \
		1) docker exec -it wec-api bash -c "cd /apps && php artisan config:set API_SWITCHER_ENABLED=true API_SWITCHER_DEFAULT=mock"; \
			echo "API Switcher enabled in MOCK mode"; \
		;; \
		2) docker exec -it wec-api bash -c "cd /apps && php artisan config:set API_SWITCHER_ENABLED=true API_SWITCHER_DEFAULT=real"; \
			echo "API Switcher enabled in REAL API mode"; \
		;; \
		3) docker exec -it wec-api bash -c "cd /apps && php artisan config:set API_SWITCHER_ENABLED=false"; \
			echo "API Switcher disabled"; \
		;; \
		4) docker exec -it wec-api bash -c "cd /apps && php artisan config:get API_SWITCHER_ENABLED API_SWITCHER_DEFAULT"; \
		;; \
		5) docker exec -it wec-api bash -c "cd /apps && php artisan api:list-criteria"; \
			echo ""; \
		;; \
		6) docker exec -it wec-api bash -c "cd /apps && php artisan api:clear-logs"; \
			echo "API logs cleared"; \
		;; \
		7) read -p "Enter path (without /api/ prefix): " path; \
			read -p "Enter method (GET, POST, etc): " method; \
			docker exec -it wec-api bash -c "cd /apps && php artisan api:check-endpoint $$path $$method"; \
		;; \
		*) echo "Invalid option"; \
		;; \
	esac

.PHONY: api-mock
api-mock:
	@docker exec -it wec-api bash -c "cd /apps && php artisan config:set API_SWITCHER_ENABLED=true API_SWITCHER_DEFAULT=mock"
	@echo "API Switcher enabled in MOCK mode"

.PHONY: api-real
api-real:
	@docker exec -it wec-api bash -c "cd /apps && php artisan config:set API_SWITCHER_ENABLED=true API_SWITCHER_DEFAULT=real"
	@echo "API Switcher enabled in REAL API mode"

.PHONY: api-off
api-off:
	@docker exec -it wec-api bash -c "cd /apps && php artisan config:set API_SWITCHER_ENABLED=false"
	@echo "API Switcher disabled"

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