import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import axios from "axios";

function IzlozbaDetalji() {
  const { id } = useParams();
  const [izlozba, setIzlozba] = useState(null);
  const [error, setError] = useState("");

  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/izlozbe/${id}`)
      .then(res => setIzlozba(res.data))
      .catch(() => setError("Greška pri učitavanju izložbe"));
  }, [id]);

  if (error) return <p style={{ color: "red" }}>{error}</p>;
  if (!izlozba) return <p>Učitavanje...</p>;

  return (
    <div>
      <h1>{izlozba.naziv}</h1>
      <p><b>Opis:</b> {izlozba.opis}</p>
      <p><b>Datum:</b> {izlozba.datum}</p>
      <p><b>Lokacija:</b> {izlozba.lokacija}</p>
      <p><b>Dostupna mesta:</b> {izlozba.dostupna_mesta}</p>

      <Link to="/izlozbe">← Nazad na izložbe</Link>
      <Link to={`/izlozbe/${izlozba.id}/galerija`}> Pogledaj galeriju</Link>
      <Link to={`/izlozbe/${izlozba.id}/prijava`}> Rezerviši mesto</Link>
    </div>
  );
}

export default IzlozbaDetalji;