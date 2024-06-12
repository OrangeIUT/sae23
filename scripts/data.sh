#!/bin/bash


database="sae23"

mosquitto_sub -h mqtt.iut-blagnac.fr -p 1883 -t AM107/by-room/+/data | while read -r line
do
  building=$(echo "$line" | jq '.[1].room' -r | head -c 1) # Get the first character of the building name
	room=$(echo "$line" | jq '.[1].room' -r)
	# replaces accents by accentless characters
	room=$(echo "$room" | sed 's/[éèêë]/e/g; s/[àâä]/a/g; s/[ùûü]/u/g; s/[îï]/i/g; s/[ôö]/o/g')
	datetime=$(date +'%Y-%m-%d %H:%M:%S')

  # metrics is an array containing the names of the metrics, and units their corresponding units
  metrics=("temperature" "humidity" "co2" "tvoc" "illumination" "pressure")
  units=("°C" "%" "ppm" "ppm" "lux" "hPa")

	room_exist=$(/opt/lampp/bin/mysql -D "$database" -sse "SELECT COUNT(*) FROM salle WHERE nom_salle='$room';")
	# If room doesn't exist, create it
	if [ "$room_exist" -eq 0 ]
  then
    /opt/lampp/bin/mysql -D "$database" -e "INSERT INTO salle (nom_salle, type, capacite, nom_bat) VALUES ('$room', 'NA', 0, '$building');"
  fi

  # for each metric, check if the sensor exists in the database, if not, create it
  for i in "${!metrics[@]}"
  do
    sensor_exist=$(/opt/lampp/bin/mysql -D "$database" -sse "SELECT COUNT(*) FROM capteur WHERE nom_capteur='${room}_${metrics[i]}';")
    if [ "$sensor_exist" -eq 0 ]
    then
      /opt/lampp/bin/mysql -D "$database" -e "INSERT INTO capteur (nom_capteur, type, unite, nom_salle, active) VALUES ('${room}_${metrics[i]}', '${metrics[i]}', '${units[i]}', '$room', 0);"
    fi

    # Insert the metric value in the database
    value=$(echo "$line" | jq ".[0].${metrics[i]}" -r)
    /opt/lampp/bin/mysql -D "$database" -e "INSERT INTO mesure (date, valeur, nom_capteur) VALUES ('$datetime', '$value', '${room}_${metrics[i]}');"
  done
done
