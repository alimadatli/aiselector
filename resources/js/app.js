import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
        testResult: null,
        websites: [],
        currentWebsite: null,
        currentSelectors: [],
        showAddWebsite: false,
        showSelectors: false,
        newWebsite: {
            name: '',
            url: ''
        },
        newSelector: {
            name: '',
            description: '',
            selector: '',
            is_active: true
        },

        init() {
            this.fetchWebsites();
        },

        async fetchWebsites() {
            try {
                const response = await fetch('/api/websites', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch websites');
                }

                const data = await response.json();
                this.websites = data;
            } catch (error) {
                console.error('Error fetching websites:', error);
                this.showError(error.message);
            }
        },

        async selectWebsite(website) {
            this.currentWebsite = website;
            await this.fetchSelectors(website.id);
        },

        async fetchSelectors(websiteId) {
            try {
                const response = await fetch(`/api/websites/${websiteId}/selectors`);
                this.currentSelectors = await response.json();
            } catch (error) {
                console.error('Error fetching selectors:', error);
                this.testResult = { message: 'Error fetching selectors: ' + error.message };
            }
        },

        async saveWebsite() {
            try {
                const response = await fetch('/api/websites', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.newWebsite)
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'Failed to save website');
                }

                const website = await response.json();
                this.websites.push(website);
                this.newWebsite = { name: '', url: '', is_active: true };
                this.showSuccess('Website saved successfully');
            } catch (error) {
                console.error('Error saving website:', error);
                this.showError(error.message);
            }
        },

        async saveSelector() {
            if (!this.currentWebsite) return;
            
            try {
                const response = await fetch(`/api/websites/${this.currentWebsite.id}/selectors`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newSelector)
                });

                if (!response.ok) throw new Error('Failed to save selector');
                
                const savedSelector = await response.json();
                this.currentSelectors.push(savedSelector);
                this.newSelector = { name: '', description: '', selector: '', is_active: true };
                this.testResult = { message: 'Selector saved successfully' };
            } catch (error) {
                console.error('Error saving selector:', error);
                this.testResult = { message: 'Error saving selector: ' + error.message };
            }
        },

        async toggleSelector(selector) {
            try {
                const response = await fetch(`/api/websites/${this.currentWebsite.id}/selectors/${selector.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ is_active: !selector.is_active })
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'Failed to update selector');
                }
                
                selector.is_active = !selector.is_active;
                this.testResult = { message: 'Selector updated successfully' };
            } catch (error) {
                console.error('Error updating selector:', error);
                this.testResult = { message: 'Error updating selector: ' + error.message };
                // Revert the toggle if the update failed
                selector.is_active = !selector.is_active;
            }
        }
    }));
});

Alpine.start();
