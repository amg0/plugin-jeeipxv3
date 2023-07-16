# Changelog plugin template

>**IMPORTANT**
>
>Pour rappel s'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

## 16/07/2023

- create action 'Impulse' on relay (only on relay) to force a ON OFF impulse even if the relay is not programmed as impulse on IPX ( with Ta Tb )

## 06/07/2023

- create action 'Commute' not visible by defaut on the relay and the digital entries to commute the state  ( 0 to 1, 1 to 0 )
- fix action ON , OFF on the relay to force the state ( 1 or 0 ) independently of the current state

## 15/05/2023

- set action 'Set' on counter to set it to a specified value ( slider ) 

## 07/05/2023

- Display counter as "tile" template by default, can be changed by user after

## 29/04/2023

- Add ConfigPush button on the eq configuration dialog to trigger the configuration of the push url on the IPX card
- Add TestAccess button on the eq configuration dialog to test the reachability of the IPX card from the jeedom backend

## 27/04/2023

- Add support for 8 IPX counters and a reset action to reset to 0

## 25/04/2023

- Documentation update
- Display proper analog sensor type in the equipment option dialog box ( ajax call ).
- Display proper equipment Icon per type of IPX sensor for the equipment

## 24/04/2023

- Version Initiale.

