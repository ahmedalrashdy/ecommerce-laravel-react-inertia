import axios from 'axios';

const api = axios.create({
    baseURL: '/',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
    withCredentials: true,
});

// Add CSRF token to requests
api.interceptors.request.use((config) => {
    // Try to get token from meta tag first
    const metaToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (metaToken) {
        config.headers['X-CSRF-TOKEN'] = metaToken;
    } else {
        // Fallback to XSRF-TOKEN cookie (Laravel default)
        const cookieToken = document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];

        if (cookieToken) {
            config.headers['X-XSRF-TOKEN'] = decodeURIComponent(cookieToken);
        }
    }

    return config;
});

// Handle response errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 422) {
            // Validation errors
            return Promise.reject(error);
        }

        return Promise.reject(error);
    },
);

export default api;
