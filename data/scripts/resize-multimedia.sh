#!/bin/bash

##
# Resizing script for Choique multimedia files.
#
# --------------------------------------------------------------------
# Please note that this script should be executed on the directory in
# which multimedia files are (i.e. SF_ROOT_DIR/web/uploads/assets).
# --------------------------------------------------------------------
#
# This script takes all of the large-sized files and resizes them to
# their medium and small sizes, *only if* there is a file for each of
# that sizes and if the large file differs from the other sizes.
#
# The newly resized files will be placed in a subdirectory for each
# size ('small' for the small files and 'medium' for the medium ones.
# After that, the files should be moved to web/uploads/assets in order
# to overwrite the existing - and wrong-sizes - files.
##

# Different sizes specification -- if no height is specified, the file
# will be resized proportionally to the specified width.
MEDIUM_WIDTH="525"
MEDIUM_HEIGHT="290"
SMALL="265"
CONVERT="`which convert`"

rm -rf small medium
mkdir small medium

find . | grep _large. | while read file
do
	sfile="${file/_large./_small.}"
	mfile="${file/_large./_medium.}"

	if [ ! -f "$sfile" ]
	then
		continue
	fi

	if [ ! -f "$mfile" ]
	then
		continue
	fi

	diff "$file" "$mfile" > /dev/null

	if [ $? -gt 0 ]
	then
		$CONVERT "$file" -resize $MEDIUM_WIDTH -crop "x$MEDIUM_HEIGHT" +repage "medium/$mfile"
		rm "medium/${mfile/_medium./_medium-1.}"
		mv "medium/${mfile/_medium./_medium-0.}" "medium/$mfile"
	fi

	diff "$file" "$sfile" > /dev/null

	if [ $? -gt 0 ]
	then
		$CONVERT "$file" -resize $SMALL small/"$sfile"
	fi
done
