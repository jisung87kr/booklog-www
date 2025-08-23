<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
    <!-- Simple logo section -->
    <div class="mb-8 text-center">
        <div class="flex items-center justify-center mb-4">
            {{ $logo }}
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">북로그</h1>
        <p class="text-gray-600 text-sm">책과 함께하는 특별한 순간들</p>
    </div>

    <!-- Clean card design -->
    <div class="w-full sm:max-w-md">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 sm:p-8">
            {{ $slot }}
        </div>
    </div>
</div>
