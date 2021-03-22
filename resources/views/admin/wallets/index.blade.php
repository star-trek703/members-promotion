<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Wallets') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="w-full border">
                        <thead class="text-lg border-b">
                            <tr>
                                <th class="px-1 py-3 border">#</th>
                                <th class="px-1 py-3 border">Name</th>
                                <th class="px-1 py-3 border">Amount</th>
                                <th class="px-1 py-3 border">View profile</th>
                            </tr>
                        </thead>
                        <tbody class="text-lg">
                            {{ $wallets->links() }}

                            <div class="my-5"></div>

                            @foreach ($wallets as $key => $wallet)
                                <tr>
                                    <td class="text-center px-1 py-3 border">
                                        {{ ($key + 1) }}
                                    </td>
                                    <td class="px-4 py-3 border">
                                        {{ $wallet->user->name }}
                                    </td>
                                    <td class="px-4 py-3 border">
                                        $ {{ $wallet->amount }}
                                    </td>
                                    <td class="px-4 py-3 border">
                                        <a href="{{ route('profile', $wallet->user->id) }}" class="text-blue-500 underline hover:text-blue-700">
                                            View profile
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
