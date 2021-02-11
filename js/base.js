// scripts to run after page has loaded
var callback = function () {
    processAllActive();
};

// the event listener to call launch scripts
if (
    document.readyState === 'complete' ||
    (document.readyState !== 'loading' && !document.documentElement.doScroll)
) {
    callback();
} else {
    document.addEventListener('DOMContentLoaded', callback);
}

// only run wrapped function after a set delay
var limiter = function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    }
}();

// extend Modal class with ok/cancel
class ConfirmModal {
    constructor({ text, trueButtonText, falseButtonText }) {
        /* default values */
        this.text = text || "Are you sure?";
        this.trueButtonText = trueButtonText || "Yes";
        this.falseButtonText = falseButtonText || "No";

        /* the container (invisible/visible) */
        this.container = document.getElementById('modal-container');
        /* the element form is appended to */
        this.modalwindow = document.getElementById('modalform-content');
        /* the actual modal form to be set and displayed */
        this.formEl = undefined;
        /* the confirm button */
        this.trueButton = undefined;
        /* the denial button */
        this.falseButton = undefined;

        this._buildModal();
        this._appendDialog();
    }

    confirm() {
        this._showModal();
        //this.trueButton.focus();

        return new Promise((resolve, reject) => {
            const somethingWentWrongUponCreation =
                !this.formEl || !this.trueButton || !this.falseButton;
            if (somethingWentWrongUponCreation) {
                reject('failed to create modal');
                return;
            }

            this.trueButton.addEventListener("click", () => {
                resolve(true);
                this._destroy();
            });

            this.falseButton.addEventListener("click", () => {
                resolve(false);
                this._destroy();
            });
        });
    }

    _buildModal() {
        /* create form */
        this.formEl = document.createElement('form');
        this.formEl.setAttribute('id', 'modal-frm');

        /* create message div */
        this.messageEl = document.createElement('div');
        /* ok button */
        this.trueButton = this.createButton(this.trueButtonText);
        /* cancel button */
        this.falseButton = this.createButton(this.falseButtonText);

        /* assemble domform */
        this.formEl.appendChild(this.messageEl);
        this.formEl.appendChild(document.createElement('br'));
        this.formEl.appendChild(this.trueButton);
        this.formEl.appendChild(this.falseButton);

        /* fill in form message */
        this.messageEl.innerHTML = '' + this.text;
    }

    createButton(name, label = null) {
        label = label ?? name;
        var Btn = document.createElement('button');
        Btn.setAttribute('id', 'modal-btn-' + name);
        Btn.setAttribute('form', this.formEl.getAttribute('id'));
        Btn.setAttribute('value', name);
        var Btnspan = document.createElement('span');
        Btnspan.innerText = label;
        Btn.appendChild(Btnspan);
        return Btn;
    }

    _showModal() {
        this.container.classList.add('show');
    }

    _hideModal() {
        this.container.classList.remove('show');
    }

    _appendDialog() {
        this.modalwindow.appendChild(this.formEl);
    }

    _destroy() {
        this._hideModal();
        this.modalwindow.removeChild(this.formEl);
        delete this;
    }
}

// simple function based around fetch()
// uses promises
// .then() will run when request succeeds
// .catch() will run when request fails
function appWebRequest(uri, params = null, ispost = false) {
    var options = {
        method: ispost ? 'POST' : 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        }
    }

    /* generate params string if params exist */
    if (params !== null) {
        if (ispost) {
            options.body = Object.keys(params).map(key => key + '=' + params[key]).join('&');
        } else {
            uri += '?' + new URLSearchParams(params);
        }
    }
    return fetch(uri, options).then(function (response) {
        if (response.ok) {
            return response.json();
        } else {
            return Promise.reject('api request failed. ¯\_(ツ)_/¯');
        }
    });
}

// a fun function that removes all classes from an element
// that start with prefix
function removeClassByPrefix(el, prefix) {
    classes = el.className.split(' ').filter(c => !c.startsWith(prefix));
    el.className = classes.join(' ').trim();
}

// this function runs a query with feedkey/episodekey against the server API
// element is marked by server- class based on response, to be processed
// by processActive
function checkkey(elmt1 = this, type = 'feed', key = null) {
    var params = (type == 'feed') ? {
        type: type,
        key: elmt1.value,
        episodekey: null
    } : {
            type: type,
            key: key,
            episodekey: elmt1.value
        };
    appWebRequest('/feeds/checkkey', params, false).then(function (data) {
        removeClassByPrefix(elmt1, 'server-');
        elmt1.classList.add(`server-${data.available}`);
        processActive(elmt1);
    }).catch(function (err) {
        // There was an error
        elmt1.classList.add(`server-failure`);
    });
}

// this function runs a query checking for user id (email) availability
function checkemail(el = this) {
    var params = {
        uid: el.value
    }
    appWebRequest('/account/checkemail', params, false).then(function (data) {
        removeClassByPrefix(el, 'server-');
        el.classList.add(`server-${data.available}`);
        processActive(el);
    }).catch(function (err) {
        // There was an error
        el.classList.add(`server-failure`);
    });
}

