import { Link, NavLink } from "react-router-dom";
import "./NavBar.css";
import { isAdmin, isFotograf } from "../utils/auth";

export default function NavBar() {
  return (
    <header className="nav">
      <Link to="/" className="nav__brand">Izložbe</Link>

      <nav className="nav__links">
        <NavLink to="/pocetna" end className={({ isActive }) => isActive ? "active" : ""}>
          Početna
        </NavLink>
        <NavLink to="/izlozbe" className={({ isActive }) => isActive ? "active" : ""}>
          Izložbe
        </NavLink>

        {isAdmin() && (
          <>
            <NavLink to="/admin/izlozbe" className={({ isActive }) => (isActive ? "active" : "")}>
              Upravljaj izložbama
            </NavLink>
            <NavLink to="/admin/prijave" className={({ isActive }) => (isActive ? "active" : "")}>
              Upravljaj prijavama
            </NavLink>
          </>
        )}

        {isFotograf() && <NavLink to="/fotograf/fotografije">Dodaj fotografiju</NavLink>}
        
      </nav>
    </header>
  );
}