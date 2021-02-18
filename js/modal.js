/* simple class to generate and handle a modal dialog */
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

    /* method calls the modal and establishes event handlers to return response */
    confirm() {
        this._showModal();
        //this.trueButton.focus();

        return new Promise((resolve, reject) => {
            /* return rejection if something went wrong */
            const somethingWentWrongUponCreation = !this.formEl || !this.trueButton || !this.falseButton;
            if (somethingWentWrongUponCreation) {reject('failed to create modal'); return; }

            /* trigger this event if true is clicked */
            this.trueButton.addEventListener("click", () => {
                resolve(true);
                this._destroy();
            });

            /* trigger this event if false is clicked */
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

    /* create a button DOM object and return */
    createButton(name, label = null) {
        /* label uses name if null */
        label = label ?? name;
        /* create the button itself */
        var Btn = document.createElement('button');
        /* set attributes */
        Btn.setAttribute('id', 'modal-btn-' + name);
        Btn.setAttribute('form', this.formEl.getAttribute('id'));
        Btn.setAttribute('value', name);
        /* create span label and set to button */
        var Btnspan = document.createElement('span');
        Btnspan.innerText = label;
        Btn.appendChild(Btnspan);
        /* return completed button */
        return Btn;
    }

    /* call this method to enable modal visibility */
    _showModal() {this.container.classList.add('show');}
    /* hide the modal in ui */
    _hideModal() {this.container.classList.remove('show');}
    /* add dialog to end of existing dialog string */
    _appendDialog() {this.modalwindow.appendChild(this.formEl);}

    /* safely remove modal from state entirely */
    _destroy() {
        /* hide the modal */
        this._hideModal();
        /* remove the modal from DOM */
        this.modalwindow.removeChild(this.formEl);
        /* delete instantiated object */
        delete this;
    }
}

/* toggle the modal container */
function modal_toggle(enablebool = true) {
    objects = [document.getElementById('modal-container')];
    toggle(objects, enablebool);
}

/* simple confirm dialog using confirmmodal class
 * original form event will be sent to server upon user confirmation
 */
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

/* clear / replace the content of the modal */
function setModalContent(content) {
    modalDOMcontainer = document.getElementById('modalform-content');
    modalDOMcontainer.innerHTML = '';

    modalDOMcontainer.appendChild(content);
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