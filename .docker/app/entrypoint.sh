#!/bin/bash

composer install -n
php vendor/bin/codecept run
