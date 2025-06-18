<form id="step1" class="step space-y-4" onsubmit="WizardForm.handleStep(1,event)">

    <div class="flex flex-col">
        <input name="first_name" type="text" placeholder="First Name" class="border p-2 rounded w-full" />
        <div class="error-message text-red-600 text-sm mt-1"></div>
    </div>

    <div class="flex flex-col">
        <input name="last_name" type="text" placeholder="Last Name" class="border p-2 rounded w-full" />
        <div class="error-message text-red-600 text-sm mt-1"></div>
    </div>

    <div class="flex flex-col">
        <input name="birthdate" type="date" class="border p-2 rounded w-full" id="birthdate" max="" />
        <div class="error-message text-red-600 text-sm mt-1"></div>
    </div>

    <div class="flex flex-col">
        <input name="report_subject" type="text" placeholder="Report Subject" class="border p-2 rounded w-full" />
        <div class="error-message text-red-600 text-sm mt-1"></div>
    </div>

    <div class="flex flex-col">
        <select id="countrySelect" name="country" class="border p-2 rounded w-full">
            <option value="" disabled selected>Select country</option>
        </select>
        <div class="error-message text-red-600 text-sm mt-1"></div>
    </div>

    <div class="flex flex-col">
        <input id="phone" name="phone" type="tel" class="border p-2 rounded w-full" />
        <div class="error-message text-red-600 text-sm mt-1"></div>
    </div>

    <div class="flex flex-col">
        <input name="email" type="text" placeholder="Email" class="border p-2 rounded w-full" />
        <div class="error-message text-red-600 text-sm mt-1"></div>
    </div>

    <div class="text-right pt-2">
        <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded transition-colors duration-200">
            Next
        </button>
    </div>
</form>