import { Link, NavLink } from "react-router-dom";
import "./NavBar.css";

export default function NavBar() {
  return (
    <header className="nav">
      <Link to="/" className="nav__brand">Izložbe</Link>

      <nav className="nav__links">
        <NavLink to="/" end className={({ isActive }) => isActive ? "active" : ""}>
          Početna
        </NavLink>
        <NavLink to="/izlozbe" className={({ isActive }) => isActive ? "active" : ""}>
          Izložbe
        </NavLink>
        <NavLink to="/registracija" className={({ isActive }) => isActive ? "active" : ""}>
          Registracija
        </NavLink>
      </nav>
    </header>
  );
}