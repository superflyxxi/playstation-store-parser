cd ../src
wget -nc "https://github.com/danger89/metacritic_api/archive/v1.0.tar.gz"
tar xf v1.0.tar.gz
cd ../tests

cp ../src/settings.ini ./

find ./ -name "*.php" | xargs -L1 php 

