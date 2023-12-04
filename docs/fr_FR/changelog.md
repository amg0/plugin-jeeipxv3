# Changelog plugin template

>**IMPORTANT**
>
>Pour rappel s'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

## 04/12/2023

- Corrige le bug sur le boutton Test Access

## 16/07/2023

- cree l'action 'Impulse' sur le relai (seulement le relai) pour forcer une impulsion ON puis OFF meme si le relai n'est pas programmé comme impulsion sur l'IPX ( avec le Ta Tb )

## 06/07/2023

- implemente l'action 'Commute' non visible par defaut sur les relais et les entrees digitales pour commuter l'etat ( 0 a 1, 1 a 0 )
- corrige l'action ON ou OFF sur le relai pour forcer l'etat 1 ou 0, independement de l'etat courant

## 15/05/2023

- implemente l'action 'Set' sur le compteur pour le mettre a une valeur specifiee ( par le slider ou le parametre de l'action )

## 07/05/2023

- afficher les compteurs avec le template "tile" par defaut. cela est changeable a posteriori par l'utilisateur

## 29/04/2023

- Add ConfigPush button on the eq configuration dialog to trigger the configuration of the push url on the IPX card
- Add TestAccess button on the eq configuration dialog to test the reachability of the IPX card from the jeedom backend

## 27/04/2023

- Support les 8 compteurs IPX counters avec une action 'reset' pour les remettre a zero 0

## 25/04/2023

- mise a jour Documentation 
- afficher les types de sonde analogique dans la boite de dialgue de configuration de l'equipement. la configuration du type se fait dans l'IPX directement
- affiche des icones differents par type de sonde IPX pour l'equipement.

## 24/04/2023

- Version Initiale.

