# PlayStation Store Parser

## Deprecated
This has been deprecated on November 16, 2020. To get equivalent features, please look to [PSDeals.net](https://psdeals.net), more specifically [Discounts by Metascore](https://psdeals.net/us-store/discounts?type=all&sort=metascore-desc). Also, you can use the [PS Store Helper](https://chrome.google.com/webstore/detail/ps-store-helper/ldjfkloldnlohgeblkanmjeehpeapbep?hl=en) extension to get all the info as you browse.

## Introduction
The PlayStation Store Parser is intended to work on a store Collection ID. A Collection is essentially a grouping of games/media on the PlayStation Store. For example, STORE-MSF77008-NEWTHISWEEK is the ID for the collection that has new the games this week. This tool will parse the games in the desired collection recusrively; fetch their metacritic score, original price, and sale price; sort the results based on score; and finally produce an HTML that can be used for publishing.

## Dependencies
- PHP 5.0 or Higher
- PHP OpenSSL
- PHP SimpleXML
- PHP curl
- PHP JSON

## Limitations
- This tool only works on US PlayStation Store. Since the API is HATEOS, it's fairly easy to write code around it. I have not tested this against any other store.

## Usage
Make the parsePlayStationSale.php executable and call it as follows. This will default to the "all deals" collection (STORE-MSF77008-WEEKLYDEALS).

`./parsePlayStationSale.php`

Alternatively, you can pass the collection to parse as the first argument.

`./parsePlayStationSale.php STORE-MSF77008-NEWTHISWEEK`

## Docker
A docker image is provided that runs cron on a scheduled basis.

### Running
```sh
docker run -p 80:80 -d -v "<path/to/config/>settings_override.ini:/home/ps-store/resources/settings_override.ini:ro" ps-store
```

### Docker Compose
```
  ps-store:
    build:
      context: .
      dockerfile: docker/Dockerfile 
    ports:
      - "80:80"
    expose:
      - "80"
    volumes:
      - "./resources/settings_override.ini:/home/ps-store/resources/settings_override.ini:ro"
```
