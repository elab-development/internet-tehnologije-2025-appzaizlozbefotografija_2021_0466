import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import Izlozbe from "./pages/Izlozbe";
import "./App.css";

function App() {
  return (
    <BrowserRouter>
      <nav style={{ padding: "10px", borderBottom: "1px solid #ccc" }}>
        <Link to="/">Izložbe</Link>
      </nav>

      <div style={{ padding: "16px" }}>
        <Routes>
          <Route path="/" element={<Izlozbe />} />
        </Routes>
      </div>
    </BrowserRouter>
  );
}

export default App;