// challenge the server with current element's value
function pswChallenge(el = this) {
    var params = {
        p: el.value
    }
    appWebRequest('/account/pswchallenge', params, true).then(function (data) {
        removeClassByPrefix(el, 'server-');
        el.classList.add(`server-${data.challenge}`);
        processActive(el);
    }).catch(function (err) {
        // There was an error
        el.classList.add(`server-failure`);
    });
}

// remove invalid characters and share value with another element
function setIdValue(el1, id2) {
    var el2 = document.getElementById(id2);
    if (!el2.value) {
        el2.value = el1.value.replace(/\W/g, '').toLowerCase();
        el2.dispatchEvent(new Event('keyup'));
    }
}

// generic function to enable/disable visibility on an object collection
function toggle(objects = [], enablebool = true) {
    if (!enablebool) {
        objects.forEach(
            function (item) {
                item.classList.remove('show');
            }
        )
    } else {
        objects.forEach(
            function (item) {
                item.classList.add('show');
            }
        )
    }
}

// toggle the sidebar-main and sidebar-overlay classes
function sidebar_toggle(enablebool = true) {
    objects = [document.getElementById('sidebar-main'), document.getElementById('sidebar-overlay')];
    toggle(objects, enablebool);
}

// toggle the modal class
function modal_toggle(enablebool = true) {
    objects = [document.getElementById('modal-container')];
    toggle(objects, enablebool);
}

async function mConfirm(source, msg = 'are you sure?') {
    const prompt = new ConfirmModal({
        trueButtonText: "Yes",
        falseButtonText: "No",
        text: msg
    });
    const choice = await prompt.confirm();
    if (choice) {
        source.form.submit();
    }
}

function setModalContent(content) {
    modalDOMcontainer = document.getElementById('modalform-content');
    modalDOMcontainer.innerHTML = '';

    modalDOMcontainer.appendChild(content);
}

// create a jdenticon svg from a string
// save svg in avatarformid
function setAvatar(string, avatarfrmid) {
    document.getElementById(avatarfrmid).value = jdenticon.toSvg(string, 100);

}

// loop through all elements with .script-active class and pass to processActive
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

/* enable / disable an input */
function setFormActive(elmt, active = true)
{
    /* if active, replace any mmissing name attributes with xname attribute */
    if (active) { !elmt.getAttribute('name') ? elmt.setAttribute('name', elmt.getAttribute('xname')) : null; }
    /* otherwise, remove the name if it exists and set to xname */
    else if (elmt.getAttribute('name')) { elmt.setAttribute('xname', elmt.getAttribute('name')); elmt.removeAttribute('name'); }
}

// update an active element's change/no-change state (boolean/checkbox)
function processActiveBool(elmt) {
    return ( elmt.checked == true && elmt.getAttribute('checked') === '' )
    || ( elmt.checked == false && !(elmt.getAttribute('checked') === '') ) ? 0 : 1;
}

// enable/disable a form's update button based on existence of update-yes fields
// update-fail fields cause fail state
function setSubmitState(frm) {
    var submitbtn = frm.querySelector('[type="submit"]:not([formnovalidate="formnovalidate"])')
    var updateyes = frm.getElementsByClassName('update-yes');
    var updatefail = frm.getElementsByClassName('update-fail');

    submitbtn.disabled =
        // if some fields have changed values
        (updateyes.length > 0)
            // if no fields are marked as a failure
            && (updatefail.length == 0)
            // if the submit button has not been intentionally disabled by another process
            && !submitbtn.classList.contains('disabled')
            ? false : true;

}

// check passwords for match:
// id1 = password field 1
// id2 = password field 2
// id3 = status message
function pswCheck(id1, id2, idsubmit, alwayson = true) {
    var el1 = document.getElementById(id1);
    var el2 = document.getElementById(id2);
    var elsubmit = document.getElementById(idsubmit);
    var elsubmitOgValue = elsubmit.getAttribute('value');

    // check field for test pass
    var RegPattern = new RegExp(el1.pattern);
    var isValid = RegPattern.test(el1.value);

    // method for modifying the submit button (idsubmit)
    function modBtn(enable, value = elsubmitOgValue) {
        if (enable) {
            //elsubmit.value = elsubmitOgValue;
            elsubmit.innerHTML = elsubmitOgValue;
            elsubmit.disabled = false;
            elsubmit.classList.remove('disabled');
        } else {
            //elsubmit.value = value;
            elsubmit.innerHTML = value;
            elsubmit.disabled = true;
            elsubmit.classList.add('disabled');
        }
    }

    // path for when alwayson bit is false (determine if passwords should be required)
    if (!(alwayson)) {
        if (!(el1.value) && !(el2.value)) {
            el1.removeAttribute('required');
            el2.removeAttribute('required');
        }
        else {
            el1.setAttribute('required', 'required');
            el2.setAttribute('required', 'required');
        }
    }

    // if the active field is empty (the idea is: don't start a prompt with an error output)
    if (!(el1.value)) {
        modBtn(true);
    }
    // if non-empty passwords match, or if other field is blank
    else if (
        ((el1.value) && (el1.value == el2.value)) ||
        !(el2.value)
    ) {
        // current field passes test
        if (isValid) {
            modBtn(true);
        }
        // other field is still empty, or both fields match but don't pass test
        else {
            modBtn(false, (8 - el1.value.length));
        }
    }
    // passwords do not match
    else {
        modBtn(false, 'match?');
    }
}
