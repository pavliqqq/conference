var input = document.querySelector("#phone");
var countrySelect = document.querySelector("#countrySelect");

var iti = window.intlTelInput(input, {
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    allowDropdown: false,
    nationalMode: false,
    autoPlaceholder: "polite",
    formatOnDisplay: true,
    initialCountry: "us"
});

function generateMaskFromPlaceholder(placeholder) {
    return placeholder.split('').map(char => {
        if (/\d/.test(char)) return '9';
        return char;
    }).join('');
}

function applyMask() {
    setTimeout(() => {
        var placeholder = input.getAttribute("placeholder");
        if (!placeholder) return;

        var mask = generateMaskFromPlaceholder(placeholder);

        if (window.Inputmask) {
            Inputmask.remove(input);
        }

        Inputmask({
            mask: mask,
            placeholder: "_",
            showMaskOnHover: false,
            clearIncomplete: true,
            autoUnmask: false
        }).mask(input);
    }, 50);
}

function setDialCode() {
    var dialCode = iti.getSelectedCountryData().dialCode;
    input.value = "+" + dialCode;
}

var countryData = window.intlTelInputGlobals.getCountryData();
for (var i = 0; i < countryData.length; i++) {
    var country = countryData[i];
    var optionNode = document.createElement("option");
    optionNode.value = country.iso2;
    optionNode.textContent = country.name + " (+" + country.dialCode + ")";
    countrySelect.appendChild(optionNode);
}
countrySelect.value = iti.getSelectedCountryData().iso2;

countrySelect.addEventListener("change", function() {
    iti.setCountry(this.value);
    applyMask();
    setTimeout(setDialCode, 60);
});

input.addEventListener("countrychange", function() {
    var currentCountry = iti.getSelectedCountryData();
    countrySelect.value = currentCountry.iso2;
    applyMask();
    setTimeout(setDialCode, 60);
});

window.addEventListener("load", function() {
    applyMask();
    setTimeout(setDialCode, 60);
});