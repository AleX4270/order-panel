#!/usr/bin/env bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE USER order_panel_testing WITH PASSWORD 'Iq621Ao?IP1L';
    CREATE DATABASE order_panel_testing;
    GRANT ALL PRIVILEGES ON DATABASE order_panel_testing TO order_panel_testing;
EOSQL