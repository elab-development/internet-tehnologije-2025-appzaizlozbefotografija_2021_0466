import { BrowserRouter, Routes, Route, useLocation } from "react-router-dom";
import NavBar from "./components/NavBar";
import Prijava from "./pages/Prijava";
import Izlozbe from "./pages/Izlozbe";
import Pocetna from "./pages/Pocetna";
import IzlozbaDetalji from "./pages/IzlozbaDetalji";
import Registracija from "./pages/Registracija";
import Galerija from "./pages/Galerija";
import PrijavaKorisnika from "./pages/PrijavaKorisnika";
import ProtectedRoute from "./components/ProtectedRoutes";
import AdminIzlozbe from "./pages/AdminIzlozbe";
import AdminPrijave from "./pages/AdminPrijave";
import FotografFotografije from "./pages/FotografFotografije";
import "./App.css";

function AppContent() {
  const location = useLocation();

  const hideNavbarRoutes = ["/", "/registracija"];
  const shouldHideNavbar = hideNavbarRoutes.includes(location.pathname);

  return (
    <>
      {!shouldHideNavbar && <NavBar />}

      {shouldHideNavbar ? (
        <Routes>
          <Route path="/" element={<PrijavaKorisnika />} />
          <Route path="/registracija" element={<Registracija />} />
        </Routes>
      ) : (
        <div className="with-navbar">
          <Routes>
            <Route path="/pocetna" element={<Pocetna />} />
            <Route path="/izlozbe" element={<Izlozbe />} />
            <Route
              path="/izlozbe/:id"
              element={
                <div className="app-shell">
                  <IzlozbaDetalji />
                </div>
              }
            />
            <Route
              path="/izlozbe/:id/prijava"
              element={
                <div className="app-shell">
                  <Prijava />
                </div>
              }
            />
            <Route
              path="/izlozbe/:id/galerija"
              element={
                <div className="app-shell">
                  <Galerija />
                </div>
              }
            />
            <Route
              path="/admin/izlozbe"
              element={
                <ProtectedRoute allowed={["admin"]}>
                  <AdminIzlozbe />
                </ProtectedRoute>
              }
            />
            <Route
              path="/admin/prijave"
              element={
                <ProtectedRoute allowed={["admin"]}>
                  <AdminPrijave />
                </ProtectedRoute>
              }
            />
              <Route
                path="/fotograf/fotografije"
                element={
                  <ProtectedRoute allowed={["fotograf"]}>
                    <FotografFotografije />
                  </ProtectedRoute>
                }
              />
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