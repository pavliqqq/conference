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
function applyMask() {
    setTimeout(() => {
        var placeholder = iti.getNumber();
        if (!placeholder) {
            placeholder = input.getAttribute("placeholder");
        }
        if (!placeholder) return;

        var mask = placeholder.replace(/[0-9]/g, "9");

        if (window.Inputmask) {
            Inputmask.remove(input);
        }

        Inputmask({
            mask: mask,
            placeholder: "_",
            showMaskOnHover: false,
            clearIncomplete: true,
            autoUnmask: true
        }).mask(input);
    }, 50);
}

var countryData = window.intlTelInputGlobals.getCountryData();
for (var i = 0; i < countryData.length; i++) {
    var country = countryData[i];
    var optionNode = document.createElement("option");
    optionNode.value = country.iso2;
    optionNode.textContent = country.name + " (+" + country.dialCode + ")";
    countrySelect.appendChild(optionNode);
}

countrySelect.addEventListener("change", function() {
    iti.setCountry(this.value);
    applyMask();
});


input.addEventListener("countrychange", function() {
    var currentCountry = iti.getSelectedCountryData();
    countrySelect.value = currentCountry.iso2;
    input.value = "";
    applyMask();
});


window.addEventListener("load", function() {
    applyMask();
});