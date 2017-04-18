#!/bin/bash

# wait for mysql to be ready
while ! nc db 3306;
    do
        sleep 1;
    done;
make load_db
php -S 0.0.0.0:8000 -t /code
