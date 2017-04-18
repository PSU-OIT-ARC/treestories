.DEFAULT_GOAL := help
.PHONY = help init

DB_NAME = treestories
DB_HOST = db
SCHEMA_DUMP = "./treestories.sql"


help:
		@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST)\
		| sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'


load_db:  ## Create DB schema
		mysql -h$(DB_HOST) -uroot $(DB_NAME) < $(SCHEMA_DUMP)

test:  ## Run tests
		@echo "Why aren't there any tests?"

