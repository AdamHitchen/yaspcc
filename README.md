# Yet Another Steam Play Compatibility Checker

Pulls user ratings from the data dumps provided by https://www.protondb.com/
and game information from steam to provide user ratings and game info for games you own.

- View ratings for games you own
- View common games between 2 or more people - filter by multiplayer only games, native only etc

Frontend coming soon™


##Setup
- Grab a steam API key at https://steamcommunity.com/dev/apikey
- Copy sample redis & steam config files in config dir
- Add your key to the steam config.
- Extract the latest dump from https://github.com/bdefore/protondb-data into var/import (Tool to automate this coming soon)
- Run docker-compose up
