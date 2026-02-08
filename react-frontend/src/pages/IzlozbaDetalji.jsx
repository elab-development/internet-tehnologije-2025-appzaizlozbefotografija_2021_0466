import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import axios from "axios";
import "./IzlozbaDetalji.css";

function IzlozbaDetalji() {
  const { id } = useParams();
  const [izlozba, setIzlozba] = useState(null);
  const [error, setError] = useState("");

  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/izlozbe/${id}`)
      .then((res) => {
        const data = res.data?.data ?? res.data; 
        setIzlozba(data);
      })
      .catch(() => setError("Greška pri učitavanju izložbe"));
  }, [id]);

  if (error) return <div className="izd-state">{error}</div>;
  if (!izlozba) return <div className="izd-state">Učitavanje...</div>;

  return (
    <div className="izd-page">
      <div className="izd-container">
        <div className="izd-card">
          <h1 className="izd-title">{izlozba.naziv}</h1>

          {izlozba.opis ? (
            <p className="izd-desc">{izlozba.opis}</p>
          ) : (
            <p className="izd-desc">Nema opisa.</p>
          )}

          <div className="izd-meta">
            <div className="izd-metaItem">
              <span className="izd-label">Datum</span>
              <div className="izd-value">{izlozba.datum}</div>
            </div>

            <div className="izd-metaItem">
              <span className="izd-label">Lokacija</span>
              <div className="izd-value">{izlozba.lokacija}</div>
            </div>

            <div className="izd-metaItem">
              <span className="izd-label">Dostupna mesta</span>
              <div className="izd-value">{izlozba.dostupna_mesta}</div>
            </div>

          
          </div>

          <div className="izd-actions">
            <Link to="/izlozbe" className="izd-btn izd-btnLink">
              ← Nazad na izložbe
            </Link>

            <Link
              to={`/izlozbe/${izlozba.id}/galerija`}
              className="izd-btn"
            >
              Pogledaj galeriju
            </Link>

            <Link
              to={`/izlozbe/${izlozba.id}/prijava`}
              className="izd-btn izd-btnPrimary"
            >
              Rezerviši mesto
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}

export default IzlozbaDetalji;