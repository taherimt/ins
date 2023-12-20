import React, { createContext, useContext, useState } from 'react';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
    const [isAuthenticated, setIsAuthenticated] = useState(false);

    // Logic to check if the user is authenticated
    const checkAuthentication = () => {
        const token = localStorage.getItem('token');
        setIsAuthenticated(token != null);
    };

    return (
        <AuthContext.Provider value={{ isAuthenticated, checkAuthentication }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
