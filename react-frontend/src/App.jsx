import { BrowserRouter, Routes, Route, useLocation } from "react-router-dom";
import Prijava from "./pages/Prijava";
import Izlozbe from "./pages/Izlozbe";
import Pocetna from "./pages/Pocetna";
import IzlozbaDetalji from "./pages/IzlozbaDetalji";
import Registracija from "./pages/Registracija";
import Galerija from "./pages/Galerija";
import NavBar from "./components/NavBar";
import PrijavaKorisnika from "./pages/PrijavaKorisnika";
import "./App.css";

function AppContent() {
  const location = useLocation();

  const hideNavbarRoutes = ["/", "/registracija"];
  const shouldHideNavbar = hideNavbarRoutes.includes(location.pathname);

  return (
    <>
      {!shouldHideNavbar && <NavBar />}

      {/* Padding samo kad navbar postoji */}
      {shouldHideNavbar ? (
        <Routes>
          <Route path="/" element={<PrijavaKorisnika />} />
          <Route path="/registracija" element={<Registracija />} />
        </Routes>
      ) : (
        <div style={{ padding: "16px" }}>
          <Routes>
            <Route path="/pocetna" element={<Pocetna />} />
            <Route path="/izlozbe/:id/prijava" element={<Prijava />} />
            <Route path="/izlozbe" element={<Izlozbe />} />
            <Route path="/izlozbe/:id" element={<IzlozbaDetalji />} />
            <Route path="/izlozbe/:id/galerija" element={<Galerija />} />
          </Routes>
        </div>
      )}
    </>
  );
}

function App() {
  return (
    <BrowserRouter>
      <AppContent />
    </BrowserRouter>
  );
}

export default App;