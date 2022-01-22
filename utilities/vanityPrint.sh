#!/bin/bash
# vanityPrint - returns a filepath to a rendered pdf document
# e.g. bash vanityPrint.sh -u "www.google.com" -d "resource/pdf" -t "Title" -s "Subject" -c "Creator";
while getopts u:d:t:s:c:a: option
do
    case "${option}"
    in
        # URL to render
        u) URL=${OPTARG};;
        # directory to use for PDF cache
        d) DIR=${OPTARG};;
        # PDF PROPERTIES
        # title
        t) TITLE=${OPTARG};;
        # subject
        s) SUBJECT=${OPTARG};;
        # name of pdf creator ( DEFAULT 'vanity.html' )
        c) CREATOR=${OPTARG};;
        # custom user agent to use in http request ( DEFAULT 'internal-pdf-render' )
        a) AGENT=${OPTARG};;
    esac
done
# define defaults
[ -z "$AGENT" ] && AGENT='internal-pdf-render';
[ -z "$CREATOR" ] && CREATOR='vanity.html';

# test for binaries and fail critically if we need to
for i in 'wkhtmltopdf' 'exiftool'; do command -v $i >/dev/null 2>&1 || { echo "$i required but not found! failing critically..."; exit 1; }; done;

# renderPdf - render a url and save a randomly named file to the given directory
function renderPdf() {
    # url to render
    local URL=$1;
    # output filename
    local PDF=$2;
    # user agent to use in request
    local AGENT=$3;
    # delete destination if it exists
    rm -f $2 &>/dev/null;
    # render pdf and fail critically upon exception
    wkhtmltopdf -T 0 -L 0 -R 0 -B 0 --print-media-type --custom-header 'User-Agent' "$AGENT" --custom-header-propagation --zoom 0.9 $URL $PDF &>/dev/null || { echo 'error: wkhtml exceptions!'; return 1; };
    # return saved filepath and exit
    echo $PDF && return 0;
}

# setPdfProp - update a PDF's properties and save in place
function setPdfProp() {
    # the PDF to set properties on
    local PDF=$1;
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
        -overwrite_original_in_place "$PDF" \
    &>/dev/null || { echo 'error: exiftool exceptions!'; return 1; };
    # return path and exit
    echo $PDF && return 0;
}

# save an input pdf to cache path with a random name
function setPdfCache() {
    # the finished file we want to move
    local INPUT=$1;
    # generate filename
    local OUTPUT="$2/$( uuidgen ).pdf";
    # move input file to final output path and pass result to stdOut
    mv -f "$INPUT" "$OUTPUT" &>/dev/null || { echo 'error: could not save result'; return 1; }
    # return result and exit
    echo $OUTPUT && return 0;
}

# attempt to get a pdf from given cache location
function getPdfCache() {
    # the directory to use as pdf cache
    local DIR=$1;
    # create the directory if it does not exist
    mkdir -p $DIR &>/dev/null;
    # drop files older than 10 minutes
    find $DIR/*.pdf -type f -mmin +10 -maxdepth 1 2>/dev/null | xargs rm;
    # get an existing pdf created in the last 1 minute
    find $DIR/*.pdf -type f -mmin -1 -maxdepth 1 2>/dev/null | grep -m1 .;
    # exit function
    return 0;
}

# get an existing pdf from cache (returns nothing if no cache file was found)
FILE=$( getPdfCache $DIR );

# continue with making new pdf if we didn't find a valid result in cache
if [ -z "$FILE" ];
then
    # create temporary file
    TMPPATH=$( mktemp );
    # be sure to delete what remains when exiting
    trap "rm -f $TMPPATH;" EXIT;
    # create a pdf, set properties, and save to final path $FILE
    FILE=$( setPdfCache $( setPdfProp "$( renderPdf $URL $TMPPATH $AGENT )" "$TITLE" "$SUBJECT" "$CREATOR" ) $DIR ) || { echo "errors encounters when creating new pdf!"; exit 1; }
fi;

# return result and exit code
[ -z "$FILE" ] && exit 1 || { echo $FILE && exit 0; };
