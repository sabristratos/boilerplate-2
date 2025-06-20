document.addEventListener('livewire:init', () => {
    Livewire.on('settings-updated', (event) => {
        if (!event || !event.settings) {
            return;
        }

        const settings = event.settings;

        // Handle app name change
        if (settings.hasOwnProperty('general.app_name')) {
            const appName = settings['general.app_name'];
            document.querySelectorAll('.app-logo-name').forEach(el => el.textContent = appName);
            document.querySelectorAll('.app-logo-image').forEach(el => el.alt = appName);
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

        // Handle primary color change
        if (settings.hasOwnProperty('appearance.primary_color')) {
            const primaryColor = settings['appearance.primary_color'];
            document.documentElement.style.setProperty('--color-accent', primaryColor);
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
    });
});
