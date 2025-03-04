<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Websites') }}
            </h2>
            <button @click="$dispatch('open-modal', 'add-website')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Website
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div id="websites-table">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="websites-list">
                                <!-- Websites will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Website Modal -->
    <x-modal name="add-website" :show="false" focusable>
        <form id="add-website-form" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('Add New Website') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="website-name" value="{{ __('Name') }}" />
                <x-text-input id="website-name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" id="name-error" />
            </div>

            <div class="mt-6">
                <x-input-label for="website-url" value="{{ __('URL') }}" />
                <x-text-input id="website-url" name="url" type="url" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" id="url-error" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3" type="submit">
                    {{ __('Add Website') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            loadWebsites();

            document.getElementById('add-website-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = {
                    name: document.getElementById('website-name').value,
                    url: document.getElementById('website-url').value
                };

                try {
                    const response = await fetch('/api/websites', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    });

                    if (!response.ok) {
                        throw new Error('Failed to add website');
                    }

                    await loadWebsites();
                    document.getElementById('add-website-form').reset();
                    Alpine.dispatch(document.body, 'close-modal', { name: 'add-website' });
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        async function loadWebsites() {
            try {
                const response = await fetch('/api/websites');
                const data = await response.json();
                
                const websitesList = document.getElementById('websites-list');
                websitesList.innerHTML = data.data.map(website => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${website.name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${website.url}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${website.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${website.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/websites/${website.id}/selectors" class="text-indigo-600 hover:text-indigo-900">Manage Selectors</a>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
    @endpush
</x-app-layout>
