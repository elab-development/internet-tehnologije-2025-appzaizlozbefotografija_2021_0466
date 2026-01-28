import { useEffect, useState } from "react";
import "./App.css";
 
function App() {
  const [count, setCount] = useState(0);
  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);
 
  // Primer poziva ka backendu (kasnije)
  const testBackend = async () => {
    setLoading(true);
    try {
      const response = await fetch("http://127.0.0.1:8000/api/test");
      const data = await response.json();
      setMessage(data.message);
    } catch (error) {
      setMessage("Greška pri pozivu backenda");
    } finally {
      setLoading(false);
    }
  };
 
  return (
<div className="app">
<h1>React Frontend – Aplikacija za izložbe fotografija</h1>
 
      <section className="section">
<h2>React test</h2>
<button onClick={() => setCount(count + 1)}>
          Kliknuto {count} puta
</button>
</section>
 
      <section className="section">
<h2>Laravel backend test</h2>
<button onClick={testBackend} disabled={loading}>
          {loading ? "Učitavanje..." : "Testiraj backend"}
</button>
 
        {message && <p className="message">{message}</p>}
</section>
</div>
  );
}
 
export default App;