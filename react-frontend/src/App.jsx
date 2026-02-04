import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import Prijava from "./pages/Prijava";
import Izlozbe from "./pages/Izlozbe";
import Pocetna from "./pages/Pocetna";
import IzlozbaDetalji from "./pages/IzlozbaDetalji";
import Registracija from "./pages/Registracija";
import Galerija from "./pages/Galerija";
import NavBar from "./components/NavBar";
import "./App.css";

function App() {
  return (
    <BrowserRouter>
      <NavBar />


      <div style={{ padding: "16px" }}>
        <Routes>
          <Route path="/" element={<Pocetna />} />
          <Route path="/izlozbe/:id/prijava" element={<Prijava />} />
          <Route path="/registracija" element={<Registracija />} />
          <Route path="/izlozbe" element={<Izlozbe />} /> 
          <Route path="/izlozbe/:id" element={<IzlozbaDetalji />} /> 
          <Route path="/izlozbe/:id/galerija" element={<Galerija />} />
        </Routes>
      </div>
    </BrowserRouter>
  );
}

export default App;