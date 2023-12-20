import { Alert, Button, Form, Col } from "react-bootstrap";
import { NavLink } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import { useState } from "react";

const LoginForm = () => {
	const [email, setEmail] = useState("");
	const [password, setPassword] = useState("");
	const { userLogin, errorMessage, vError, loading } = useAuth();

	const handleSubmit = async (event) => {

		event.preventDefault();
		try {
			await userLogin(email, password);
		} catch (error) {
			console.log(error);
		}
	};
	return (
		<Col md={8}>
			<div className="mb-4">
				<h3>User Login</h3>
			</div>
			<Form onSubmit={handleSubmit}>
				{errorMessage && <Alert variant="danger">{errorMessage}</Alert>}
				<Form.Group className="mb-3" controlId="formBasicEmail">
					<Form.Label className="text-center">
						Email <span className="text-danger">*</span>
					</Form.Label>
					<Form.Control type="email" placeholder="Enter Email" required min={3} onChange={(event) => setEmail(event.target.value)} />
					{vError && <p className="text-danger pt-1">{vError.email}</p>}
				</Form.Group>
				<Form.Group className="mb-3" controlId="formBasicPassword">
					<Form.Label>
						Password <span className="text-danger">*</span>
					</Form.Label>
					<Form.Control type="password" placeholder="Enter Password" required min={3} onChange={(event) => setPassword(event.target.value)} />
					{vError && <p className="text-danger pt-1">{vError.password}</p>}
				</Form.Group>
				<Button variant="outline-primary" type="submit" >
					Login
				</Button>
			</Form>
			<p className="mt-3">
				{`Don't have an account? `}
				<NavLink to={"/register"} className={"text-primary fw-bold text-decoration-none"}>
					Register
				</NavLink>
			</p>
		</Col>
	);
};

export default LoginForm;
