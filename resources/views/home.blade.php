<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 flex flex-col">
            <div class="p-4">
                <h1 class="text-2xl font-bold">LOGO</h1>
            </div>
            <nav class="flex-1">
                <a href="{{ route('home') }}" class="block py-2 px-4 hover:bg-gray-700">Home</a>
                <a href="{{ route('register') }}" class="block py-2 px-4 hover:bg-gray-700">Register a device</a>
                <a href="{{ route('update') }}" class="block py-2 px-4 hover:bg-gray-700">Update a device</a>
                <a href="{{ route('check') }}" class="block py-2 px-4 hover:bg-gray-700">Check a device</a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Device Management System
                    </h1>
                    <div class="flex items-center">
                        <span class="mr-2">username</span>
                        <img src="https://via.placeholder.com/40" alt="User" class="w-10 h-10 rounded-full">
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-2xl font-semibold mb-4">Ministry of Home Affairs</h2>
                            <h3 class="text-xl mb-6">Sri Lanka</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                    Register a device
                                </a>
                                <a href="{{ route('update') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                    Update a device
                                </a>
                                <a href="{{ route('delete') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                    Delete a device
                                </a>
                                <a href="{{ route('check') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                    Check a device
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>