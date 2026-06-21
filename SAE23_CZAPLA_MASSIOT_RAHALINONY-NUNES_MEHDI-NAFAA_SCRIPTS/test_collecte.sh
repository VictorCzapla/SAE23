#!/bin/bash
# Database configuration
DB_USER="arahalinony"
DB_PASS="sae23.blagnac"
DB_NAME="SAE23_V2"
# MQTT broker configuration
NOM_SALLE="E208"
NOM_CAPTEUR="AM107-TEST-LOCAL"
VALEUR_TEMPERATURE="20.0"

echo "Lancement du test"
echo ""
echo "Salle=$NOM_SALLE | Capteur=$NOM_CAPTEUR | Température=${VALEUR_TEMPERATURE}°C"

# 1. Check if the room is configured in your local database
CHECK_SALLE=$(/opt/lampp/bin/mysql -u"$DB_USER" -p"$DB_PASS" -D"$DB_NAME" -se "SELECT COUNT(*) FROM Salle WHERE nom_salle='$NOM_SALLE';")

if [ "$CHECK_SALLE" -gt 0 ]; then
    
    # 2. Auto-register the sensor if it doesn't exist yet for this room
    /opt/lampp/bin/mysql -u"$DB_USER" -p"$DB_PASS" -D"$DB_NAME" -e "INSERT IGNORE INTO \`Capteur\` (nom_capteur, type, unite, nom_salle) VALUES ('$NOM_CAPTEUR', 'Température', '°C', '$NOM_SALLE');"
    
    # Get current timestamps
    date_actuelle=$(date +%Y-%m-%d)
    heure_actuelle=$(date +%H:%M:%S)
    
    echo "[$heure_actuelle] Salle : $NOM_SALLE | Capteur : $NOM_CAPTEUR | Valeur : ${VALEUR_TEMPERATURE}°C"
    
    # 3. Formulate the SQL INSERT statement for the measurement
    REQUETE="INSERT INTO \`Mesure\` (date_mesure, heure, valeur, nom_capteur) VALUES ('$date_actuelle', '$heure_actuelle', '$VALEUR_TEMPERATURE', '$NOM_CAPTEUR');"
    
    # 4. Execute insertion into local MySQL
    /opt/lampp/bin/mysql -u"$DB_USER" -p"$DB_PASS" -D"$DB_NAME" -e "$REQUETE"
    
    echo " Insertion réussie dans la table 'Mesure'."
else
    # Fallback log matching your application logic
    echo "Données ignorées pour la salle $NOM_SALLE (Non configurée par l'admin dans la base locale)."
fi

echo ""
echo ""
