#!/bin/bash
if [ -n "$PORT" ]; then
  sed -i "s/80/$PORT/g" /etc/apache2/sites-enabled/000-default.conf /etc/apache2/ports.conf
fi
exec apache2-foreground