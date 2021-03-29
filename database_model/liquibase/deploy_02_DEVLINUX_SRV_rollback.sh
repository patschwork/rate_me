#!/bin/sh
# patrick_schmitz
# v0.2
# Starts the LiquiBase Deployment with a helper Python Script

export cd=$(pwd)
export scrptdir=$(dirname $0)

export pythonpathExe=python
export ENVKEY_UPDATE=DEVLINUX_SRV_rollback
export CONFIGPATH=$cd
cd ../../helper_tools/Python/liquibase/
$pythonpathExe deploy.py $ENVKEY_UPDATE $CONFIGPATH
cd $cd
