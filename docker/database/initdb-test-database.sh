#!/usr/bin/env bash
set -e

"${psql[@]}" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE USER order_panel_testing WITH PASSWORD 'Iq621Ao?IP1L';
    CREATE DATABASE order_panel_testing;
    GRANT ALL PRIVILEGES ON DATABASE order_panel_testing TO order_panel_testing;
EOSQL

"${psql[@]}" --dbname="order_panel_testing" <<-'EOSQL'
    GRANT ALL ON SCHEMA public TO order_panel_testing;
    CREATE EXTENSION IF NOT EXISTS postgis;
EOSQL