import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "./Izlozbe.css";

export default function Izlozbe() {
  const [izlozbe, setIzlozbe] = useState([]);
  const [loading, setLoading] = useState(true);
  const [greska, setGreska] = useState("");

  
  const [qNaziv, setQNaziv] = useState("");
  const [qLokacija, setQLokacija] = useState("");
  const [datumOd, setDatumOd] = useState(""); 
  const [datumDo, setDatumDo] = useState(""); 

  const navigate = useNavigate();

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/izlozbe")
      .then((res) => {
        const data = res.data.data ?? res.data;
        setIzlozbe(Array.isArray(data) ? data : []);
      })
      .catch(() => setGreska("Greška pri učitavanju izložbi"))
      .finally(() => setLoading(false));
  }, []);

  const normalize = (s) => (s ?? "").toString().trim().toLowerCase();

  
  const toYmd = (value) => {
    if (!value) return "";
    const str = value.toString();
    return str.length >= 10 ? str.slice(0, 10) : str;
  };

  const filtrirane = useMemo(() => {
    const nNaziv = normalize(qNaziv);
    const nLok = normalize(qLokacija);

    return izlozbe.filter((i) => {
      const naziv = normalize(i.naziv);
      const lok = normalize(i.lokacija);

      if (nNaziv && !naziv.includes(nNaziv)) return false;
      if (nLok && !lok.includes(nLok)) return false;

      const d = toYmd(i.datum);
      if (datumOd && d && d < datumOd) return false;
      if (datumDo && d && d > datumDo) return false;

      return true;
    });
  }, [izlozbe, qNaziv, qLokacija, datumOd, datumDo]);

  const resetFiltera = () => {
    setQNaziv("");
    setQLokacija("");
    setDatumOd("");
    setDatumDo("");
  };

  if (loading) return <p className="izlozbe-loading">Učitavanje...</p>;
  if (greska) return <p className="izlozbe-error">{greska}</p>;

  const fallbackThumb =
    "https://images.unsplash.com/photo-1452802447250-470a88ac82bc?auto=format&fit=crop&w=800&q=80";

  return (
    <div className="izlozbe-page">
      <div className="izlozbe-header">
        <h2>Izložbe</h2>
        
      </div>
      <div className="izlozbe-filters">
        <div className="izlozbe-filters__grid">
          <div className="izlozbe-field">
            <label>Naziv</label>
            <input
              value={qNaziv}
              onChange={(e) => setQNaziv(e.target.value)}
              placeholder="Npr. izložba, priroda..."
            />
          </div>

          <div className="izlozbe-field">
            <label>Lokacija</label>
            <input
              value={qLokacija}
              onChange={(e) => setQLokacija(e.target.value)}
              placeholder="Npr. Novi Sad..."
            />
          </div>

          <div className="izlozbe-field">
            <label>Datum od</label>
            <input
              type="date"
              value={datumOd}
              onChange={(e) => setDatumOd(e.target.value)}
            />
          </div>

          <div className="izlozbe-field">
            <label>Datum do</label>
            <input
              type="date"
              value={datumDo}
              onChange={(e) => setDatumDo(e.target.value)}
            />
          </div>
        </div>

        <div className="izlozbe-filters__actions">
          <button className="izlozbe-btn izlozbe-btn--ghost" onClick={resetFiltera} type="button">
            Resetuj
          </button>
          <div className="izlozbe-count">
            Prikazano: <b>{filtrirane.length}</b> / {izlozbe.length}
          </div>
        </div>
      </div>

      {filtrirane.length === 0 ? (
        <p className="izlozbe-empty">Nema izložbi za izabrane filtere.</p>
      ) : (
        <div className="izlozbe-grid">
          {filtrirane.map((i) => {
            const thumbUrl = i.slika || i.thumbnail || fallbackThumb;
            const datumText = toYmd(i.datum);

            return (
              <div className="izlozbe-card" key={i.id}>
                <div className="izlozbe-thumbWrap">
                  <img
                    className="izlozbe-thumb"
                    src={thumbUrl}
                    alt={`Izložba ${i.naziv}`}
                    loading="lazy"
                  />
                  <div className="izlozbe-thumbOverlay">
                    <button
                      className="izlozbe-detaljiBtn"
                      onClick={() => navigate(`/izlozbe/${i.id}`)}
                      type="button"
                    >
                      Detalji
                    </button>
                  </div>
                </div>

                <div className="izlozbe-info">
                  <div className="izlozbe-naziv">{i.naziv}</div>

                  <div className="izlozbe-meta">
                    <span>{i.lokacija}</span>
                    <span className="izlozbe-dot">•</span>
                    <span>{datumText}</span>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}