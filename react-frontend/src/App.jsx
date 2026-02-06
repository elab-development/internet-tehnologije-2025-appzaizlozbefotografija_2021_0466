import { BrowserRouter, Routes, Route, useLocation } from "react-router-dom";
import NavBar from "./components/NavBar";

import Prijava from "./pages/Prijava";
import Izlozbe from "./pages/Izlozbe";
import Pocetna from "./pages/Pocetna";
import IzlozbaDetalji from "./pages/IzlozbaDetalji";
import Registracija from "./pages/Registracija";
import Galerija from "./pages/Galerija";
import PrijavaKorisnika from "./pages/PrijavaKorisnika";

import "./App.css";

function AppContent() {
  const location = useLocation();

  const hideNavbarRoutes = ["/", "/registracija"];
  const fullWidthRoutes = ["/pocetna"];

  const shouldHideNavbar = hideNavbarRoutes.includes(location.pathname);
  const isFullWidth = fullWidthRoutes.includes(location.pathname);

  return (
    <>
      {!shouldHideNavbar && <NavBar />}

      {shouldHideNavbar ? (
        <Routes>
          <Route path="/" element={<PrijavaKorisnika />} />
          <Route path="/registracija" element={<Registracija />} />
        </Routes>
      ) : isFullWidth ? (
        <Routes>
          <Route path="/pocetna" element={<Pocetna />} />
        </Routes>
      ) : (
        <div className="app-shell">
          <Routes>
            <Route path="/izlozbe" element={<Izlozbe />} />
            <Route path="/izlozbe/:id" element={<IzlozbaDetalji />} />
            <Route path="/izlozbe/:id/prijava" element={<Prijava />} />
            <Route path="/izlozbe/:id/galerija" element={<Galerija />} />
          </Routes>
        </div>
      )}
    </>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <AppContent />
    </BrowserRouter>
  );
}