import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import axios from "axios";

export default function Izlozbe() {
  const [izlozbe, setIzlozbe] = useState([]);
  const [loading, setLoading] = useState(true);
  const [greska, setGreska] = useState("");

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/izlozbe")
      .then((res) => {
        const data = res.data.data ?? res.data;
        setIzlozbe(data);
      })
      .catch(() => {
        setGreska("Greška pri učitavanju izložbi");
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p>Učitavanje...</p>;
  if (greska) return <p style={{ color: "red" }}>{greska}</p>;

  return (
    <div>
      <h2>Izložbe</h2>

      {izlozbe.length === 0 ? (
        <p>Nema izložbi.</p>
      ) : (
        <ul>
          {izlozbe.map((i) => (
            <li key={i.id}>
              <Link to={`/izlozbe/${i.id}`}>
              <b>{i.naziv}</b>
              </Link>
              {" "}– {i.lokacija} ({i.datum})
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}