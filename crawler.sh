#!/bin/bash
URL="$1"
SELECTOR="$2"

echo "Ejecutando crawler en $(date)" >> /tmp/crawl_a_site.log
echo "URL: $URL" >> /tmp/crawl_a_site.log
echo "Selector: $SELECTOR" >> /tmp/crawl_a_site.log