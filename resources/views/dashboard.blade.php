<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Personal access tokens') }}
        </h2>
    </x-slot>


    <div style="margin-right: 10px;margin-left: 10px;" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200">
                    <p class="mb-8">

                        <x-button-link href="{{ route('token.showForm') }}">
                            Create new token
                        </x-button-link>
                    </p>
                    <br>
                    <p class="mb-8">

                        <x-button-link href="{{ route('image-resize') }}">
                            Resize An Image
                        </x-button-link>
                    </p>
                    @if (count($tokens) > 0)
                        <!-- This example requires Tailwind CSS v2.0+ -->
                        <center>
                            <h1 class="flex flex-col" style="font-size: larger;margin-right: 5%;">
                                All Tokens
                            </h1>
                        </center>
                        <div  class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                        <table style="width: 100%" class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Name
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Last used
                                                    </th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">Delete</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($tokens as $token)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <center>{{ $token->name }}</center>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <center>
                                                                {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : '' }}
                                                            </center>
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <form method="POST"
                                                                action="{{ route('token.delete', ['token' => $token->id]) }}">
                                                                @csrf
                                                                <center><x-button>Delete</x-button></center>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <!-- More people... -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-center">You don't have personal access tokens yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
