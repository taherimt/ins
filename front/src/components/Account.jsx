import { Nav } from "react-bootstrap";
import { NavLink } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import { PersonCircle, BoxArrowRight } from "react-bootstrap-icons";
import Loading from "./Loading";

const Account = () => {
	const { loginStorageData, userLogout, loading } = useAuth();
	return (
		<>
			{loading && <Loading />}
			{loginStorageData ? (
				<>
					<Nav className="ms-auto my-2 my-lg-0" navbarScroll>
						<NavLink to={"/dashboard"} className={"nav-link"}>
							<PersonCircle size={22} className="pb-1" /> {loginStorageData.user.name}
						</NavLink>
					</Nav>
					<Nav className="my-2 my-lg-0" navbarScroll>
						<NavLink className="nav-link text-white" onClick={userLogout}>
							<BoxArrowRight className="pb-1" size={22} />
						</NavLink>
					</Nav>
				</>
			) : (
				<>
					<Nav className="ms-auto my-2 my-lg-0" navbarScroll>
						<NavLink to={"/login"} className="nav-link text-white">
							Login
						</NavLink>
						<NavLink to={"/register"} className="nav-link text-white">
							Register
						</NavLink>
					</Nav>
				</>
			)}
		</>
	);
};

export default Account;
