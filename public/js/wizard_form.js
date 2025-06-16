function showStep(step) {
    document.querySelectorAll(".step").forEach(s => s.classList.add("hidden"));
    const currentStep = document.getElementById("step" + step);
    currentStep.classList.remove("hidden");
    localStorage.setItem("formStep", step);

    if (step === "1" || step === 1 || step === "2" || step === 2) {
        clearErrors(currentStep);
    }

    if (step === "1" || step === 1) {
        // Показываем заголовок, прячем блок ссылок
        document.getElementById('form_title').classList.remove("hidden");
        document.getElementById('all-members-div').classList.add("hidden");

        // Применяем маску и дату
        applyMask();
        birthdate();

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
}

async function handleStep(num, e) {
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
        if (memberId) {
            formData.append("id", memberId);
        }
        onSuccess = (json) => {
            if (json.success === false && json.errors) {
                clearErrors(form);

                for (const [field, message] of Object.entries(json.errors)) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        let errorDiv = null;

                        const itiWrapper = input.closest('.iti');
                        if (itiWrapper) {
                            errorDiv = itiWrapper.nextElementSibling;
                        } else {
                            errorDiv = form.querySelector(`[name="${field}"] ~ .error-message`);
                        }

                        if (errorDiv && errorDiv.classList.contains('error-message')) {
                            errorDiv.textContent = message;
                        }
                    }
                }
            } else if (json.id) {
                localStorage.setItem("member_id", json.id);
                showStep(2);
            } else {
                console.error("Ошибка при сохранении первого шага");
            }
        };
    } else if (num === 2) {
        url = "/register/second";
        additional = () => {
            const memberId = localStorage.getItem("member_id");
            if (!memberId) {
                console.error("ID участника не найден. Повторите первый шаг.");
                showStep(1);
                return false;
            }
            formData.append("id", memberId);
            return true;
        };
        onSuccess = (json) => {
            if (json.success === false && json.errors) {
                clearErrors(form);
                for (const [field, message] of Object.entries(json.errors)) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        const errorDiv = input.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('error-message')) {
                            errorDiv.textContent = message;
                        }
                    }
                }
            } else if (json.success === true && json.count) {
                localStorage.setItem("count_members", json.count);
                showStep(3);
            } else {
                console.error("Ошибка при сохранении второго шага");
            }
        };
    } else {
        return;
    }

    if (!additional()) return;

    try {
        const res = await fetch(url, {
            method: "POST",
            headers: num === 1 ? {
                "Accept": "application/json",

            } : {},
            body: formData
        });

        const json = await res.json();
        onSuccess(json);
    } catch (err) {
        console.error("Ошибка сети при отправке шага " + num);
        console.error(err);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const current = localStorage.getItem("formStep") || "1";
    showStep(current);
    restoreForm(current);
    birthdate();
});


function restoreForm(step) {
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
    if (step === 1) {
        input.value = data.phone || "";

        if (data.phone && iti && typeof iti.setNumber === "function") {
            iti.setNumber(data.phone); // восстановит и страну, и формат
            countrySelect.value = iti.getSelectedCountryData().iso2;
        }

        applyMask();
    }
}

function birthdate() {
    const birthdateInput = document.getElementById('birthdate');
    const today = new Date().toISOString().split('T')[0];
    birthdateInput.setAttribute('max', today);
}

function clearErrors(form) {
    const errorElements = form.querySelectorAll('.error-message');
    errorElements.forEach(el => el.textContent = '');
}

function goToStep1() {
    showStep(1);
    restoreForm(1);
}

function startOver() {
    localStorage.removeItem("formStep");
    localStorage.removeItem("step1");
    localStorage.removeItem("step2");
    localStorage.removeItem("member_id");
    localStorage.removeItem("count_members");

    document.querySelectorAll("form.step").forEach(form => {
        form.reset();
        clearErrors(form);
    });

    showStep(1);
}