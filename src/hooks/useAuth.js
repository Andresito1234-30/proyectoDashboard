// hooks/useAuth.js
// Authentication hook for vanilla JS (localStorage-based session management)

/**
 * Auth manager for handling authentication state
 * Uses localStorage for persistence (vanilla JS alternative to React Context)
 */
const AuthManager = {
  /**
   * Initialize auth state from localStorage
   */
  init() {
    const accessToken = this.getAccessToken();
    return {
      user: this.getCurrentUser(),
      isAuthenticated: !!accessToken,
    };
  },

  /**
   * Login user and store tokens
   * @param {string} email
   * @param {string} password
   */
  async login(email, password) {
    // Import authAPI dynamically to avoid circular dependency
    const { authAPI } = await import('../services/api.js');
    
    try {
      const response = await authAPI.login({ email, password });
      const { access, user } = response.data;

      // Store access token
      localStorage.setItem('accessToken', access);

      // Create and store user data
      const userData = user || { email };
      localStorage.setItem('user', JSON.stringify(userData));

      return { success: true, data: response.data };
    } catch (error) {
      throw error;
    }
  },

  /**
   * Register new user
   * @param {Object} userData - { firstname, lastname, email, password }
   */
  async signup(userData) {
    const { authAPI } = await import('../services/api.js');
    
    try {
      const response = await authAPI.register(userData);
      return { success: true, data: response.data };
    } catch (error) {
      throw error;
    }
  },

  /**
   * Logout user and clear storage
   */
  logout() {
    localStorage.removeItem('accessToken');
    localStorage.removeItem('user');
    window.location.href = '/index.php';
  },

  /**
   * Get current user from localStorage
   */
  getCurrentUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  /**
   * Get access token
   */
  getAccessToken() {
    return localStorage.getItem('accessToken');
  },

  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return !!this.getAccessToken();
  },

  /**
   * Check for session expiration message
   */
  checkSessionExpired() {
    const expired = localStorage.getItem('sessionExpired');
    if (expired) {
      localStorage.removeItem('sessionExpired');
      return true;
    }
    return false;
  },
};

// Export as default for easy import
export default AuthManager;
