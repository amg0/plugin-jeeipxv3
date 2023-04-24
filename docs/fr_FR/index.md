# Plugin Jeedom V4 pour la carte IPX V3 DE CGE

plugin-jeeipxv3 is a Jeedom V4 plugin for CGE IPX 800 V3 card.

Requires IPX800 V3 to run firmware > 3.05.46 ( ioname.xml must be supported )
    
- supports as many IPX card as you want as individual jeedom equipments with configuPush and Reboot action
- support Analog, Output Relay, and Digital Inputs ( analog, led and btn entries in IPX )
- presents the IPX card output relays and digital inputs as individual Jeedom equipement usable in scenario and dashboards
- gets the equipment names by default from the IPX configuration for Jeedom equipement ( but can be changed afterward )
- configurable regular polled refresh for all data with a ConfigPush action  ( shortens the API Key to 32 chars to fit IPX )  
- but also support configuration a Push url on IPX to get real time updates into Jeedom 
- calculates and display the proper corrected analog value for analog entries based on the IPX chosen configuration ( PH, Temp, ... )


## Utilisation


## Commandes


## Change Log

[Change Log](changelog.md)

## Installation

