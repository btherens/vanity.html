/* run code block after a set delay */
var limiter = function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    }
}();

/* refresh all views to the latest data from server */
//var refreshView = async function () {
//	console.log('I ran');
//};

/* remove all classes from an element that start with prefix */
function removeClassByPrefix(el, prefix) {
    classes = el.className.split(' ').filter(c => !c.startsWith(prefix));
    el.className = classes.join(' ').trim();
}

/* loop through all elements with .script-active class and pass to processActive */
function processAllActive() {
    /* collect all active elements */
    var inputs = document.querySelectorAll('.script-active');
    /* loop through each input and trigger keyup events */
    inputs.forEach( ( el ) => { el.dispatchEvent( new Event( 'keyup' ) ); } );
};

/* process the passed dom element's active status and apply */
function processActive(elmt) {
    var status = elmt.type == 'checkbox' ? processActiveBool(elmt) : processActiveText(elmt);
    // wipe the 'update-' class before updating state
    removeClassByPrefix(elmt, 'update-');
    /* set the new class */
    elmt.classList.add('update-'.concat(status ? (status == 2 ? 'fail' : 'yes') : 'no'));
    /* update the element's form submit state */
    setSubmitState(elmt.form);
}

/* determine active element's change/no-change state and return */
function processActiveText(elmt) {
    var RegPattern = new RegExp(elmt.pattern ? elmt.pattern : '[\s\S]*');
    var status = 0;

    /* has the field changed */
    var nochange = elmt.value.replace(/(\r\n|\n|\r)/gm, /\n/) == elmt.getAttribute('value').replace(/(\r\n|\n|\r)/gm, /\n/);
    /* isfail - return true if any tests fail */
    var isfail =
        /* does it fail regex test or not meet requirements for required fields */
        (!RegPattern.test(elmt.value) && !(!elmt.hasAttribute('required') && elmt.value == '')) ||
        /* is the value new with a failure in server response */
        (!nochange && elmt.classList.contains('server-false'));


    if (isfail) { status = 2; } else {
        if (nochange) { status = 0;} else {status = 1}
    }
    status = isfail ? 2 : nochange ? 0 : 1;

    /* disable inputs if we can */
    if (!elmt.classList.contains('script-always')) {
        /* if status is not 0 (match with existing value) */
        setFormActive(elmt, !(status == 0));
    }

    return status;
}

/* update an active element's change/no-change state (boolean/checkbox) */
function processActiveBool(elmt) {
    return ( elmt.checked == true && elmt.getAttribute('checked') === '' )
    || ( elmt.checked == false && !(elmt.getAttribute('checked') === '') ) ? 0 : 1;
}

/* 
 * enable / disable an input
 * if the input is disabled (no change from server) name will be removed from attributes. removes unneeded values from response to server)
 */
function setFormActive(
    /* the form input to enable / disable */
    elmt,
    /* pass state as boolean - enable = true, disable = false */
    state = true
) {
    /* if active, replace any missing name attributes with xname attribute */
    if (state) { !elmt.getAttribute('name') ? elmt.setAttribute('name', elmt.getAttribute('xname')) : null; }
    /* otherwise, remove the name if it exists and set to xname */
    else if (elmt.getAttribute('name')) { elmt.setAttribute('xname', elmt.getAttribute('name')); elmt.removeAttribute('name'); }
}

/* enable/disable a form's update button based on existence of update-yes fields 
 * update-fail fields cause fail state
 */
function setSubmitState(frm) {
    /* get the form's submit button */
    var submitbtn = frm.querySelector('[type="submit"]:not([formnovalidate="formnovalidate"])')
    /* collect all fields that would need to be updated upon submission */
    var updateyes = frm.getElementsByClassName('update-yes');
    /* collect any inputs with error responses (either by failed regex checks or response from server) */
    var updatefail = frm.getElementsByClassName('update-fail');

    submitbtn.disabled =
        /* if some fields have changed values */
        (updateyes.length > 0)
            /* if no fields are marked as a failure */
            && (updatefail.length == 0)
            /* if the submit button has not been intentionally disabled by another process */
            && !submitbtn.classList.contains('disabled')
            ? false : true;
}

/* check passwords for match and update UI and bindings accordingly
 * id1 = password field 1
 * id2 = password field 2
 * idsubmit = status message
*/
function pswCheck(
    /* the first password to compare with */
    id1,
    id2,
    idsubmit,
    alwayson = true
) {
    /* get object variables */
    var el1 = document.getElementById(id1);
    var el2 = document.getElementById(id2);
    var elsubmit = document.getElementById(idsubmit);
    var elsubmitOgValue = elsubmit.getAttribute('value');

    /* check field for test pass */
    var RegPattern = new RegExp(el1.pattern);
    var isValid = RegPattern.test(el1.value);

    /* function will make visual changes to the submit button (idsubmit) */
    function modBtn(
        /* pass state as boolean - enable = true, disable = false */
        state,
        /* pass new label value (original value is default) */
        value = elsubmitOgValue
    ) {
        /* disable the button and apply new label */
        if (!state) {
            /* set new value to label */
            elsubmit.innerHTML = value;
            /* disable button (KNOWN ISSUE: Safari will still allow submit events from keyboard) */
            elsubmit.disabled = true;
            /* add the disabled class to the object to apply any relevant styles */
            elsubmit.classList.add('disabled');
        /* otherwise, restore the label and button state */
        } else {
            /* restore the original label */
            elsubmit.innerHTML = elsubmitOgValue;
            /* enable the button attribute */
            elsubmit.disabled = false;
            /* remove disabled classlist to update visuals */
            elsubmit.classList.remove('disabled');
        }
    }

    /* path for when alwayson bit is false (determine if passwords should be required) */
    if (!(alwayson)) {
        /* if both fields are empty, assume we're not updating passwords and don't require either */
        if (!(el1.value) && !(el2.value)) {
            el1.removeAttribute('required');
            el2.removeAttribute('required');
        }
        /* if either field is populated, make sure both fields are required */
        else {
            el1.setAttribute('required', 'required');
            el2.setAttribute('required', 'required');
        }
    }

    /* enable the button if the active input is empty */
    if (!el1.value) {modBtn(true);}
    /* if non-empty passwords match, or if other field is blank */
    else if ((!!el1.value && (el1.value == el2.value)) || !el2.value) {
        /* current field passes tests, is enabled */
        if (isValid) {modBtn(true);}
        /* other field is still empty, or both fields match but don't pass test */
        else {modBtn(false,(8 - el1.value.length));}
    }
    /* passwords do not match */
    else {modBtn(false, 'match?');}
}
