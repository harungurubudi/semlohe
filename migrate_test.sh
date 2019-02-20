#!/bin/bash

php vendor/bin/phinx migrate -e testing
php vendor/bin/phinx seed:run -e testing