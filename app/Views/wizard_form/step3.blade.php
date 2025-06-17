<div id="step3" class="step hidden text-right">
    <h3 class="text-lg font-semibold mb-4">Share the event:</h3>
    <div class="flex gap-4 justify-end mb-4">
        <a href="{{ $facebookUrl }}"
           target="_blank"
           class="bg-blue-600 text-white px-4 py-2 rounded">
            Share on Facebook
        </a>
        <a href="{{ $twitterUrl }}"
           target="_blank"
           class="bg-blue-400 text-white px-4 py-2 rounded">
            Share on Twitter
        </a>
    </div>
</div>

<div class="flex justify-between items-center mt-6 hidden" id="all-members-div">
    <button type="button" onclick="startOver()"
            class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded transition-colors duration-200">
        Start over
    </button>
    <a href="/all_members" id="all-members-link" class="text-blue-600 underline">
        All members
    </a>
</div>