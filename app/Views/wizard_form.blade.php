@extends('layout')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <div id="map" class="w-full h-[450px]"></div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const map = L.map('map').setView([34.101558, -118.342345], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([34.101558, -118.342345]).addTo(map)
                .bindPopup('7060 Hollywood Blvd, Los Angeles, CA')
                .openPopup();
        })
    </script>

    <div class="max-w-2xl mx-auto mt-10 p-6 bg-white shadow rounded">
        <h2 class="text-xl text-center font-semibold mb-6" id="form_title">To participate in the conference, please fill
            out the
            form</h2>

        <form id="step1" class="step" onsubmit="handleStep(1,event)">
            <div class="grid gap-4 mb-4">
                <input name="first_name" type="text" placeholder="First Name" required
                       class="border p-2 w-full rounded"/>
                <input name="last_name" type="text" placeholder="Last Name" required class="border p-2 w-full rounded"/>
                <input name="birthdate" type="date" required class="border p-2 w-full rounded"/>
                <input name="report_subject" type="text" placeholder="Report Subject" required
                       class="border p-2 w-full rounded"/>
                <input name="country" type="text" placeholder="Country" required class="border p-2 w-full rounded"/>
                <input name="phone" type="tel" placeholder="Phone" required class="border p-2 w-full rounded"/>
                <input name="email" type="email" placeholder="Email" required class="border p-2 w-full rounded"/>
            </div>
            <div class="text-right">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded transition-colors duration-200">
                    Next
                </button>
            </div>
        </form>

        <form id="step2" class="step hidden" onsubmit="handleStep(2,event)" enctype="multipart/form-data">
            <div class="grid gap-4 mb-4">
                <input name="company" type="text" placeholder="Company" required class="border p-2 w-full rounded"/>
                <input name="position" type="text" placeholder="Position" required class="border p-2 w-full rounded"/>
                <textarea name="about_me" placeholder="About me" required class="border p-2 w-full rounded"></textarea>
                <input name="photo" type="file" class="border p-2 w-full rounded"/>
            </div>
            <div class="text-right">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded transition-colors duration-200">
                    Next
                </button>
            </div>
        </form>

        <div id="step3" class="step hidden text-right">
            <h3 class="text-lg font-semibold mb-4">Share the event:</h3>
            <div class="flex gap-4 justify-end">
                <a href="https://www.facebook.com/sharer/sharer.php?u=http://localhost:8000/wizard_form"
                   target="_blank"
                   class="bg-blue-600 text-white px-4 py-2 rounded">
                    Share on Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?text=Check%20out%20this%20Meetup%20with%20SoCal%20AngularJS!&url=http://localhost:8000/wizard_form"
                   target="_blank"
                   class="bg-blue-400 text-white px-4 py-2 rounded">
                    Share on Twitter
                </a>
            </div>
        </div>
        <div class="text-right mt-6 hidden" id="all-members-div">
            <a href="/all_members" id="all-members-link" class="text-blue-600 underline">
                All members
            </a>
        </div>
    </div>

    <script>
        function showStep(step) {
            document.querySelectorAll(".step").forEach(s => s.classList.add("hidden"));
            document.getElementById("step" + step).classList.remove("hidden");
            localStorage.setItem("formStep", step);

            if (step === "3") {
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
                onSuccess = (json) => {
                    if (json.id) {
                        localStorage.setItem("member_id", json.id);
                        showStep(2);
                    } else {
                        alert("Ошибка при сохранении первого шага");
                    }
                };
            } else if (num === 2) {
                url = "/register/second";
                additional = () => {
                    const memberId = localStorage.getItem("member_id");
                    if (!memberId) {
                        alert("ID участника не найден. Повторите первый шаг.");
                        showStep(1);
                        return false;
                    }
                    formData.append("id", memberId);
                    return true;
                };
                onSuccess = (json) => {
                    if (json.count) {
                        localStorage.setItem("count_members", json.count);
                        showStep(3);
                    } else {
                        alert("Ошибка при сохранении второго шага");
                    }
                };
            } else {
                return;
            }

            if (!additional()) return;

            try {
                const res = await fetch(url, {
                    method: "POST",
                    headers: num === 1 ? {"Accept": "application/json"} : {},
                    body: formData
                });

                const json = await res.json();
                onSuccess(json);
            } catch (err) {
                alert("Ошибка сети при отправке шага " + num);
                console.error(err);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const current = localStorage.getItem("formStep") || "1";
            showStep(current);
            restoreForm(current);
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
        }
    </script>
@endsection