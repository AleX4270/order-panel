#!/usr/bin/env bash
set -e

"${psql[@]}" --dbname="$POSTGRES_DB" <<-'EOSQL'
    CREATE EXTENSION IF NOT EXISTS postgis;
EOSQL