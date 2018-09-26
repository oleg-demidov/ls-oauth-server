#!/bin/bash
# My first script

read -p 'Вставить url:' url
read -p 'Вставить token:' token

url=${url:-"http://oauth.loc/api/me"}

echo "$url?token=$token"
curl -X POST -H "Authorization: Bearer $token" $url 

