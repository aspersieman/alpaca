#!/usr/bin/env sh

sleep $((60 - $(date +%S) )) && date && php /app/artisan schedule:run
