#!/bin/bash

# Database configuration
DB_USER="arahalinony"
DB_PASS="sae23.blagnac"
DB_NAME="SAE23_V2"

# MQTT broker configuration
MQTT_HOST="mqtt.iut-blagnac.fr"
MQTT_PORT="8883"
MQTT_USER="student"
MQTT_PASS="student"
MQTT_TOPIC="sensors/AM107/by-room/+/data" 

echo "Lancement du script de récupération des températures"

# Subscribe to the MQTT broker and process each incoming message
mosquitto_sub -h "$MQTT_HOST" -p "$MQTT_PORT" -u "$MQTT_USER" -P "$MQTT_PASS" -t "$MQTT_TOPIC" --insecure -v | while read -r topic payload
do  
    if [ ! -z "$payload" ]; then
        
        # Extract room name from topic 
        nom_salle=$(echo "$topic" | cut -d'/' -f4)

        # Extract sensor name and temperature value from JSON payload
        nom_capteur=$(echo "$payload" | jq -r '.[1].deviceName')
        valeur=$(echo "$payload" | jq -r '.[0].temperature')

        if [ "$nom_capteur" != "null" ] && [ "$valeur" != "null" ]; then
            
            # Check if the room is configured in the database
            CHECK_SALLE=$(/opt/lampp/bin/mysql -u"$DB_USER" -p"$DB_PASS" -D"$DB_NAME" -se "SELECT COUNT(*) FROM Salle WHERE nom_salle='$nom_salle';")

            if [ "$CHECK_SALLE" -gt 0 ]; then
                
                # Auto-register the sensor if it doesn't exist yet for this room
                /opt/lampp/bin/mysql -u"$DB_USER" -p"$DB_PASS" -D"$DB_NAME" -e "INSERT IGNORE INTO \`Capteur\` (nom_capteur, type, unite, nom_salle) VALUES ('$nom_capteur', 'Température', '°C', '$nom_salle');"
                
                date_actuelle=$(date +%Y-%m-%d)
                heure_actuelle=$(date +%H:%M:%S)
                echo "[$heure_actuelle] Salle : $nom_salle | Capteur : $nom_capteur | Valeur : $valeur°C"
                
                # Insert the new measurement into the database
                REQUETE="INSERT INTO \`Mesure\` (date_mesure, heure, valeur, nom_capteur) VALUES ('$date_actuelle', '$heure_actuelle', '$valeur', '$nom_capteur');"
                /opt/lampp/bin/mysql -u"$DB_USER" -p"$DB_PASS" -D"$DB_NAME" -e "$REQUETE"
                
                echo " Insertion réussie."
            else
                # Room not configured by admin, skip the data
                echo "Données ignorées pour la salle $nom_salle (Non configurée par l'admin)."
            fi
            echo ""
			echo ""
        fi
    fi
done