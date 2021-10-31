#!/bin/bash
# vanityPrint - returns a filepath to a rendered pdf document
# e.g. bash vanityPrint.sh -u "www.google.com" -d "/etc/local/vpsupport/pdf" -t "Title" -s "Subject" -c "Creator" -a "Custom User Agent";
while getopts u:d:t:s:c:a: option
do
    case "${option}"
    in
        # username to use when connecting
        u) URL=${OPTARG};;
        # git repository name
        d) DIR=${OPTARG};;
        # directory to install bdeploy in
        t) TITLE=${OPTARG};;
        # directory to target deployments at
        s) SUBJECT=${OPTARG};;
        # branch to configure as default deployment
        c) CREATOR=${OPTARG};;
        # custom user agent to use
        a) AGENT=${OPTARG};;
    esac
done
# define defaults
if [ -z "$AGENT" ]; then AGENT='internal-pdf-render'; fi;
if [ -z "$CREATOR" ]; then CREATOR='vanity.html'; fi;

# htmltopdf - render a url and save a randomly named file to the given directory
function htmltopdf() {
    # url to render
    local URL=$1;
    # where to save
    local DIR=$2;
    # generate filename
    local PATH="$2/$( uuidgen ).pdf";
    # render pdf
    wkhtmltopdf -T 5 -L 5 -R 5 -B 5 --custom-header 'User-Agent' 'internal-pdf-render' --custom-header-propagation --zoom 0.9 $URL $PATH &>/dev/null;
    # return saved filepath
    echo $PATH;
}

function setPdfProp() {
    # the PDF to set properties on
    local PATH=$1;
    # the properties to apply
    local TITLE=$2;
    local SUBJECT=$3;
    local CREATOR=$4;
    # execute pdf property update
    exiftool \
        -z -P \
        -XMP:Format="application/pdf" \
        -Title="$TITLE" \
        -PDF:Subject="$SUBJECT" -XMP:Description="$SUBJECT" \
        -XMP:Marked=True \
        -PDF:Creator="$CREATOR" -XMP:CreatorTool="$CREATOR" \
        -Producer="$CREATOR" \
        -overwrite_original_in_place "$PATH" \
    &>/dev/null;
    # return path
    echo $PATH;
}

# create the directory if it does not exist
[ ! -d $DIR ] && mkdir –p $DIR;
# clean up old files first
find $DIR/*.pdf -type f -mmin +10 -maxdepth 1 | xargs rm 2>/dev/null;
# get an existing pdf created in the last 1 minute
FILE=$( find $DIR/*.pdf -type f -mmin -1 -maxdepth 1 | grep -m1 . 2>/dev/null );
# make new pdf if necessary
if [ -z "$FILE" ]; then FILE=$( setPdfProp "$( htmltopdf $URL $DIR )" "$TITLE" "$SUBJECT" "$CREATOR" ); fi;
# return file result
echo $FILE;
