const WizardForm = {
    currentStep: localStorage.getItem("formStep") || "1",

    init() {
        document.addEventListener("DOMContentLoaded", () => {
            this.showStep(this.currentStep);
            this.restoreForm(this.currentStep);
        });
    },

    showStep(step) {
        this.currentStep = step;
        document.querySelectorAll(".step").forEach(s => s.classList.add("hidden"));
        const currentStepEl = document.getElementById("step" + step);
        currentStepEl.classList.remove("hidden");
        localStorage.setItem("formStep", step);

        if (step === "1" || step === 1 || step === "2" || step === 2) {
            this.clearErrors(currentStepEl);
        }

        if (step === "1" || step === 1) {
            document.getElementById('form_title').classList.remove("hidden");
            document.getElementById('all-members-div').classList.add("hidden");

            this.setBirthdateMax();
            PhoneCountryControl.applyMask();
        }

        if (step === "3" || step === 3) {
            document.getElementById('form_title').classList.add("hidden");
            document.getElementById('all-members-div').classList.remove("hidden");

            const count = localStorage.getItem("count_members");
            const link = document.getElementById("all-members-link");
            if (link && count) {
                link.textContent = `All members (${count})`;
            }
        }
    },

    async handleStep(num, e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        localStorage.setItem("step" + num, JSON.stringify(Object.fromEntries(formData.entries())));

        let url = "";
        let additional = () => true;
        let onSuccess = () => {
        };

        if (num === 1) {
            url = "/register/first";
            const memberId = localStorage.getItem("member_id");
            if (memberId) formData.append("id", memberId);

            onSuccess = (json) => {
                if (json.success === false && json.errors) {
                    this.clearErrors(form);
                    this.showErrors(form, json.errors);
                } else if (json.id) {
                    localStorage.setItem("member_id", json.id);
                    this.showStep(2);
                } else {
                    console.error("Error saving first step");
                }
            };
        } else if (num === 2) {
            url = "/register/second";
            additional = () => {
                const memberId = localStorage.getItem("member_id");
                if (!memberId) {
                    console.error("Member Id not found. Repeat first step.");
                    this.showStep(1);
                    return false;
                }
                formData.append("id", memberId);
                return true;
            };

            onSuccess = (json) => {
                if (json.success === false && json.errors) {
                    this.clearErrors(form);
                    this.showErrors(form, json.errors);
                } else if (json.success === true && json.count) {
                    localStorage.setItem("count_members", json.count);
                    this.showStep(3);
                } else {
                    console.error("Error saving second step");
                }
            };
        } else {
            return;
        }

        if (!additional()) return;

        try {
            const res = await fetch(url, {
                method: "POST",
                headers: {"Accept": "application/json"},
                body: formData
            });
            const json = await res.json();
            onSuccess(json);
        } catch (err) {
            console.error("Network error sending step " + num, err);
        }
    },

    restoreForm(step) {
        const saved = localStorage.getItem("step" + step);
        if (!saved) return;

        const form = document.getElementById("step" + step);
        const data = JSON.parse(saved);

        for (const [key, value] of Object.entries(data)) {
            const field = form.elements.namedItem(key);
            if (field && field.type !== "file") {
                field.value = value;
            }
        }

        if (step === "1" || step === 1) {
            const input = document.querySelector("#phone");
            const countrySelect = document.querySelector("#countrySelect");

            input.value = data.phone || "";

            if (data.phone && typeof PhoneCountryControl.iti?.setNumber === "function") {
                PhoneCountryControl.iti.setNumber(data.phone);
                countrySelect.value = PhoneCountryControl.iti.getSelectedCountryData().iso2;
            }

            PhoneCountryControl.applyMask();
        }
    },

    setBirthdateMax() {
        const birthdateInput = document.getElementById('birthdate');
        if (birthdateInput) {
            const today = new Date().toISOString().split('T')[0];
            birthdateInput.setAttribute('max', today);
        }
    },

    clearErrors(form) {
        const errorElements = form.querySelectorAll('.error-message');
        errorElements.forEach(el => el.textContent = '');
    },

    showErrors(form, errors) {
        for (const [field, message] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                let errorDiv = null;

                const itiWrapper = input.closest('.iti');
                if (itiWrapper) {
                    errorDiv = itiWrapper.nextElementSibling;
                } else {
                    errorDiv = form.querySelector(`[name="${field}"] ~ .error-message`);
                }

                if (errorDiv?.classList.contains('error-message')) {
                    errorDiv.textContent = message;
                }
            }
        }
    },

    goToStep1() {
        this.showStep(1);
        this.restoreForm(1);
    },

    startOver() {
        ["formStep", "step1", "step2", "member_id", "count_members"].forEach(k => localStorage.removeItem(k));

        document.querySelectorAll("form.step").forEach(form => {
            form.reset();
            this.clearErrors(form);
        });

        this.showStep(1);
    },
};

WizardForm.init();