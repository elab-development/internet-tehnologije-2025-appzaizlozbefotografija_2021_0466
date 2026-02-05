import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import axios from "axios";

export default function Galerija() {
  const { id } = useParams();

  const [fotografije, setFotografije] = useState([]);
  const [loading, setLoading] = useState(true);
  const [greska, setGreska] = useState("");

  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/izlozbe/${id}/fotografije`)
      .then((res) => {
        setFotografije(res.data);
      })
      .catch(() => {
        setGreska("Greška pri učitavanju galerije");
      })
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <p>Učitavanje...</p>;
  if (greska) return <p style={{ color: "red" }}>{greska}</p>;

  return (
    <div>
      <Link to={`/izlozbe/${id}`}>← Nazad na izložbu</Link>

      <h2>Galerija</h2>

      {fotografije.length === 0 ? (
        <p>Nema fotografija za ovu izložbu.</p>
      ) : (
        <div
          style={{
            display: "grid",
            gridTemplateColumns: "repeat(3, 1fr)",
            gap: "12px",
            marginTop: "16px",
          }}
        >
          {fotografije.map((f) => (
            <div key={f.id} style={{ border: "1px solid #ddd", padding: "8px" }}>
              <img
                src={
                  f.putanja_slike?.startsWith("http")
                    ? f.putanja_slike
                    : `http://127.0.0.1:8000/${f.putanja_slike}`
                }
                alt={f.naziv}
                style={{ width: "100%", height: "180px", objectFit: "cover" }}
              />
              <p><b>{f.naziv}</b></p>
              {f.opis && <p>{f.opis}</p>}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}