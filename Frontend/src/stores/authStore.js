import { ref } from 'vue';
import apiClient from '@/api/axios';
import { defineStore } from 'pinia';
import { useRouter } from 'vue-router';
import { useGlobalToast } from '@/composables/useGlobalToast';

export const useAuthenticationStore = defineStore('auth', () => {

    // state
    const { showSuccess, showError } = useGlobalToast();
    const showDialog = ref(false);
    const isLoading = ref(false);
    const errors = ref({
        general: '',    // Error umum (misal: server error, koneksi gagal)
        user_name: '',   // Error khusus untuk field user_name
        password: ''    // Error khusus untuk field password
    });

    // Initialize currentUser dengan null, bukan dari localStorage
    const currentUser = ref(null);

    const router = useRouter();

    // Helper
    const clearErrors = () => {
        errors.value = {
            general: '',
            user_name: '',
            password: ''
        };
    };
    const setFieldError = (field, message) => {
        errors.value[field] = message;
    };

    // actions
    const loginStore = async (input) => {
        clearErrors();
        isLoading.value = true;

        try {
            let hasError = false;
            if (!input.password) {
                setFieldError('password', 'Password is required');
                hasError = true;
            }
            if (hasError) {
                isLoading.value = false;
                return;
            }

            const response = await apiClient.post('/auth/login', {
                user_name: input.user_name,
                password: input.password
            });

            const userData = {
                id: response.data.data.id,
                user_name: response.data.data.user_name
            };

            // Set currentUser dengan data user yang benar
            currentUser.value = userData;

            // Simpan user data ke localStorage
            localStorage.setItem('user', JSON.stringify(userData));

            // Simpan token ke localStorage
            localStorage.setItem('token', response.data.data.token);

            showSuccess('Success', 'Login berhasil');
            showDialog.value = false;
            router.push({ name: 'Dashboard' });
        } catch (error) {
            // Error handling sama seperti sebelumnya...
            if (error.response?.status === 422) {
                const backendErrors = error.response.data.errors;
                if (backendErrors) {
                    Object.keys(backendErrors).forEach(field => {
                        if (errors.value.hasOwnProperty(field)) {
                            setFieldError(field, backendErrors[field][0]);
                        }
                    });
                }
            } else if (error.response?.status === 401) {
                const message = error.response?.data?.message || 'Invalid credentials';
                setFieldError('general', message);
            } else {
                const message = error.response?.data?.message || 'Internal server error';
                setFieldError('general', message);
            }
        } finally {
            isLoading.value = false;
        }
    };

    const registerStore = async (input) => {
        clearErrors();
        isLoading.value = true;

        try {
            let hasError = false;
            if (!input.user_name) {
                setFieldError('user_name', 'User Name is required');
                hasError = true;
            }
            if (!input.password) {
                setFieldError('password', 'Password is required');
                hasError = true;
            }
            if (hasError) {
                isLoading.value = false;
                return;
            }

            const response = await apiClient.post('/auth/register', {
                user_name: input.user_name,
                password: input.password
            });

            const userData = {
                id: response.data.data.id,
                user_name: response.data.data.user_name
            };

            // Set currentUser dengan data user yang benar
            currentUser.value = userData;

            // Simpan user data ke localStorage
            localStorage.setItem('user', JSON.stringify(userData));

            // Simpan token ke localStorage
            localStorage.setItem('token', response.data.data.token);

            showDialog.value = false;
            router.push({ name: 'Dashboard' });
        } catch (error) {
            if (error.response?.status === 422) {
                const backendErrors = error.response.data.errors;
                if (backendErrors) {
                    Object.keys(backendErrors).forEach(field => {
                        if (errors.value.hasOwnProperty(field)) {
                            setFieldError(field, backendErrors[field][0]);
                        }
                    });
                }
            } else if (error.response?.status === 401) {
                const message = error.response?.data?.message || 'Invalid credentials';
                setFieldError('general', message);
            } else {
                const message = error.response?.data?.message || 'Internal server error';
                setFieldError('general', message);
            }
        } finally {
            isLoading.value = false;
        }
    }

    const logoutStore = async () => {
        try {
            await apiClient.post('/auth/logout');

            // Bersihkan localStorage dan state
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            currentUser.value = null;

            showSuccess('Success', 'Logout berhasil');
            router.push({ name: 'products' });
        } catch (error) {
            // console.log('Logout error:', error);
            localStorage.removeItem('user');
            localStorage.removeItem('token');
            currentUser.value = null;
            router.push({ name: 'products' });
        }
    };

    const loadUserFromLocalStorage = () => {
        try {
            const userString = localStorage.getItem('user');
            const token = localStorage.getItem('token');

            if (userString && userString !== 'undefined' && userString !== 'null') {
                const userData = JSON.parse(userString);
                currentUser.value = userData;
            } else {
                currentUser.value = null;
            }
        } catch (error) {
            currentUser.value = null;
            console.error("Gagal parse user dari localStorage:", error);
        }
    };

    // actions end

    return {
        // State variables
        showDialog,
        isLoading,
        errors,
        currentUser,

        // Functions
        loginStore,
        registerStore,
        clearErrors,
        setFieldError,
        logoutStore,
        loadUserFromLocalStorage
    };
});