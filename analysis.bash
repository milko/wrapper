#!/bin/bash

#####################################################################################
#                                                                                   #
# Script for generating code analysis.                                              #
#                                                                                   #
#####################################################################################

echo
echo "********************************************************************************"
echo "*                             Generate analysis                                *"
echo "********************************************************************************"
directory=`dirname $0`
cd $directory

###
# Create file list.
###
echo "Creating files list -> files"
./.phan/bin/mkfileset.bash > files

###
# Perform analysis.
###
echo "Analysing files:"
./vendor/bin/phan --progress-bar -o analysis.txt

echo
echo "=> Done"
echo
