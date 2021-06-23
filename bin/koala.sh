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

createProject() {
    echo "Enter a project name:"
    read name

    echo "Enter the domain to work on, it will become {{domain}}.docker :"
    read domain

    cp -pr ~/.koala/templates/newProject ~/$name/
    sed -i -e "s/{{projectName}}/$domain/g" ~/$name/.env.dist

    echo -ne "$blue
    Add these lines to your hosts file
    $clear"

    echo -ne "
    $yellow
    127.0.0.1    $domain.docker
    ::1    $domain.docker
    $clear
"

    echo "Project is created at ~/$name"
    echo "To start the project run the following commands:"
    echo "cd ~/$name"
    echo "bin/start-environment.sh"
    exit 0;
}

menu(){
echo -ne "
$yellow
========================================================

     ▄▄▄   ▄ ▄▄▄▄▄▄▄ ▄▄▄▄▄▄▄ ▄▄▄     ▄▄▄▄▄▄▄
    █   █ █ █       █       █   █   █       █
    █   █▄█ █   ▄   █   ▄   █   █   █   ▄   █
    █      ▄█  █ █  █  █▄█  █   █   █  █▄█  █
    █     █▄█  █▄█  █       █   █▄▄▄█       █
    █    ▄  █       █   ▄   █       █   ▄   █
    █▄▄▄█ █▄█▄▄▄▄▄▄▄█▄▄█ █▄▄█▄▄▄▄▄▄▄█▄▄█ █▄▄█

========================================================
$clear
$(colorGreen '1)') Start
$(colorGreen '2)') Stop
$(colorGreen '3)') Restart
$(colorGreen '4)') Compose service
$(colorGreen '5)') Create project
$(colorGreen '0)') Exit
$(colorBlue 'Choose an option:') "
        read a
        case $a in
	        1) koalaStart ; menu ;;
	        2) koalaStop ; menu ;;
	        3) koalaRestart ; menu ;;
	        4) composeService ; menu ;;
	        5) createProject ;;
			0) exit 0 ;;
			*) echo -e $red"Wrong option."$clear; WrongCommand;;
        esac
}
menu
