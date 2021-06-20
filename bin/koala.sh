#!/usr/bin/env bash

green='\e[32m'
blue='\e[34m'
yellow='\e[33m'
clear='\e[0m'

colorGreen() {
    echo -ne $green$1$clear
}
colorBlue() {
    echo -ne $blue$1$clear
}

koalaStart() {
    ~/.koala/bin/start-environment.sh
    exit 0
}

koalaStop() {
    ~/.koala/bin/stop-environment.sh
    exit 0
}

koalaRestart() {
    ~/.koala/bin/stop-environment.sh
    ~/.koala/bin/start-environment.sh
    exit 0
}

composeService() {
    ~/.koala/bin/compose-service.sh
}

menu(){
echo -ne "
$yellow=================================== KOALA ===================================$clear
$(colorGreen '1)') Start Koala
$(colorGreen '2)') Stop Koala
$(colorGreen '3)') Restart Koala
$(colorGreen '4)') Compose service
$(colorGreen '0)') Exit
$(colorBlue 'Choose an option:') "
        read a
        case $a in
	        1) koalaStart ; menu ;;
	        2) koalaStop ; menu ;;
	        3) koalaRestart ; menu ;;
	        4) composeService ; menu ;;
			0) exit 0 ;;
			*) echo -e $red"Wrong option."$clear; WrongCommand;;
        esac
}

menu
