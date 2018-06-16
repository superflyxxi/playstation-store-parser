# Introduction
The PlayStation Store Parser is intended to work on a store Collection ID. A Collection is essentially a grouping of games/media on the PlayStation Store. For example, STORE-MSF77008-NEWTHISWEEK is the ID for the collection that has new the games this week. This tool will parse the games in the desired collection recusrively; fetch their metacritic score, original price, and sale price; sort the results based on score; and finally produce an HTML that can be used for publishing.

# Dependencies
- PHP 5.0 or Higher
- Metacritic_Api_1.0 or higher (https://github.com/danger89/metacritic_api)

# Limitations
- This tool only works on US PlayStation Store. Since the API is HATEOS, it's fairly easy to write code around it. I have not tested this against any other store.
- I've altered danger89/metacritic_api to support PlayStation 4 and added redimentary name matching. The base API will find for PC game's metacritic score.

# Usage
Make the parsePlayStationSale.php executable and call it as follows. This will default to the "all deals" collection (STORE-MSF77008-WEEKLYDEALS).

`./parsePlayStationSale.php`

Alternatively, you can pass the collection to parse as the first argument.

`./parsePlayStationSale.php STORE-MSF77008-NEWTHISWEEK`
