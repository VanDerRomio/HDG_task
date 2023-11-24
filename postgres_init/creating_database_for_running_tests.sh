#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL

CREATE USER postgres_test WITH ENCRYPTED PASSWORD 'postgres_test';
CREATE DATABASE postgres_test;
GRANT ALL ON DATABASE postgres_test TO postgres_test;
ALTER DATABASE postgres_test OWNER TO postgres_test;
GRANT USAGE, CREATE ON SCHEMA PUBLIC TO postgres_test;

EOSQL
