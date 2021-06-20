#!/bin/bash
sudo sysctl -w vm.max_map_count=262144
~/.koala/bin/generate-certificates.sh elasticsearch7.${KOALA_LOCAL_TLD} kibana.elasticsearch7.${KOALA_LOCAL_TLD}
cd ~/.koala/services/elasticsearch-7/ && docker-compose up -d
