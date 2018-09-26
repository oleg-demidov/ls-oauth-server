#!/bin/bash
# My first script

read -p 'Вставить url:' url
read -p 'Вставить refresh_token:' rtoken
read -p 'Вставить client_id:' client_id
read -p 'Вставить client_secret:' client_secret
read -p 'Вставить scope:' scope

url=${url:-"http://oauth.loc/oauth/access_token"}
client_id=${client_id:-"1"}
#scope=${scope:-"mail"}

echo "$url?grant_type=refresh_token&refresh_token=$rtoken&client_id=$client_id&client_secret=$client_secret&scope=$scope"
curl -X POST -d "grant_type=refresh_token&refresh_token=$rtoken&client_id=$client_id&client_secret=$client_secret&scope=$scope" $url 

