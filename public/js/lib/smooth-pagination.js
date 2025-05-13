/**
 * SmoothPagination.js
 * A lightweight JavaScript library for implementing smooth, AJAX-based pagination
 * that preserves scroll position and provides a seamless user experience.
 */

class SmoothPagination {    constructor(options = {}) {
        this.options = {
            contentSelector: options.contentSelector || '.content-container',
            paginationSelector: options.paginationSelector || '.pagination',
            loadingIndicator: options.loadingIndicator !== undefined ? options.loadingIndicator : false,
            loadingClass: options.loadingClass || 'sp-loading',
            animationSpeed: options.animationSpeed || 300,
            onBeforeLoad: options.onBeforeLoad || null,
            onAfterLoad: options.onAfterLoad || null,
            updateUrl: options.updateUrl !== undefined ? options.updateUrl : true,
            scrollToTop: options.scrollToTop !== undefined ? options.scrollToTop : false
        };

        // Internal properties
        this.currentScrollPosition = 0;
        this.isLoading = false;
        this.containerHeight = 0;
        this.containerOffset = 0;

        // Initialize the pagination
        this.init();
    }

    init() {
        // Add event listeners to pagination links
        document.addEventListener('click', (event) => {
            if (event.target.matches(`${this.options.paginationSelector} a`) || 
                event.target.closest(`${this.options.paginationSelector} a`)) {
                
                const link = event.target.matches(`${this.options.paginationSelector} a`) ? 
                    event.target : 
                    event.target.closest(`${this.options.paginationSelector} a`);
                    
                event.preventDefault();
                this.loadPage(link.href);
            }
        });

        // Store initial container dimensions
        const contentContainer = document.querySelector(this.options.contentSelector);
        if (contentContainer) {
            this.containerHeight = contentContainer.offsetHeight;
            this.containerOffset = this.getOffset(contentContainer).top;
        }

        // Create loading indicator if enabled
        if (this.options.loadingIndicator) {
            this.createLoadingIndicator();
        }
    }    loadPage(url) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.currentScrollPosition = window.scrollY;
        
        // Show loading indicator only if enabled
        if (this.options.loadingIndicator) {
            this.showLoadingIndicator();
        }

        // Call onBeforeLoad callback if defined
        if (typeof this.options.onBeforeLoad === 'function') {
            this.options.onBeforeLoad();
        }

        // Fetch content via AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            this.updateContent(html, url);
        })
        .catch(error => {
            console.error('Error loading page:', error);
            this.isLoading = false;
            this.hideLoadingIndicator();
        });
    }

    updateContent(html, url) {
        // Create temporary div to parse the HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Extract content and pagination elements
        const newContent = tempDiv.querySelector(this.options.contentSelector);
        const newPagination = tempDiv.querySelector(this.options.paginationSelector);
        
        // Update the content area
        if (newContent) {
            const contentContainer = document.querySelector(this.options.contentSelector);
            if (contentContainer) {
                contentContainer.innerHTML = newContent.innerHTML;
            }
        }
        
        // Update pagination
        if (newPagination) {
            const paginationContainer = document.querySelector(this.options.paginationSelector);
            if (paginationContainer) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }
        }        // Update URL in history if option is enabled
        if (this.options.updateUrl) {
            history.pushState({}, '', url);
        }

        // Hide loading indicator if it was shown
        if (this.options.loadingIndicator) {
            this.hideLoadingIndicator();
        }
        
        // Maintain scroll position or scroll to top based on options
        if (!this.options.scrollToTop) {
            window.scrollTo(0, this.currentScrollPosition);
        } else {
            const contentContainer = document.querySelector(this.options.contentSelector);
            if (contentContainer) {
                window.scrollTo({
                    top: this.getOffset(contentContainer).top,
                    behavior: 'smooth'
                });
            }
        }

        // Call onAfterLoad callback if defined
        if (typeof this.options.onAfterLoad === 'function') {
            this.options.onAfterLoad();
        }

        this.isLoading = false;
    }

    createLoadingIndicator() {
        if (!document.querySelector('.sp-loading-indicator')) {
            const indicator = document.createElement('div');
            indicator.className = 'sp-loading-indicator';
            indicator.innerHTML = `
                <div class="sp-loading-spinner">
                    <div class="sp-spinner-circle"></div>
                </div>
            `;
            document.body.appendChild(indicator);
            
            // Add CSS for the loading indicator
            const style = document.createElement('style');
            style.textContent = `
                .sp-loading-indicator {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 9999;
                    background-color: rgba(255, 255, 255, 0.8);
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    display: none;
                }
                .sp-loading-spinner {
                    width: 40px;
                    height: 40px;
                }
                .sp-spinner-circle {
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    border: 4px solid rgba(0, 0, 0, 0.1);
                    border-top-color: var(--primary-color, #007bff);
                    animation: sp-rotate 1s linear infinite;
                }
                @keyframes sp-rotate {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                .${this.options.loadingClass} {
                    opacity: 0.6;
                    pointer-events: none;
                    transition: opacity 0.3s ease;
                }
            `;
            document.head.appendChild(style);
        }
    }

    showLoadingIndicator() {
        const contentContainer = document.querySelector(this.options.contentSelector);
        if (contentContainer) {
            contentContainer.classList.add(this.options.loadingClass);
        }
        
        const indicator = document.querySelector('.sp-loading-indicator');
        if (indicator) {
            indicator.style.display = 'block';
        }
    }

    hideLoadingIndicator() {
        const contentContainer = document.querySelector(this.options.contentSelector);
        if (contentContainer) {
            contentContainer.classList.remove(this.options.loadingClass);
        }
        
        const indicator = document.querySelector('.sp-loading-indicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    getOffset(element) {
        const rect = element.getBoundingClientRect();
        return {
            top: rect.top + window.scrollY,
            left: rect.left + window.scrollX
        };
    }

    // Public method to reload current page
    refresh() {
        const currentUrl = window.location.href;
        this.loadPage(currentUrl);
    }
}

// Make available globally
window.SmoothPagination = SmoothPagination;
