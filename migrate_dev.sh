#!/bin/bash

php vendor/bin/phinx migrate -e development
php vendor/bin/phinx seed:run -e development