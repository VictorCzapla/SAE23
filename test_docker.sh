#!/bin/bash

# InfluxDB Configuration
INFLUX_HOST="localhost"
INFLUX_PORT="8086"
INFLUX_DB="capteurs"
INFLUX_USER="noder"
INFLUX_PASS="passroot" 

# Real data structure from your screenshot
BUILDING="RT"
ROOM="E208"
VALUE="26.2"

echo "Envoi de la température vers InfluxDB"

# measurement,tag1=value,tag2=value field=value
PAYLOAD="temperature,building=${BUILDING},room=${ROOM} value=${VALUE}"

# Execute HTTP POST request
curl -i -XPOST "http://${INFLUX_HOST}:${INFLUX_PORT}/write?db=${INFLUX_DB}&u=${INFLUX_USER}&p=${INFLUX_PASS}" --data-binary "${PAYLOAD}"

echo ""
echo "Insertion terminée."