#!/usr/bin/env bash

NAME=$0;
PURPOSE="Run commands in docker containers"
OPTIONS="hacn"
SYNOPSIS="`basename $NAME` [-${OPTIONS}]"
USAGE_EXAMPLE="`basename $NAME` -h"
SCRIPT_VERSION="1.0.0"
REQUIREMENTS="getopts"
PWD=`pwd`;

DOCKER_NAMESPACE="lamoid"
ARTISAN_COMMAND="docker-compose run ${DOCKER_NAMESPACE}.php php artisan";
COMPOSER_COMMAND="docker-compose run ${DOCKER_NAMESPACE}.composer composer";
NPM_COMMAND="docker-compose exec ${DOCKER_NAMESPACE}.node npm";
NODE_RESTART_COMMAND="docker-compose restart ${DOCKER_NAMESPACE}.node";

COMMAND="";
PARAMETERS="";

assert_requirements() {
	for REQUIREMENT in $REQUIREMENTS
	do
		command -v ${REQUIREMENT} >/dev/null 2>&1 || { echo >&2 "'${REQUIREMENT}' is required, but it's not installed. Aborting."; exit 1; }
	done
}

get_options() {
	OPTIONS_FOUND=false;
	while getopts "${OPTIONS}" OPT
	do
		OPTIONS_FOUND=true;
		case "$OPT" in
			\?)
				exit 1
			;;
		esac
	done
	OPTIND=1;
	shift $((OPTIND-1))
	while getopts  "${OPTIONS}" OPT
	do
		OPTIONS_FOUND=true;
		case "$OPT" in
			h)
				usage
				exit 0;
			;;
			a)
				COMMAND="artisan";
			;;
			c)
				COMMAND="composer";
			;;
			n)
				COMMAND="npm";
			;;
		esac
	done
  shift
  PARAMETERS="${@}";

  # validate
}

validate() {
  # Examples
  # [[ "${CLEAR}" == "true" || "${PERMISSIONS}" == "true" || "${SYMLINKS}" == "true" ]] || fail "ERROR: Clear (-c), permissions (-p) or symlinks (-s) parameters are required";
  # [[ "${SYSTEM_TYPE}" == "1" || "${SYSTEM_TYPE}" == "2" ]] || fail "ERROR: System type (-t) needs to be one of 1 or 2";
  output "TODO validate";
}

usage() {
	echo >&2 "`basename ${NAME}` ${SCRIPT_VERSION} - ${PURPOSE}
 Usage: ${SYNOPSIS}
 Requires: ${REQUIREMENT}
 Example: ${USAGE_EXAMPLE}
 Options:
   -h, Usage and options (this help)
   -a, Run an artisan command
   -n, Run an npm command
   "
}

output() {
  local MESSAGE=$1;
  local DEBUG_ONLY=$2;
  if [[ "${DEBUG_ONLY}" == "true" && "${DEBUG}" != "true" ]]
  then
    return;
  fi
  echo "${MESSAGE}";
}

fail() {
  local MESSAGE=$1
  output "${MESSAGE}";
  echo "";
  usage
  exit 1
}

contains() {
  [[ $1 =~ (^|[[:space:]])$2($|[[:space:]]) ]] && echo 0 || echo 1
}

require_sudo() {
  if [ `whoami` != root ]; then
    fail "ERROR: This command requires root or sudo privileges";
  fi
}

run() {
  if [[ ${COMMAND} == 'artisan' ]]
  then
    ${ARTISAN_COMMAND} ${PARAMETERS}
  fi
  if [[ ${COMMAND} == 'composer' ]]
  then
    ${COMPOSER_COMMAND} ${PARAMETERS}
  fi
  if [[ ${COMMAND} == 'npm' ]]
  then
    ${NPM_COMMAND} ${PARAMETERS}
    local RESTART=$(contains "${PARAMETERS}" "install");
    if [[ ${RESTART} == "0" ]]
    then
      output "Restarting node...";
      ${NODE_RESTART_COMMAND};
    fi
  fi
}

assert_requirements
get_options "$@"
run

