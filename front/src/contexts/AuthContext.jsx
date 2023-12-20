import React, { useContext, useEffect, useState } from "react";
import axios from "axios";

// This context API is for user authentication
const AuthContext = React.createContext(null);

export const useAuth = () => {
	return useContext(AuthContext);
};

const AuthProvider = ({ children }) => {
	const [loading, setLoading] = useState(false);
	const [currentUser, setCurrentUser] = useState();
	const [loginStorageData, setLoginStorageData] = useState("");
	const [errorMessage, setErrorMessage] = useState("");
	const [successMessage, setSuccessMessage] = useState("");
	const [vError, setVError] = useState("");

	// Resetting all error & success messages
	const resetError = () => {
		setErrorMessage("");
		setSuccessMessage("");
		setSuccessMessage("");
		setLoading(true);
	};

	// User registration function
	const userRegister = async (name, email, password, password_confirmation, agreement) => {
		const registerData = { name, email, password, password_confirmation, agreement };
		resetError();
		try {
			const response = await axios.post("http://127.0.0.1:8000/api/register", registerData);
			setCurrentUser(response.data);
			localStorage.setItem("userLoginData", JSON.stringify(response.data));
		} catch (error) {
			if (error.response.status === 422) {
				setVError(error.response.data.errors);
			}
		}
		setLoading(false);
	};

	// User login function
	const userLogin = async (email, password) => {
		const loginData = { email, password };
		resetError();

		try {
			const response = await axios.post("http://127.0.0.1:8000/api/login", loginData);

			// Now that response is defined, you can access it
			console.log(response.data);

			if (response.data.status === 200) {
				setCurrentUser(response.data);
				localStorage.setItem("userLoginData", JSON.stringify(response.data));
				console.log(response.data);
			} else {
				setErrorMessage(response.data.errors);
			}
		} catch (error) {
			if (error.response && error.response.status === 422) {
			    setVError(error.response.data.errors);
			}
			console.log(error);
		}

		setLoading(false);
	};


	// User logout function
	const userLogout = async () => {
		// await new Promise((r) => setTimeout(r, 100));
		resetError();
		try {
			const response = await axios.post("http://127.0.0.1:8000/api/logout", null, {
				headers: { Authorization: `Bearer ${loginStorageData.token}` },
			});
			if (response.data.status === 200) {
				setCurrentUser("");
				localStorage.removeItem("userLoginData");
			}
		} catch (error) {
			console.log(error);
		}
		setLoading(false);
	};


	useEffect(() => {
		setLoginStorageData(currentUser || JSON.parse(localStorage.getItem("userLoginData")));
	}, [currentUser]);

	const userData = { currentUser, loginStorageData, userLogin, userRegister, userLogout, vError, errorMessage, successMessage, loading };

	return <AuthContext.Provider value={userData}>{children}</AuthContext.Provider>;
};

export default AuthProvider;
