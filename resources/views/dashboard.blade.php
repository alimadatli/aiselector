<x-app-layout>
    <div class="py-12" x-data="dashboard">
        <!-- Notification -->
        <div x-show="notification.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             :class="notification.type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700'"
             class="fixed top-4 right-4 px-4 py-3 border rounded-lg shadow-lg">
            <div class="flex items-center">
                <div class="py-1">
                    <p x-text="notification.message"></p>
                </div>
            </div>
        </div>

        <!-- Test Result Alert -->
        <div x-show="testResult !== null" 
             x-transition
             class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
             @click="testResult = null">
            <p x-text="testResult?.message"></p>
        </div>

        <!-- Website List -->
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Websites</h2>
                <button @click="showAddWebsite = true" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Add Website
                </button>
            </div>

            <!-- Add Website Form -->
            <div x-show="showAddWebsite" class="mb-6 bg-white p-4 rounded shadow">
                <form @submit.prevent="saveWebsite">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" x-model="newWebsite.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">URL</label>
                            <input type="url" x-model="newWebsite.url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="showAddWebsite = false" 
                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Save Website
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <template x-if="websites.length > 0">
                <div class="grid grid-cols-1 gap-4">
                    <template x-for="website in websites" :key="website.id">
                        <div class="bg-white p-4 rounded shadow">
                            <div class="flex items-center justify-between">
                                <div class="cursor-pointer" @click="selectWebsite(website)">
                                    <h3 class="text-lg font-semibold" x-text="website.name"></h3>
                                    <p class="text-gray-600" x-text="website.url"></p>
                                </div>
                                <button @click="selectWebsite(website)" 
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                    View Selectors
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Selectors Section -->
            <div x-show="currentWebsite" class="mt-8">
                <h3 class="text-xl font-bold mb-4" x-text="'Selectors for ' + currentWebsite?.name"></h3>
                
                <!-- Add New Selector Form -->
                <form @submit.prevent="saveSelector" class="mb-6 bg-white p-4 rounded shadow">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" x-model="newSelector.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" x-model="newSelector.description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Selector</label>
                            <input type="text" x-model="newSelector.selector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Add Selector
                        </button>
                    </div>
                </form>

                <!-- Selectors List -->
                <template x-if="currentSelectors.length > 0">
                    <div class="grid grid-cols-1 gap-4">
                        <template x-for="selector in currentSelectors" :key="selector.id">
                            <div class="bg-white p-4 rounded shadow">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold" x-text="selector.name"></h4>
                                        <p class="text-gray-600" x-text="selector.description"></p>
                                        <p class="text-sm font-mono mt-2" x-text="selector.selector"></p>
                                    </div>
                                    <button @click="toggleSelector(selector)"
                                            :class="selector.is_active ? 'bg-green-500' : 'bg-red-500'"
                                            class="text-white px-3 py-1 rounded hover:opacity-90">
                                        <span x-text="selector.is_active ? 'Active' : 'Inactive'"></span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="currentSelectors.length === 0">
                    <p class="text-gray-600">No selectors found for this website.</p>
                </template>
            </div>

            <template x-if="websites.length === 0">
                <p class="text-gray-600">No websites found.</p>
            </template>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
            websites: [],
            currentWebsite: null,
            currentSelectors: [],
            newSelector: {
                name: '',
                description: '',
                selector: ''
            },
            showAddWebsite: false,
            showSelectors: false,
            showAddSelector: false,
            editingWebsite: false,
            editingSelector: false,
            websiteForm: {
                name: '',
                url: '',
                is_active: true
            },
            selectorForm: {
                name: '',
                selector: '',
                description: '',
                is_active: true
            },
            selectorChanges: [],
            testResult: null,
            showChanges: false,
            newWebsite: {
                name: '',
                url: ''
            },
            notification: {
                show: false,
                type: '',
                message: ''
            },

            async init() {
                await this.fetchWebsites();
            },

            async fetchWebsites() {
                try {
                    const response = await fetch('/api/websites');
                    this.websites = await response.json();
                } catch (error) {
                    console.error('Error fetching websites:', error);
                    this.showNotification('error', 'Failed to fetch websites');
                }
            },

            async saveWebsite() {
                try {
                    const method = this.editingWebsite ? 'PUT' : 'POST';
                    const url = this.editingWebsite 
                        ? `/api/websites/${this.editingWebsite.id}`
                        : '/api/websites';

                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.newWebsite)
                    });

                    if (response.ok) {
                        await this.fetchWebsites();
                        this.showAddWebsite = false;
                        this.resetWebsiteForm();
                        this.showNotification('success', 'Website saved successfully');
                    }
                } catch (error) {
                    console.error('Error saving website:', error);
                    this.showNotification('error', 'Failed to save website');
                }
            },

            editWebsite(website) {
                this.editingWebsite = website;
                this.newWebsite = { ...website };
                this.showAddWebsite = true;
            },

            async deleteWebsite(website) {
                if (!confirm('Are you sure you want to delete this website?')) return;

                try {
                    const response = await fetch(`/api/websites/${website.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        await this.fetchWebsites();
                        this.showNotification('success', 'Website deleted successfully');
                    }
                } catch (error) {
                    console.error('Error deleting website:', error);
                    this.showNotification('error', 'Failed to delete website');
                }
            },

            async showSelectorsModal(website) {
                this.currentWebsite = website;
                try {
                    const response = await fetch(`/api/websites/${website.id}/selectors`);
                    this.currentSelectors = await response.json();
                    this.showSelectors = true;
                } catch (error) {
                    console.error('Error fetching selectors:', error);
                    this.showNotification('error', 'Failed to fetch selectors');
                }
            },

            async saveSelector() {
                try {
                    const method = this.editingSelector ? 'PUT' : 'POST';
                    const url = this.editingSelector 
                        ? `/api/websites/${this.currentWebsite.id}/selectors/${this.editingSelector.id}`
                        : `/api/websites/${this.currentWebsite.id}/selectors`;

                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.newSelector)
                    });

                    if (response.ok) {
                        await this.showSelectorsModal(this.currentWebsite);
                        this.showAddSelector = false;
                        this.resetSelectorForm();
                        this.showNotification('success', 'Selector saved successfully');
                    }
                } catch (error) {
                    console.error('Error saving selector:', error);
                    this.showNotification('error', 'Failed to save selector');
                }
            },

            editSelector(selector) {
                this.editingSelector = selector;
                this.selectorForm = { ...selector };
                this.showAddSelector = true;
            },

            async deleteSelector(selector) {
                if (!confirm('Are you sure you want to delete this selector?')) return;

                try {
                    const response = await fetch(`/api/websites/${this.currentWebsite.id}/selectors/${selector.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        await this.showSelectorsModal(this.currentWebsite);
                        this.showNotification('success', 'Selector deleted successfully');
                    }
                } catch (error) {
                    console.error('Error deleting selector:', error);
                    this.showNotification('error', 'Failed to delete selector');
                }
            },

            async testSelector(selector) {
                try {
                    this.testResult = null; // Reset before testing
                    const response = await fetch(`/api/scraper/validate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            website_id: this.currentWebsite.id,
                            selector_name: selector.name,
                            scraped_data: {},
                            html_content: document.documentElement.outerHTML
                        })
                    });

                    const result = await response.json();
                    this.testResult = result;
                    
                    if (result.status === 'updated') {
                        await this.showSelectorsModal(this.currentWebsite);
                    }

                    // Auto-hide the result after 5 seconds
                    setTimeout(() => {
                        this.testResult = null;
                    }, 5000);
                } catch (error) {
                    console.error('Error testing selector:', error);
                    this.testResult = {
                        status: 'error',
                        message: 'Failed to test selector: ' + error.message
                    };
                }
            },

            async viewChanges(selector) {
                try {
                    const response = await fetch(`/api/websites/${this.currentWebsite.id}/selectors/${selector.id}/changes`);
                    this.selectorChanges = await response.json();
                    this.showChanges = true;
                } catch (error) {
                    console.error('Error fetching selector changes:', error);
                }
            },

            resetWebsiteForm() {
                this.newWebsite = {
                    name: '',
                    url: ''
                };
                this.editingWebsite = false;
            },

            resetSelectorForm() {
                this.newSelector = {
                    name: '',
                    description: '',
                    selector: ''
                };
                this.editingSelector = false;
            },

            selectWebsite(website) {
                this.currentWebsite = website;
                this.showSelectorsModal(website);
            },

            toggleSelector(selector) {
                selector.is_active = !selector.is_active;
                this.saveSelector();
            },

            showNotification(type, message) {
                this.notification = {
                    show: true,
                    type,
                    message
                };

                setTimeout(() => {
                    this.notification.show = false;
                }, 5000);
            }
        }));
    });
</script>
