#!/bin/bash
# My first script

read -p 'Вставить url:' url

read -p "Вставить client_id:" client_id

read -p "Вставить client_secret:" client_secret

read -p "Вставить redirect_uri:" redirect_uri

read -p 'Вставить code:' code

client_secret=${client_secret:-"dsfsdfsdfsdfsdfds"}
client_id=${client_id:-"1"}
redirect_uri=${redirect_uri:-"http://test.loc"}
url=${url:-"http://oauth.loc/oauth/access_token"}

echo "http://oauth.loc/oauth/access_token?client_id=$client_id&client_secret=$client_secret&redirect_uri=$redirect_uri&code=$code"
wget "http://oauth.loc/oauth/access_token" --post-data="client_id=$client_id&client_secret=$client_secret&redirect_uri=$redirect_uri&code=$code"

