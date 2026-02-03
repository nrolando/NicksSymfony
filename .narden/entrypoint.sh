#!/bin/bash
set -e

SSL_DIR="/etc/apache2/ssl"
KEY="$SSL_DIR/local.key"
CRT="$SSL_DIR/local.crt"

if [ ! -f "$KEY" ] || [ ! -f "$CRT" ]; then
  echo "Generating self-signed SSL certificate..."

  mkdir -p "$SSL_DIR"

  openssl req -x509 -nodes -days 365 \
    -newkey rsa:2048 \
    -keyout "$KEY" \
    -out "$CRT" \
    -subj "/C=US/ST=Local/L=Local/O=Dev/OU=Dev/CN=localhost" \
    -addext "subjectAltName=DNS:localhost,IP:127.0.0.1"
else
  echo "SSL certificate already exists."
fi

exec "$@"
