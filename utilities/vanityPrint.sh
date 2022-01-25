#!/usr/bin/env bash
usage() { cat << EOF
usage: vanityPrint -u URL -d DIR -t TITLE -s SUBJECT -c CREATOR -a AGENT [-w]

renders a pdf of the given url and saves the result to DIR. DIR serves as this PDF's cache.

returns the full filepath to a rendered pdf document

OPTIONS:
    -u URL     internet address to render e.g. http://google.com
    -d DIR     directory to save results to (will be used as a file cache)

    PDF PROPERTIES
    -t TITLE   document's title
    -a AUTHOR  document's author
    -s SUBJECT document's subject
    -c CREATOR creator of the document - default is 'vanity.html'

    -b AGENT   pass a custom user agent to use in web request - default is 'internal-pdf-render'
    -w TRIM    pass -w flag to drop files older than timeout (10 minutes)

EOF
}

while getopts :u:d:t:a:s:c:b:w option
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
        # author
        a) AUTHOR=${OPTARG};;
        # subject
        s) SUBJECT=${OPTARG};;
        # name of pdf creator ( DEFAULT 'vanity.html' )
        c) CREATOR=${OPTARG};;
        # custom user agent to use in http request ( DEFAULT 'internal-pdf-render' )
        b) AGENT=${OPTARG};;
        # pass -w to drop files older than timeout
        w) TRIM=1;;
        # return usage info if any unsupported parameters were passed
        ?) usage; exit 1;;
    esac
done
# define defaults
[ -z "$AGENT" ] && AGENT='internal-pdf-render';
[ -z "$CREATOR" ] && CREATOR='vanity.html';

# test for binaries and fail critically if we need to
for i in 'wkhtmltopdf' 'exiftool'; do command -v $i &>/dev/null || { echo "$i required but not found! failing critically..." 1>&2; exit 1; }; done;

# renderPdf - render a url and save to output path
function renderPdf() {
    # StdIn: url to render
    local URL; read URL;
    # output filename
    local PDF=$1;
    # user agent to use in request
    local AGENT=$2;

    # remove any existing file at PDF path
    rm -f "$PDF" &>/dev/null;

    # render pdf and fail critically upon exception
    wkhtmltopdf -T 0 -L 0 -R 0 -B 0 --print-media-type --custom-header 'User-Agent' "$AGENT" --custom-header-propagation $URL $PDF &>/dev/null || { echo 'error: wkhtml exceptions!' 1>&2; return 1; };
    # return saved filepath and exit
    echo $PDF && return 0;
}

# setPdfProp - update a PDF's properties and save in place
function setPdfProp() {
    # StdIn: the PDF to set properties on
    local PDF; read PDF;
    # args: the properties to apply
    local TITLE=$1;
    local AUTHOR=$2;
    local SUBJECT=$3;
    local CREATOR=$4;

    # execute pdf property update
    exiftool \
        -z -P \
        -XMP:Format="application/pdf" \
        -XMP:Marked=True \
        -Title="$TITLE" \
        -PDF:Subject="$SUBJECT" -XMP:Description="$SUBJECT" \
        -Author="$AUTHOR" \
        -PDF:Creator="$CREATOR" -XMP:CreatorTool="$CREATOR" \
        -Producer="$CREATOR" \
        -overwrite_original_in_place "$PDF" \
    &>/dev/null || { echo 'error: exiftool exceptions!' 1>&2; return 1; }
    # return path and exit
    echo $PDF && return 0;
}

# save an input pdf to cache path with a random name
function setPdfCache() {
    # the finished file we want to move
    local INPUT; read INPUT;
    # generate filename
    local OUTPUT="$1/$( uuidgen ).pdf";
    # move input file to final output path and pass result to stdOut
    mv -f "$INPUT" "$OUTPUT" &>/dev/null || { echo 'error: could not save result' 1>&2; return 1; }
    # return result and exit
    echo $OUTPUT && return 0;
}

# attempt to get a pdf from given cache location
function getPdfCache() {
    # Stdin: the directory to use as pdf cache
    local DIR; read DIR;
    # arg1: override default timeout in minutes (1 minute)
    local TIMEOUT=$1; [ -z "$TIMEOUT" ] && TIMEOUT='1';
    # create the directory if it does not exist
    mkdir -p $DIR &>/dev/null;
    # get an existing pdf created in the last 1 minute
    find $DIR/*.pdf -type f -mmin "-${TIMEOUT}" -maxdepth 1 2>/dev/null | grep -m1 .;
    # exit function with exit code from find command
    return $?;
}

# attempt to get a pdf from given cache location
function trimPdfCache() {
    # Stdin: the directory to use as pdf cache
    local DIR; read DIR;
    # arg1: override default timeout in minutes (10 minutes)
    local TIMEOUT=$1; [ -z "$TIMEOUT" ] && TIMEOUT='10';
    # create the directory if it does not exist
    mkdir -p $DIR &>/dev/null;
    # drop files older than timeout
    find $DIR/*.pdf -type f -mmin "+${TIMEOUT}" -maxdepth 1 2>/dev/null | xargs rm;
    # exit function with exit code from find command
    return $?;
}

# pass invalid input state to usage and exit abnormally, otherwise continue
if [ -z "$DIR" ]; then usage; exit 1; else

    # trim cache directory if switch was passed
    [ "$TRIM" == '1' ] && echo "$DIR" | trimPdfCache;

    # proceed if URL was passed
    if [ -n "$URL" ]; then

        # get an existing pdf from cache (returns nothing if no cache file was found)
        FILE=$( echo "$DIR" | getPdfCache );

        # if cache did not return the pdf
        if [ -z "$FILE" ]; then
            # create temporary path for pdf rendering and configure trap to ensure script cleans up temporary files on exit
            TMPPATH=$( mktemp ); trap 'rm -f "'"$TMPPATH"'";' EXIT;
            # create a pdf, set properties, and save to final path $FILE
            FILE="$( echo "$URL" | renderPdf "$TMPPATH" "$AGENT" | setPdfProp "$TITLE" "$AUTHOR" "$SUBJECT" "$CREATOR" | setPdfCache "$DIR" )" || { echo 'errors encounters when creating new pdf!' 1>&2; exit 1; }
        fi

        # return result and exit code
        [ -z "$FILE" ] && exit 1 || { echo "$FILE" && exit 0; };

    fi
fi
