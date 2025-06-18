const PhoneCountryControl = {
    input: null,
    countrySelect: null,
    iti: null,

    init() {
        this.input = document.querySelector("#phone");
        this.countrySelect = document.querySelector("#countrySelect");

        this.iti = window.intlTelInput(this.input, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            allowDropdown: false,
            nationalMode: false,
            autoPlaceholder: "polite",
            formatOnDisplay: true,
        });

        this.populateCountries();
        this.setInitialCountry();
        this.bindEvents();

        window.addEventListener("load", () => {
            this.restoreFromStorage();
            this.applyMask();
            setTimeout(() => this.setDialCode(), 60);
        });
    },

    generateMaskFromPlaceholder(placeholder) {
        return placeholder.split('').map(char => (/\d/.test(char) ? '9' : char)).join('');
    },

    applyMask() {
        setTimeout(() => {
            const placeholder = this.input.getAttribute("placeholder");
            if (!placeholder) return;

            const mask = this.generateMaskFromPlaceholder(placeholder);

            if (window.Inputmask) {
                Inputmask.remove(this.input);
            }

            Inputmask({
                mask: mask,
                placeholder: "_",
                showMaskOnHover: false,
                clearIncomplete: false,
                autoUnmask: false
            }).mask(this.input);
        }, 50);
    },

    setDialCode() {
        if (this.input.value === '') {
            const dialCode = this.iti.getSelectedCountryData().dialCode;
            this.input.value = "+" + dialCode;
        }
    },

    populateCountries() {
        const countryData = window.intlTelInputGlobals.getCountryData();
        for (let i = 0; i < countryData.length; i++) {
            const country = countryData[i];
            const optionNode = document.createElement("option");
            optionNode.value = country.iso2;
            optionNode.textContent = country.name + " (+" + country.dialCode + ")";
            this.countrySelect.appendChild(optionNode);
        }
    },

    setInitialCountry() {
        const selectedCountry = this.iti.getSelectedCountryData();
        this.countrySelect.value = selectedCountry.iso2;
    },

    bindEvents() {
        this.countrySelect.addEventListener("change", () => {
            this.iti.setCountry(this.countrySelect.value);
            this.applyMask();
            setTimeout(() => this.setDialCode(), 60);
        });

        this.input.addEventListener("countrychange", () => {
            const currentCountry = this.iti.getSelectedCountryData();
            this.countrySelect.value = currentCountry.iso2;
            this.applyMask();
            setTimeout(() => this.setDialCode(), 60);
        });
    },

    restoreFromStorage() {
        const saved = localStorage.getItem("step1");
        if (saved) {
            const data = JSON.parse(saved);
            if (data.phone && typeof this.iti.setNumber === "function") {
                this.iti.setNumber(data.phone);
            }
        }
    }
};

document.addEventListener("DOMContentLoaded", () => {
    PhoneCountryControl.init();
});