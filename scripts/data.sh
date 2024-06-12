#!/bin/bash


user="tviallard"
password="Bonjour123"
database="sae23"


mosquitto_sub -h mqtt.iut-blagnac.fr -p 1883 -t AM107/by-room/+/data | while read -r line
do
	building=$(echo "$line" | jq '.[1].building' -r)
	room=$(echo "$line" | jq '.[1].room' -r)

	temperature=$(echo "$line" | jq '.[0].temperature')
	humidity=$(echo "$line" | jq '.[0].humidity')
	co2=$(echo "$line" | jq '.[0].co2')
	tvoc=$(echo "$line" | jq '.[0].tvoc')
	illumination=$(echo "$line" | jq '.[0].illumination')
	pressure=$(echo "$line" | jq '.[0].pressure')
	datetime=$(date +'%Y-%m-%d %H:%M:%S')

	metrics=("temperature" "humidity" "co2" "tvoc" "illumination" "pressure")

	room_exist=$(/opt/lampp/bin/mysql -u "$user" -p"$password" -D "$database" -sse "SELECT COUNT(*) FROM salle WHERE nom_salle='$room';")
	if [ "$room_exist" -gt 0 ]; then
		for el in "${metrics[@]}"
		do
			sensor_exist=$(/opt/lampp/bin/mysql -u "$user" -p"$password" -D "$database" -sse "SELECT COUNT(*) FROM capteur WHERE nom_capteur='${room}${el}';")
			if [ "$sensor_exist" -gt 0 ]; then
				
		done
	else
		echo "La salle $room n'existe pas."
	fi
done
