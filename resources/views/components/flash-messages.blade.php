{{-- Messages flash --}}
@if(session('success'))
    <div id="flash-success" class="container mx-auto max-w-4xl px-4 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg flex justify-between items-center shadow-sm">
            <span class="font-medium">✅ {{ session('success') }}</span>
            <button onclick="document.getElementById('flash-success').remove()" class="text-green-600 hover:text-green-900 font-bold text-lg cursor-pointer">&times;</button>
        </div>
    </div>
@endif

@if(session('error'))
    <div id="flash-error" class="container mx-auto max-w-4xl px-4 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg flex justify-between items-center shadow-sm">
            <span class="font-medium">❌ {{ session('error') }}</span>
            <button onclick="document.getElementById('flash-error').remove()" class="text-red-600 hover:text-red-900 font-bold text-lg cursor-pointer">&times;</button>
        </div>
    </div>
@endif

<script>
    setTimeout(function() {
        var success = document.getElementById('flash-success');
        var error = document.getElementById('flash-error');
        if (success) success.remove();
        if (error) error.remove();
    }, 3000);
</script>
