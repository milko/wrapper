#!/bin/bash

#####################################################################################
#                                                                                   #
# Script for creating a file list for Phan									        #
#                                                                                   #
#####################################################################################

if [[ -z $WORKSPACE ]]
then
    export WORKSPACE=~/Documents/Development/Git/wrapper
fi

cd $WORKSPACE

JUNK=/var/tmp/junk.txt

for dir in \
    src/wrapper \
    vendor/mongodb/mongodb/src \
    vendor/mongodb/mongodb/src/Exception \
    vendor/mongodb/mongodb/src/GridFS \
    vendor/mongodb/mongodb/src/Model \
    vendor/mongodb/mongodb/src/Operation \
    vendor/triagens/arangodb/lib/triagens/ArangoDb
do
    if [ -d "$dir" ]; then
        find $dir -name '*.php' >> $JUNK
    fi
done

cat $JUNK | \
    grep -v "junk_file.php" | \
    grep -v "junk/directory.php" | \
    awk '!x[$0]++'

rm $JUNK