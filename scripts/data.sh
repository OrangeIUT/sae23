#!/bin/bash


database="sae23"

mosquitto_sub -h mqtt.iut-blagnac.fr -p 1883 -t AM107/by-room/+/data | while read -r line
do
  building=$(echo "$line" | jq '.[1].room' -r | head -c 1) # Get the first character of the building name
	room=$(echo "$line" | jq '.[1].room' -r)
	datetime=$(date +'%Y-%m-%d %H:%M:%S')

  # metrics is an array containing the names of the metrics, and their corresponding units
  metrics=( ("temperature" "°C") ("humidity" "%") ("co2" "ppm") ("tvoc" "ppb") ("illumination" "lux") ("pressure" "hPa") )

	room_exist=$(/opt/lampp/bin/mysql -D "$database" -sse "SELECT COUNT(*) FROM salle WHERE nom_salle='$room';")
	# If room doesn't exist, create it
	if [ "$room_exist" -eq 0 ]
  then
    /opt/lampp/bin/mysql -D "$database" -e "INSERT INTO salle (nom_salle, type, capacite, batiment) VALUES ('$room', NA, 0, '$building');"
  fi

  # for each metric, check if the sensor exists in the database, if not, create it
  for el in "${metrics[@]}"
  do
    sensor_exist=$(/opt/lampp/bin/mysql -D "$database" -sse "SELECT COUNT(*) FROM capteur WHERE nom_capteur='${room}_${el[0]}';")
    if [ "$sensor_exist" -eq 0 ]
    then
      /opt/lampp/bin/mysql -D "$database" -e "INSERT INTO capteur (nom_capteur, type, unite, nom_salle, active) VALUES ('${room}_${el}', '${el[0]}', '${el[1]}', '$room', 0);"
    fi

    # Insert the metric value in the database
    value=$(echo "$line" | jq ".[0].${el[0]}" -r)
    /opt/lampp/bin/mysql -D "$database" -e "INSERT INTO mesure (date, valeur, nom_capteur) VALUES ('$datetime', '$value', '${room}_${el[0]}');"
  done
done
