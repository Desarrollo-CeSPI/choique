#!/bin/bash

GREEN_COLOR='\033[01;32m' # bold green
YELLOW_COLOR='\033[01;33m' # bold yellow
RED_COLOR='\033[01;31m' # bold red
RESET='\033[00;00m' # normal white

function message_success()
{
  MESSAGE=${@:-"${RESET}Error: No message passed"}
  echo -e "${GREEN_COLOR}${MESSAGE}${RESET}"
}

function message_info()
{
  MESSAGE=${@:-"${RESET}Error: No message passed"}
  echo -e "${YELLOW_COLOR}${MESSAGE}${RESET}"
}

function message_warning()
{
  MESSAGE=${@:-"${RESET}Error: No message passed"}
  echo -e "${RED_COLOR}${MESSAGE}${RESET}"
}

function check_file_existance()
{
  for i in $@; do
    if [ -f $i ]; then return; fi
  done
  message_warning "One of $@ does not exists."
  exit 2;
}

function run_hooks()
{
  if [ $# -ne 1 ]
  then
    message_warning "Hooks dir not specified."
    exit 3;
  fi
  
  if [ -d $1 ]
  then
    for i in `ls $1`
    do
      source $1/$i
    done
  fi
}
