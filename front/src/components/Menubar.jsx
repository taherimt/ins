import { Navbar, Container, Nav, Form, Button } from "react-bootstrap";
import { NavLink } from "react-router-dom";
import Account from "./Account";
import '../Menubar.css';
function Menubar() {
	return (
		<Navbar bg="dark" expand="lg" className="sticky-top cnn-navbar">
			<Container>
				<Navbar.Brand href="/" className="cnn-navbar-brand">News Letter</Navbar.Brand>
				<Navbar.Toggle aria-controls="navbarScroll" />
				<Navbar.Collapse id="navbarScroll">
					<Nav className="me-auto my-2 my-lg-0" navbarScroll>
						<NavLink to={"/"} className="nav-link text-white">
							Home
						</NavLink>
					</Nav>
					<Account />
				</Navbar.Collapse>
			</Container>
		</Navbar>

	);
}

export default Menubar;
