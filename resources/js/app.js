import './bootstrap';
import sort from '@alpinejs/sort'
import './form-builder';

Alpine.plugin(sort)

// Form Canvas Alpine.js component
window.formCanvas = function() {
    return {
        refreshPreview() {
            // Force a re-render of the preview by triggering a Livewire refresh
            this.$wire.$refresh();
        },
        updatePreviewElement(event) {
            console.log('Preview element update received:', event.detail);
            // event.detail is an array with one element containing our data
            const data = event.detail[0];
            const elementIndex = data.elementIndex;
            const elementId = data.elementId;
            const html = data.html;
            
            console.log('Extracted data:', { elementIndex, elementId, html });
            
            const previewContainer = document.querySelector('[data-preview-element="' + elementId + '"]');
            if (previewContainer) {
                console.log('Updating preview container:', elementId);
                previewContainer.innerHTML = html;
                // Re-initialize Alpine.js components in the updated content
                Alpine.initTree(previewContainer);
            } else {
                console.log('Preview container not found for element:', elementId);
                console.log('Available containers:', document.querySelectorAll('[data-preview-element]'));
            }
        },
        updateEditElement(event) {
            console.log('Edit element update received:', event.detail);
            // event.detail is an array with one element containing our data
            const data = event.detail[0];
            const elementIndex = data.elementIndex;
            const elementId = data.elementId;
            const html = data.html;
            
            console.log('Extracted edit data:', { elementIndex, elementId, html });
            
            // Find the edit element container by its wire:key pattern
            const editContainer = document.querySelector(`[wire\\:key*="element-${elementId}"]`);
            if (editContainer) {
                console.log('Updating edit container:', elementId);
                // Find the inner content div and update it
                const contentDiv = editContainer.querySelector('.p-4');
                if (contentDiv) {
                    contentDiv.innerHTML = html;
                    // Re-initialize Alpine.js components in the updated content
                    Alpine.initTree(contentDiv);
                }
            } else {
                console.log('Edit container not found for element:', elementId);
                console.log('Available containers:', document.querySelectorAll('[wire\\:key*="element-"]'));
            }
        }
    }
}

document.addEventListener('livewire:init', () => {
    Livewire.on('settings-updated', (event) => {
        if (!event || !event.settings) {
            return;
        }

        const settings = event.settings;

        // Handle app name change
        if (settings.hasOwnProperty('general.app_name')) {
            document.title = settings['general.app_name'];
        }

        // Handle primary color change
        if (settings.hasOwnProperty('appearance.primary_color')) {
            const primaryColor = settings['appearance.primary_color'];
            document.documentElement.style.setProperty('--color-primary-500', primaryColor);
        }

        // Handle accent color change
        if (settings.hasOwnProperty('appearance.accent_color')) {
            const accentColor = settings['appearance.accent_color'];
            document.documentElement.style.setProperty('--color-accent', accentColor);
        }

        // Handle theme change
        if (settings.hasOwnProperty('appearance.theme')) {
            const theme = settings['appearance.theme'];
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        // Handle logo change
        if (settings.hasOwnProperty('appearance.logo')) {
            const logoUrl = settings['appearance.logo'];
            document.querySelectorAll('.app-logo-container').forEach(container => {
                const appName = document.querySelector('.app-logo-name')?.textContent || 'Logo';
                container.innerHTML = ''; // Clear existing content

                if (logoUrl) {
                    const img = document.createElement('img');
                    img.src = logoUrl;
                    img.alt = appName;
                    img.className = 'app-logo-image size-8 object-contain';
                    container.appendChild(img);
                } else {
                    // Re-create the SVG icon if the logo is removed
                    const svgNS = "http://www.w3.org/2000/svg";
                    const svg = document.createElementNS(svgNS, "svg");
                    svg.setAttribute('viewBox', '0 0 55 55');
                    svg.setAttribute('class', 'app-logo-icon size-5 fill-current text-white dark:text-black');

                    const path = document.createElementNS(svgNS, "path");
                    path.setAttribute('d', 'M53.15,22.31a3.8,3.8,0,0,0-3-3.11L38,17.43l-4.1-12.2a3.8,3.8,0,0,0-7.1,0L22.7,17.43,10.58,19.2a3.8,3.8,0,0,0-3,3.11L5.9,34.58,1,41.25a3.8,3.8,0,0,0,3.34,5.92l12.79-2,4.86,9.4a3.8,3.8,0,0,0,6.72,0l4.86-9.4,12.79,2a3.8,3.8,0,0,0,3.34-5.92L48.8,34.58Z');

                    svg.appendChild(path);
                    container.appendChild(svg);
                }
            });
        }

        // Handle favicon change
        if (settings.hasOwnProperty('appearance.favicon')) {
            const faviconUrl = settings['appearance.favicon'];
            const faviconLink = document.getElementById('favicon');
            const appleTouchIconLink = document.getElementById('apple-touch-icon');

            if (faviconLink) {
                faviconLink.href = faviconUrl || '/favicon.png';
            }
            if (appleTouchIconLink) {
                appleTouchIconLink.href = faviconUrl || '/favicon.png';
            }
        }
    });

    const getBreakpoint = (width) => {
        if (width < 640) return 'xs';
        if (width < 768) return 'sm';
        if (width < 1024) return 'md';
        if (width < 1280) return 'lg';
        if (width < 1536) return 'xl';
        return '2xl';
    };

    let lastBreakpoint = getBreakpoint(window.innerWidth);
    if(lastBreakpoint) {
        Livewire.dispatch('breakpoint-updated', { breakpoint: lastBreakpoint });
    }

    window.addEventListener('resize', () => {
        const newBreakpoint = getBreakpoint(window.innerWidth);
        if (newBreakpoint !== lastBreakpoint) {
            lastBreakpoint = newBreakpoint;
            Livewire.dispatch('breakpoint-updated', { breakpoint: newBreakpoint });
        }
    });
});
