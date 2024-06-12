#!/bin/bash

mosquitto_sub -h mqtt.iut-blagnac.fr -p 1883 -t AM107/by-room/+/data | while read -r line
do
  room=$(echo "$line" | jq '.[1].room' -r)
  temperature=$(echo "$line" | jq '.[0].temperature')
  humidity=$(echo "$line" | jq '.[0].humidity')
  co2=$(echo "$line" | jq '.[0].co2')
  tvoc=$(echo "$line" | jq '.[0].tvoc')
  illumination=$(echo "$line" | jq '.[0].illumination')
  pressure=$(echo "$line" | jq '.[0].pressure')
  datetime=$(date +'%Y-%m-%d %H:%M:%S')

  metrics=("temperature" "humidity" "co2" "tvoc" "illumination" "pressure")
  for el in "${metrics[@]}"
  do
    capt_exist_query="select nom_capteur from capteur where nom_capteur='${room}${el}' limit 1;"
    capteur=$(mysql -u tviallard -pBonjour123 -h localhost --socket=/opt/lampp/var/mysql/mysql.sock -D sae23 -e "$capt_exist_query")
    if [ -n "$capteur" ]
    then
      value=$(echo "$line" | jq '.[0]."'"$el"'"' -r)
      insert_query="insert into mesure (date, valeur, nom_capteur) values ('$datetime', '$value', '$capteur')"
      mysql -u tviallard -pBonjour123 -h localhost --socket=/opt/lampp/var/mysql/mysql.sock -D sae23 -e "$insert_query"
    else
      echo "Le capteur n'existe pas dans la base de données."
    fi
  done
done

